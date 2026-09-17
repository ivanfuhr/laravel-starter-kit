#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${DEPLOY_PATH:-/opt/app}"
REPO_URL="${REPO_URL:?REPO_URL is required}"
BRANCH="${DEPLOY_BRANCH:-main}"
COMPOSE_FILE="${COMPOSE_FILE:-compose.prod.yaml}"

upsert_env() {
  local key="$1"
  local value="$2"
  local tmp
  tmp="$(mktemp)"
  if [ -f .env ] && grep -q "^${key}=" .env; then
    grep -v "^${key}=" .env > "${tmp}" || true
    mv "${tmp}" .env
  else
    rm -f "${tmp}"
  fi
  printf '%s=%s\n' "${key}" "${value}" >> .env
}

env_value() {
  local key="$1"
  if [ -f .env ] && grep -q "^${key}=" .env; then
    sed -n "s/^${key}=//p" .env | head -n1
  fi
}

cli() {
  docker compose -f "${COMPOSE_FILE}" --profile cli run --rm --no-deps php-cli "$@"
}

git_cmd() {
  if [ -n "${GH_TOKEN:-}" ]; then
    git -c "url.https://x-access-token:${GH_TOKEN}@github.com/.insteadOf=https://github.com/" "$@"
  else
    git "$@"
  fi
}

echo "==> Deploy directory: ${APP_DIR}"

if [ ! -d "${APP_DIR}/.git" ]; then
  echo "==> Cloning repository..."
  rm -rf "${APP_DIR}"
  mkdir -p "$(dirname "${APP_DIR}")"
  git_cmd clone --branch "${BRANCH}" "${REPO_URL}" "${APP_DIR}"
fi

cd "${APP_DIR}"

echo "==> Fetching latest code (${BRANCH})..."
git_cmd fetch --prune origin
git checkout "${BRANCH}"
git reset --hard "origin/${BRANCH}"

mkdir -p secrets
chmod 700 secrets

if [ ! -f .env ]; then
  cp .env.example .env
fi

# Strip broken JSON auth lines and nested Compose interpolations that break parsing
grep -v '^COMPOSER_AUTH=' .env | grep -v '^MAIL_FROM_NAME=' > .env.tmp || true
mv .env.tmp .env

EXISTING_APP_KEY="$(env_value APP_KEY)"
EXISTING_DB_PASSWORD="$(env_value DB_PASSWORD)"

if [ -z "${EXISTING_APP_KEY}" ] || [ "${EXISTING_APP_KEY}" = "base64:" ]; then
  EXISTING_APP_KEY="${APP_KEY:-base64:$(head -c 32 /dev/urandom | base64 | tr -d '\n')}"
fi

if [ -z "${EXISTING_DB_PASSWORD}" ]; then
  EXISTING_DB_PASSWORD="${DB_PASSWORD:-$(head -c 24 /dev/urandom | base64 | tr -d '\n/=+')}"
fi

upsert_env "APP_NAME" "${APP_NAME:-Laravel}"
upsert_env "APP_ENV" "production"
upsert_env "APP_DEBUG" "false"
upsert_env "APP_URL" "${APP_URL:?APP_URL is required}"
upsert_env "APP_KEY" "${EXISTING_APP_KEY}"
upsert_env "MAIL_FROM_NAME" "${APP_NAME:-Laravel}"

upsert_env "DB_CONNECTION" "pgsql"
upsert_env "DB_HOST" "postgres"
upsert_env "DB_PORT" "5432"
upsert_env "DB_DATABASE" "${DB_DATABASE:-laravel}"
upsert_env "DB_USERNAME" "${DB_USERNAME:-laravel}"
upsert_env "DB_PASSWORD" "${EXISTING_DB_PASSWORD}"

upsert_env "SESSION_DRIVER" "redis"
upsert_env "QUEUE_CONNECTION" "redis"
upsert_env "CACHE_STORE" "redis"
upsert_env "REDIS_HOST" "redis"
upsert_env "REDIS_CLIENT" "phpredis"
upsert_env "LOG_CHANNEL" "stderr"
upsert_env "LOG_LEVEL" "error"

# Cloudflare R2 (S3-compatible) for media files — production only
upsert_env "FILESYSTEM_MEDIA_DISK" "s3"
upsert_env "AWS_DEFAULT_REGION" "${AWS_DEFAULT_REGION:-auto}"
upsert_env "AWS_USE_PATH_STYLE_ENDPOINT" "${AWS_USE_PATH_STYLE_ENDPOINT:-false}"

if [ -n "${AWS_ACCESS_KEY_ID:-}" ]; then
  upsert_env "AWS_ACCESS_KEY_ID" "${AWS_ACCESS_KEY_ID}"
fi

if [ -n "${AWS_SECRET_ACCESS_KEY:-}" ]; then
  upsert_env "AWS_SECRET_ACCESS_KEY" "${AWS_SECRET_ACCESS_KEY}"
fi

if [ -n "${AWS_BUCKET:-}" ]; then
  upsert_env "AWS_BUCKET" "${AWS_BUCKET}"
fi

if [ -n "${AWS_ENDPOINT:-}" ]; then
  upsert_env "AWS_ENDPOINT" "${AWS_ENDPOINT}"
fi

if [ -n "${AWS_URL:-}" ]; then
  upsert_env "AWS_URL" "${AWS_URL}"
fi

if [ -n "${CLOUDFLARE_TUNNEL_TOKEN:-}" ]; then
  upsert_env "CLOUDFLARE_TUNNEL_TOKEN" "${CLOUDFLARE_TUNNEL_TOKEN}"
fi

if [ -n "${COMPOSER_AUTH:-}" ]; then
  printf '%s' "${COMPOSER_AUTH}" > secrets/composer_auth
  chmod 600 secrets/composer_auth
fi

if [ ! -f secrets/composer_auth ] || [ ! -s secrets/composer_auth ]; then
  echo "ERROR: secrets/composer_auth is missing (COMPOSER_AUTH required for Flux Pro)" >&2
  exit 1
fi

if ! grep -q '^CLOUDFLARE_TUNNEL_TOKEN=.\+' .env; then
  echo "ERROR: CLOUDFLARE_TUNNEL_TOKEN is not set in .env" >&2
  exit 1
fi

if ! grep -q '^AWS_ACCESS_KEY_ID=.\+' .env \
  || ! grep -q '^AWS_SECRET_ACCESS_KEY=.\+' .env \
  || ! grep -q '^AWS_BUCKET=.\+' .env \
  || ! grep -q '^AWS_ENDPOINT=.\+' .env; then
  echo "ERROR: Cloudflare R2 (AWS_*) credentials are not set in .env" >&2
  exit 1
fi

echo "==> .env keys:"
cut -d= -f1 .env | grep -v '^#' | grep -v '^$' | sort

echo "==> Building and starting services..."
docker compose -f "${COMPOSE_FILE}" build
docker compose -f "${COMPOSE_FILE}" up -d --remove-orphans

echo "==> Waiting for postgres and php-fpm..."
healthy=0
for _ in $(seq 1 60); do
  if docker compose -f "${COMPOSE_FILE}" ps php-fpm 2>/dev/null | grep -q "(healthy)"; then
    healthy=1
    break
  fi
  if docker compose -f "${COMPOSE_FILE}" ps php-fpm 2>/dev/null | grep -qE "Exit|exited"; then
    echo "ERROR: php-fpm exited" >&2
    docker compose -f "${COMPOSE_FILE}" logs php-fpm --no-color --tail=200 >&2
    exit 1
  fi
  sleep 2
done

if [ "${healthy}" -ne 1 ]; then
  echo "ERROR: php-fpm did not become healthy" >&2
  docker compose -f "${COMPOSE_FILE}" ps >&2
  docker compose -f "${COMPOSE_FILE}" logs php-fpm --no-color --tail=200 >&2
  exit 1
fi

echo "==> Running migrations and optimize via php-cli..."
cli php artisan config:clear
cli php artisan migrate --force
cli php artisan optimize
cli php artisan storage:link || true
cli php artisan reload || true

echo "==> Service status:"
docker compose -f "${COMPOSE_FILE}" ps

echo "==> Health check..."
if ! docker compose -f "${COMPOSE_FILE}" exec -T web wget -qO- http://127.0.0.1/up >/dev/null; then
  echo "ERROR: /up health check failed" >&2
  echo "==> /up body:" >&2
  docker compose -f "${COMPOSE_FILE}" exec -T web wget -qO- http://127.0.0.1/up 2>&1 | head -c 2000 >&2 || true
  echo "==> php-fpm logs:" >&2
  docker compose -f "${COMPOSE_FILE}" logs php-fpm --no-color --tail=100 >&2
  echo "==> artisan about:" >&2
  cli php artisan about >&2 || true
  exit 1
fi

echo "OK: /up returned successfully"
echo "==> Deploy finished."

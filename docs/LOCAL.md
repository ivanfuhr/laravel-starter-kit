# Local development

Create an app from this starter kit with the Laravel installer:

```bash
export COMPOSER_AUTH='...' # Flux Pro JSON for composer.fluxui.dev
laravel new my-app --using=ivanfuhr/laravel-starter-kit --bun
```

This app also runs locally with Docker Compose (`compose.dev.yaml`), not Laravel Sail.

## Prerequisites

- Docker Engine + Compose plugin
- Flux Pro auth file: `secrets/composer_auth` (gitignored)

```bash
mkdir -p secrets
printf '%s' "$COMPOSER_AUTH" > secrets/composer_auth
```

`COMPOSER_AUTH` is the same JSON Composer uses for `composer.fluxui.dev`. `laravel new` / `composer create-project` copy it automatically when the env var is set.

## Quick start

```bash
cp .env.example .env
# Set Docker-oriented DB/Redis (see below), then:
php artisan key:generate

./bin/dev up -d
./bin/dev artisan migrate
./bin/dev bun install
./bin/dev bun run dev
```

App: [http://localhost:8080](http://localhost:8080) (override with `NGINX_PORT`). Without Docker, `APP_URL` defaults to `http://localhost:8000`.

## `.env` for Docker

| Key | Value |
|-----|--------|
| `APP_URL` | `http://localhost:8080` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `postgres` |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `laravel` (or match compose) |
| `DB_USERNAME` | `laravel` |
| `DB_PASSWORD` | `secret` (default in `compose.dev.yaml`) |
| `REDIS_HOST` | `redis` |
| `CACHE_STORE` / `SESSION_DRIVER` / `QUEUE_CONNECTION` | `redis` (recommended) |

## `./bin/dev` (Sail-style)

| Command | Purpose |
|---------|---------|
| `./bin/dev up -d` | Start the stack |
| `./bin/dev stop` | Stop the stack |
| `./bin/dev artisan …` | Artisan in `workspace` |
| `./bin/dev composer …` | Composer in `workspace` |
| `./bin/dev bun …` | Bun in `workspace` |
| `./bin/dev test` | Pest/Artisan tests |
| `./bin/dev shell` | Bash in `workspace` |
| `./bin/dev psql` | PostgreSQL CLI |
| `./bin/dev redis` | Redis CLI |
| `./bin/dev open` | Print the app URL |

Run `./bin/dev` with no args for the full list.

Services: `web` (nginx), `php-fpm`, `workspace`, `postgres`, `redis`.

## AI agents

Local Docker instructions for agents live in `.ai/guidelines/docker-local/` and are composed into `AGENTS.md` via [Laravel Boost custom guidelines](https://laravel.com/docs/boost#adding-custom-ai-guidelines).

Production deploy: `docs/DEPLOY.md`.

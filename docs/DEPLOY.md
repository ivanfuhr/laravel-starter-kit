# Deploy & Docker

Production hostname is whatever you set in `APP_URL` and your Cloudflare Tunnel. The tunnel origin must target `http://web:80` on the Docker network.

Stack follows the [Docker Laravel Compose guide](https://docs.docker.com/guides/laravel/) and Laravel [deployment](https://laravel.com/framework/docs/deployment) practices.

## Architecture

```
Internet → Cloudflare (TLS) → cloudflared → web (nginx)
                                         → php-fpm
                                         → queue
                                         → scheduler
                                         → postgres / redis
```

No host ports are published.

`queue` runs `queue:work --queue=default --tries=3 --timeout=90`. `scheduler` runs `schedule:work` (dispara `queue:prune-batches`).

`php-cli` (Compose profile `cli`) runs Artisan one-shots from the deploy pipeline: migrate, optimize, storage:link, reload.

## Layout

| Path | Purpose |
|------|---------|
| `compose.prod.yaml` | Production stack |
| `compose.dev.yaml` | Local development |
| `docker/` | PHP-FPM, nginx, entrypoints |
| `scripts/deploy.sh` | Pull, build, up, php-cli migrate/optimize |
| `secrets/composer_auth` | Flux Pro Composer auth (gitignored) |

## Environment

Keys live in `.env.example`. Production essentials: `APP_KEY`, `APP_URL`, `DB_*` (pgsql/`postgres`), redis drivers, `CLOUDFLARE_TUNNEL_TOKEN`, `COMPOSER_AUTH`, and Cloudflare R2 (`AWS_*` + `FILESYSTEM_MEDIA_DISK=s3`).

### GitHub

| Name | Type |
|------|------|
| `SSH_HOST` | secret |
| `SSH_PRIVATE_KEY` | secret |
| `CLOUDFLARE_TUNNEL_TOKEN` | secret |
| `COMPOSER_AUTH` | secret |
| `AWS_ACCESS_KEY_ID` | secret |
| `AWS_SECRET_ACCESS_KEY` | secret |
| `SSH_USER` | variable |
| `APP_URL` | variable (public https URL) |
| `APP_NAME` | variable (optional; default `Laravel`) |
| `DEPLOY_PATH` | variable (optional; default `/opt/<repo-name>`) |
| `AWS_BUCKET` | variable |
| `AWS_ENDPOINT` | variable (`https://<accountid>.r2.cloudflarestorage.com`) |
| `AWS_DEFAULT_REGION` | variable (`auto`) |
| `AWS_URL` | variable (optional; only if serving objects via public URL/CDN) |

Local development keeps `FILESYSTEM_MEDIA_DISK=public`. Production deploy always sets `FILESYSTEM_MEDIA_DISK=s3` and injects the R2 credentials into `.env`. Media is private on R2 and served through the app (`Storage::get()`), not via public object URLs.

## Server bootstrap (one-time)

1. Docker Engine + Compose plugin + Git
2. CI public key in `~/.ssh/authorized_keys` for `SSH_USER`
3. Tunnel origin → `http://web:80`
4. Push to `main` (or run the Deploy workflow)

Git pull on the server uses `GITHUB_TOKEN` from Actions (HTTPS), so no deploy key is required.
## Deploy flow

`.github/workflows/deploy.yml` → CI → SSH → `scripts/deploy.sh`:

1. `git fetch` / `reset --hard origin/main`
2. Sync tunnel token + `COMPOSER_AUTH` / `secrets/composer_auth`
3. `docker compose build && up -d`
4. `php-cli`: `migrate --force`, `optimize`, `storage:link`, `reload`
5. Hit `/up`

```bash
export CLOUDFLARE_TUNNEL_TOKEN=...
export COMPOSER_AUTH='...'
export APP_URL=https://example.com
export REPO_URL=https://github.com/<owner>/<repo>.git
cd /opt/<repo> && ./scripts/deploy.sh
```

Ad-hoc Artisan:

```bash
docker compose -f compose.prod.yaml --profile cli run --rm php-cli php artisan <command>
```

## Local development

See [docs/LOCAL.md](LOCAL.md). Short version:

```bash
mkdir -p secrets && printf '%s' "$COMPOSER_AUTH" > secrets/composer_auth
./bin/dev up -d
./bin/dev artisan migrate
./bin/dev bun run dev
```

App: http://localhost:8080

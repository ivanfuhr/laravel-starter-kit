# Local Docker Compose

- This project uses Docker Compose for local development (`compose.dev.yaml`), not Laravel Sail (`boost.json` has `"sail": false`).
- You MUST run PHP, Artisan, Composer, and Bun commands through `./bin/dev` so they execute inside the containers.
- Start services with `./bin/dev up -d` and stop them with `./bin/dev stop`.
- Application URL: `http://localhost:8080` (or `NGINX_PORT`). Use the `get-absolute-url` Boost tool when sharing links.
- Before the first `up`, ensure Flux Pro auth exists: `secrets/composer_auth` (raw `COMPOSER_AUTH` JSON, gitignored).
- Align `.env` with Compose services when using Docker: `DB_CONNECTION=pgsql`, `DB_HOST=postgres`, `DB_PORT=5432`, `DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` matching postgres service, `REDIS_HOST=redis`, and prefer redis for `CACHE_STORE` / `SESSION_DRIVER` / `QUEUE_CONNECTION`.
- Always prefix common tooling with `./bin/dev`. Examples:
    - Artisan: `./bin/dev artisan migrate`
    - Composer: `./bin/dev composer install`
    - Bun/Vite: `./bin/dev bun run dev`
    - PHP: `./bin/dev php -v`
    - Tests: `./bin/dev test` or `./bin/dev pest`
    - Shell in workspace: `./bin/dev shell`
    - Postgres CLI: `./bin/dev psql`
    - Redis CLI: `./bin/dev redis`
- View available commands with `./bin/dev` (no arguments).
- Prefer `./bin/dev` over calling `docker compose -f compose.dev.yaml` directly unless a Compose flag is not wrapped.
- Production stack is `compose.prod.yaml` + `scripts/deploy.sh` (see `docs/DEPLOY.md`). Do not use production Compose for day-to-day local work.

# Laravel Starter Kit

Starter kit Laravel 13 para criar apps com Livewire 4, Flux Pro, Docker Compose, Pest e Boost.

Uso pelo [Laravel installer](https://laravel.com/framework/docs/13.x/starter-kits#community-maintained-starter-kits):

```bash
export COMPOSER_AUTH='{"http-basic":{"composer.fluxui.dev":{"username":"seu-email","password":"sua-license-key"}}}'

laravel new my-app --using=ivanfuhr/laravel-starter-kit --bun
```

`COMPOSER_AUTH` é obrigatório: o kit instala Flux Pro de `composer.fluxui.dev`. No `post-root-package-install` o JSON vai para `secrets/composer_auth` e `auth.json` (gitignored).

Enquanto o pacote não estiver no Packagist:

```bash
laravel new my-app --using=https://github.com/ivanfuhr/laravel-starter-kit --bun
```

Ou via Composer:

```bash
composer create-project ivanfuhr/laravel-starter-kit my-app
cd my-app
composer setup
```

O `--bun` instala o frontend (`bun.lock`). O `post-create-project-cmd` gera a `APP_KEY`, cria o SQLite e roda as migrations.

## O que vem no kit

- Laravel 13, PHP 8.5, Livewire 4, Flux UI (Pro)
- Pest (cobertura 100%), Pint, PHPStan, Rector
- Laravel Boost (skills e MCP)
- Docker Compose local (`./bin/dev`) e stack de produção (nginx, queue, scheduler, Postgres, Redis, Cloudflare Tunnel)
- CI (tests + coverage) e deploy por SSH

Sem autenticação Fortify e sem domínio de negócio: o app gerado começa na welcome do Livewire.

## Requisitos

- PHP 8.5, Composer, Bun 1.4
- Licença [Flux Pro](https://fluxui.dev) (`COMPOSER_AUTH`)
- Docker Engine + Compose, se for usar `./bin/dev`

## Docker local

Ver [docs/LOCAL.md](docs/LOCAL.md).

```bash
./bin/dev up -d
./bin/dev artisan migrate
./bin/dev bun run dev
```

App: http://localhost:8080 (sem Docker, `APP_URL` default é `http://localhost:8000`).

## Testes

```bash
composer test
```

Ou no container: `./bin/dev test`.

## Produção

Ver [docs/DEPLOY.md](docs/DEPLOY.md). No GitHub Actions, configure `APP_URL` e, se quiser, `DEPLOY_PATH` (default `/opt/<repo-name>`).

## Licença

[MIT](LICENSE)

FROM composer:2 AS vendor

WORKDIR /var/www

COPY . /var/www

RUN --mount=type=secret,id=composer_auth \
    COMPOSER_AUTH="$(cat /run/secrets/composer_auth)" \
    composer install --no-dev --no-scripts --no-interaction --no-progress --prefer-dist

FROM oven/bun:1.4 AS assets

WORKDIR /var/www

COPY package.json bun.lock .bunfig.toml ./
RUN bun install --frozen-lockfile

COPY . .
COPY --from=vendor /var/www/vendor /var/www/vendor

RUN bun run build

FROM nginx:alpine

RUN apk add --no-cache wget

COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=assets /var/www/public /var/www/public

WORKDIR /var/www/public

CMD ["nginx", "-g", "daemon off;"]

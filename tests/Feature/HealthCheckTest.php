<?php

declare(strict_types=1);

it('exposes the application health endpoint', function () {
    $this->get('/up')->assertSuccessful();
});

it('documents required production environment keys', function () {
    $example = file_get_contents(base_path('.env.example'));

    expect($example)
        ->toContain('CLOUDFLARE_TUNNEL_TOKEN=')
        ->toContain('APP_URL=')
        ->toContain('DB_CONNECTION=')
        ->toContain('COMPOSER_AUTH=')
        ->toContain('secrets/composer_auth');
});

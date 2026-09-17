<?php

declare(strict_types=1);

it('is a composer project starter kit', function () {
    /** @var array<string, mixed> $composer */
    $composer = json_decode((string) file_get_contents(base_path('composer.json')), true);

    expect($composer['type'])->toBe('project')
        ->and($composer['scripts']['post-root-package-install'])->toContain('@php bin/install-composer-auth.php')
        ->and($composer['scripts']['post-create-project-cmd'])->toContain('@php artisan key:generate --ansi')
        ->and($composer['scripts']['post-create-project-cmd'])->toContain('@php artisan migrate --graceful --ansi');
});

it('defines required environment variables in .env.example', function () {
    $example = file_get_contents(base_path('.env.example'));

    expect($example)
        ->toContain('APP_KEY=')
        ->toContain('APP_URL=')
        ->toContain('DB_CONNECTION=sqlite')
        ->toContain('COMPOSER_AUTH=')
        ->toContain('secrets/composer_auth');
});

it('ships flux pro composer auth install for create-project', function () {
    expect(file_exists(base_path('bin/install-composer-auth.php')))->toBeTrue();
});

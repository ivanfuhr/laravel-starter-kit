<?php

declare(strict_types=1);

if (! is_dir('secrets')) {
    mkdir('secrets', 0700, true);
}

$auth = getenv('COMPOSER_AUTH');

if (! is_string($auth) || $auth === '') {
    return;
}

file_put_contents('secrets/composer_auth', $auth);

if (! file_exists('auth.json')) {
    file_put_contents('auth.json', $auth);
}

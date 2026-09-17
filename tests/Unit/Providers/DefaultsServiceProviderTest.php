<?php

declare(strict_types=1);

use App\Providers\DefaultsServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

test('models are unguarded', function (): void {
    expect(Model::isUnguarded())->toBeTrue();
});

test('password defaults are unset outside production', function () {
    expect(app()->isProduction())->toBeFalse();

    $validator = Validator::make(
        ['password' => 'password'],
        ['password' => Password::defaults()],
    );

    expect($validator->passes())->toBeTrue();
});

test('password defaults are strict in production', function () {
    app()->detectEnvironment(fn (): string => 'production');

    (new DefaultsServiceProvider(app()))->boot();

    expect(app()->isProduction())->toBeTrue();

    $validator = Validator::make(
        ['password' => 'password'],
        ['password' => Password::defaults()],
    );

    expect($validator->fails())->toBeTrue();
});

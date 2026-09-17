<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Str;

test('user uses uuid primary keys', function () {
    $user = User::factory()->create();

    expect(Str::isUuid($user->id))->toBeTrue();
});

test('user is verified by default', function () {
    $user = User::factory()->create();

    expect($user->hasVerifiedEmail())->toBeTrue();
});

test('user password is hashed', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    expect($user->password)->not->toBe('password')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->created_at)->not->toBeNull()
        ->and($user->updated_at)->not->toBeNull();
});

test('user can be unverified', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull()
        ->and($user->hasVerifiedEmail())->toBeFalse();
});

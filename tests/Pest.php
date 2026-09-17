<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit', '../resources/views');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your
| test files.
|
*/

/*
|--------------------------------------------------------------------------
| Tia Engine
|--------------------------------------------------------------------------
|
| Re-run only tests affected by local changes. Skipped automatically on CI / --ci.
| Baselined fetches the shared dependency graph recorded by .github/workflows/tia-baseline.yml.
|
*/

pest()->tia()
    ->locally()
    ->baselined()
    ->defaultBranch('main');

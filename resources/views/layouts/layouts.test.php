<?php

declare(strict_types=1);

use Laravel\Head\Facades\Head;

it('renders the guest layout with main content', function () {
    Head::title('Sign in');

    $this->blade(
        '<x-layouts::guest><div>Guest content</div></x-layouts::guest>',
    )
        ->assertSee('Guest content')
        ->assertSeeHtml('Sign in - '.config('app.name'))
        ->assertSeeHtml('data-flux-main')
        ->assertSeeHtml('window.Flux.applyAppearance');
});

it('renders the app layout with main content', function () {
    Head::title('Dashboard');

    $this->blade(
        '<x-layouts::app><div>App content</div></x-layouts::app>',
    )
        ->assertSee('App content')
        ->assertSeeHtml('Dashboard - '.config('app.name'))
        ->assertSeeHtml('data-flux-main')
        ->assertDontSeeHtml('data-flux-sidebar');
});

<?php

declare(strict_types=1);

it('renders the welcome page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(__('Welcome'))
        ->assertSee(__('Documentation'))
        ->assertSee(__('Laracasts'))
        ->assertSeeHtml('Welcome - '.config('app.name'));
});

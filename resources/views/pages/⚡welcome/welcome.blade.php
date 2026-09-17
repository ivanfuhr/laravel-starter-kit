<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Welcome') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Laravel has an incredibly rich ecosystem. We suggest starting with the following.') }}
        </flux:text>
    </div>

    <div class="flex flex-wrap gap-3">
        <flux:button
            href="https://laravel.com/docs"
            target="_blank"
            variant="primary"
            icon:trailing="arrow-top-right-on-square"
        >
            {{ __('Documentation') }}
        </flux:button>

        <flux:button
            href="https://laracasts.com"
            target="_blank"
            variant="ghost"
            icon:trailing="arrow-top-right-on-square"
        >
            {{ __('Laracasts') }}
        </flux:button>
    </div>
</div>

<?php

use Livewire\Component;

new class extends Component
{
    public bool $visible = false;

    public function mount(): void
    {
        $this->visible = blank(request()->cookie(config('localization.consent_cookie')));
    }

    public function accept(): void
    {
        cookie()->queue(cookie()->forever(config('localization.consent_cookie'), 'accepted'));
        $this->visible = false;
    }
};
?>

<div>
    @if ($visible)
        <div class="fixed inset-x-0 bottom-0 z-50 p-4">
            <div class="mx-auto flex max-w-3xl flex-col gap-4 rounded-2xl border border-base-content/10 bg-base-100 p-4 shadow-xl sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-base-content/80">
                    {{ __('cookie.body') }}
                    <a href="{{ route('legal.privacy') }}" wire:navigate class="link link-primary">{{ __('legal.privacy') }}</a>
                    ·
                    <a href="{{ route('legal.cookies') }}" wire:navigate class="link link-primary">{{ __('legal.cookies') }}</a>
                </p>
                <button type="button" class="btn btn-primary btn-sm shrink-0" wire:click="accept">
                    {{ __('cookie.accept') }}
                </button>
            </div>
        </div>
    @endif
</div>

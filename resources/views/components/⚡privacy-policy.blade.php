<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Privacy Policy')] class extends Component
{
};
?>

<article class="prose prose-sm max-w-3xl text-base-content">
    <h1 class="text-3xl font-semibold tracking-tight">{{ __('legal.privacy') }}</h1>
    <p class="mt-4 text-base-content/70">{{ __('legal.updated') }}</p>
    <div class="mt-6 space-y-4 text-base-content/80">
        <p>{{ __('legal.privacy_p1') }}</p>
        <p>{{ __('legal.privacy_p2') }}</p>
        <p>{{ __('legal.privacy_p3') }}</p>
        <p>{{ __('legal.privacy_p4') }}</p>
    </div>
</article>

<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    @php
        $current = config('localization.locales.'.app()->getLocale(), config('localization.locales.en'));
    @endphp
    <x-dropdown right>
        <x-slot:trigger>
            <button type="button" class="btn btn-ghost btn-sm gap-2">
                <x-flag :country="$current['flag']" />
                <span>{{ strtoupper(app()->getLocale()) }}</span>
            </button>
        </x-slot:trigger>
        @foreach (config('localization.locales') as $code => $locale)
            <li>
                <a href="{{ route('locale.switch', $code) }}" class="flex items-center gap-2">
                    <x-flag :country="$locale['flag']" />
                    <span>{{ $locale['label'] }}</span>
                </a>
            </li>
        @endforeach
    </x-dropdown>
</div>

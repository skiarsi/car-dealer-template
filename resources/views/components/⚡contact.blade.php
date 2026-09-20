<?php

use App\Models\Dealership;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Contact')] class extends Component
{
    public function with(): array
    {
        return [
            'dealership' => Dealership::current(),
        ];
    }
};
?>

<div class="mx-auto max-w-xl space-y-6">
    <h1 class="text-3xl font-semibold tracking-tight">{{ __('contact.title') }}</h1>
    @if ($dealership)
        <dl class="space-y-4 rounded-2xl border border-base-content/10 bg-base-100 p-6 text-base-content/80">
            <div>
                <dt class="text-sm text-base-content/50">{{ __('contact.address') }}</dt>
                <dd class="mt-1">{{ $dealership->address }}<br>{{ $dealership->city }}</dd>
            </div>
            <div>
                <dt class="text-sm text-base-content/50">{{ __('inquiry.phone') }}</dt>
                <dd class="mt-1"><a href="tel:{{ $dealership->phone }}" class="link link-hover">{{ $dealership->phone }}</a></dd>
            </div>
            <div>
                <dt class="text-sm text-base-content/50">{{ __('inquiry.email') }}</dt>
                <dd class="mt-1"><a href="mailto:{{ $dealership->email }}" class="link link-hover">{{ $dealership->email }}</a></dd>
            </div>
            <div>
                <dt class="text-sm text-base-content/50">{{ __('contact.hours') }}</dt>
                <dd class="mt-3 space-y-2">
                    @foreach ($dealership->weeklyHours() as $day => $slot)
                        <div class="flex justify-between gap-4 text-sm">
                            <span>{{ __('days.'.$day) }}</span>
                            <span>
                                @if ($slot['closed'])
                                    {{ __('hours.closed') }}
                                @else
                                    {{ $slot['open'] }} – {{ $slot['close'] }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </dd>
            </div>
        </dl>
    @endif
</div>

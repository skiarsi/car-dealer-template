<?php

use App\Models\Inquiry;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'latest' => Inquiry::query()
                ->with(['vehicle.brand', 'vehicle.vehicleModel'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
};
?>

<section class="space-y-4" wire:poll.visible.3600s>
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">{{ __('admin.latest') }}</h2>
        <a href="{{ route('admin.inquiries') }}" wire:navigate class="text-sm text-primary">{{ __('admin.view_all') }}</a>
    </div>
    @forelse ($latest as $inquiry)
        <article class="cursor-pointer rounded-xl border border-base-content/10 bg-base-100 p-4" wire:click="$dispatch('inquiry-open', { id: {{ $inquiry->id }} })">
            <div class="flex items-start justify-between gap-3">
                <p class="font-medium">{{ $inquiry->name }}</p>
                @if ($inquiry->isNew())
                    <span class="badge badge-primary badge-sm">{{ __('status.new') }}</span>
                @endif
            </div>
            <p class="text-sm text-base-content/60">{{ $inquiry->vehicle?->title() }}</p>
            <p class="mt-1 text-sm">{{ $inquiry->email ?? $inquiry->phone }}</p>
            @if ($inquiry->message)
                <p class="mt-2 line-clamp-2 text-sm text-base-content/80">{{ $inquiry->message }}</p>
            @endif
        </article>
    @empty
        <p class="text-base-content/60">{{ __('admin.empty') }}</p>
    @endforelse
</section>

<?php

use App\Models\Inquiry;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'count' => Inquiry::query()->where('status', 'new')->count(),
        ];
    }
};
?>

<li wire:poll.visible.3600s>
    <a href="{{ route('admin.inquiries') }}" wire:navigate class="my-0.5 px-4 py-1.5 hover:text-inherit">
        <x-icon name="o-inbox" class="mb-0.5" />
        <span class="flex items-center gap-2">
            {{ __('admin.inquiries') }}
            @if ($count > 0)
                <span class="badge badge-primary badge-sm">{{ $count }}</span>
            @endif
        </span>
    </a>
</li>

<?php

use App\Models\Inquiry;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public bool $open = false;

    public ?int $inquiryId = null;

    #[On('inquiry-open')]
    public function show(int $id): void
    {
        $this->inquiryId = $id;
        $this->open = true;

        Inquiry::query()->whereKey($id)->where('status', 'new')->update(['status' => 'read']);
    }

    public function updatedOpen(bool $open): void
    {
        if (! $open) {
            $this->inquiryId = null;
        }
    }

    public function with(): array
    {
        return [
            'inquiry' => $this->inquiryId
                ? Inquiry::query()->with(['vehicle.brand', 'vehicle.vehicleModel'])->find($this->inquiryId)
                : null,
        ];
    }
};
?>

<x-modal wire:model="open" :title="$inquiry?->name ?? __('inquiry.message')" separator>
    @if ($inquiry)
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('admin.vehicle') }}</dt>
                <dd class="mt-1 font-medium">{{ $inquiry->vehicle?->title() ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('inquiry.email') }}</dt>
                <dd class="mt-1">{{ $inquiry->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('inquiry.phone') }}</dt>
                <dd class="mt-1">{{ $inquiry->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('admin.received') }}</dt>
                <dd class="mt-1">{{ $inquiry->created_at->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('inquiry.message') }}</dt>
                <dd class="mt-2 whitespace-pre-wrap rounded-xl bg-base-200/70 p-3">{{ $inquiry->message ?: __('admin.no_message') }}</dd>
            </div>
        </dl>
    @endif

    <x-slot:actions>
        <x-button :label="__('admin.close')" class="btn-ghost" @click="$wire.open = false" />
    </x-slot:actions>
</x-modal>

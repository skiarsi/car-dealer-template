<?php

use App\Models\Inquiry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::admin')] #[Title('Inquiries')] class extends Component
{
    use WithPagination;

    public function markRead(int $id): void
    {
        Inquiry::query()->whereKey($id)->update(['status' => 'read']);
    }

    public function with(): array
    {
        return [
            'inquiries' => Inquiry::query()
                ->with(['vehicle.brand', 'vehicle.vehicleModel'])
                ->latest()
                ->paginate(12),
        ];
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">{{ __('admin.inquiries') }}</h1>

    <div class="overflow-x-auto rounded-2xl border border-base-content/10 bg-base-100">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('inquiry.name') }}</th>
                    <th>{{ __('admin.vehicle') }}</th>
                    <th>{{ __('inquiry.email') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.received') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->name }}</td>
                        <td>{{ $inquiry->vehicle?->title() }}</td>
                        <td>
                            <div>{{ $inquiry->email ?? '—' }}</div>
                            <div class="text-sm text-base-content/60">{{ $inquiry->phone ?? '—' }}</div>
                        </td>
                        <td>{{ __('status.'.$inquiry->status) }}</td>
                        <td>{{ $inquiry->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-right">
                            @if ($inquiry->isNew())
                                <button type="button" class="btn btn-ghost btn-xs" wire:click="markRead({{ $inquiry->id }})">
                                    {{ __('admin.mark_read') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-base-content/60">{{ __('admin.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $inquiries->links() }}</div>
</div>

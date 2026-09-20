<?php

use App\Models\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('layouts::admin')] #[Title('Vehicles')] class extends Component
{
    use Toast, WithPagination;

    public function delete(int $id): void
    {
        $vehicle = Vehicle::query()->with('images')->findOrFail($id);
        $vehicle->delete();
        $this->success(__('admin.vehicle_deleted'));
    }

    public function with(): array
    {
        return [
            'vehicles' => Vehicle::query()
                ->with(['brand', 'vehicleModel', 'images'])
                ->latest()
                ->paginate(12),
        ];
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ __('admin.vehicles') }}</h1>
        <a href="{{ route('admin.vehicles.create') }}" wire:navigate class="btn btn-primary btn-sm">{{ __('admin.add_vehicle') }}</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-base-content/10 bg-base-100">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ __('admin.vehicle') }}</th>
                    <th>{{ __('vehicle.year') }}</th>
                    <th>{{ __('vehicle.price') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vehicles as $vehicle)
                    <tr>
                        <td class="w-16">
                            @if ($vehicle->coverUrl())
                                <img src="{{ $vehicle->coverUrl() }}" alt="" class="h-12 w-16 rounded object-cover">
                            @else
                                <div class="flex h-12 w-16 items-center justify-center rounded bg-base-200 text-xs text-base-content/50">—</div>
                            @endif
                        </td>
                        <td>
                            <div class="font-medium">{{ $vehicle->brand->name }} {{ $vehicle->vehicleModel->name }}</div>
                            <div class="text-sm text-base-content/60">{{ __('engine.'.$vehicle->engine_type) }} · {{ $vehicle->seats }} {{ __('vehicle.seats') }}</div>
                        </td>
                        <td>{{ $vehicle->year }}</td>
                        <td>{{ $vehicle->formattedPrice() }}</td>
                        <td>{{ __('listing.'.$vehicle->status) }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" wire:navigate class="btn btn-ghost btn-xs">{{ __('admin.edit') }}</a>
                            <button type="button" class="btn btn-ghost btn-xs text-error" wire:click="delete({{ $vehicle->id }})" wire:confirm="{{ __('admin.confirm_delete') }}">
                                {{ __('admin.delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-base-content/60">{{ __('admin.no_vehicles') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $vehicles->links() }}</div>
</div>

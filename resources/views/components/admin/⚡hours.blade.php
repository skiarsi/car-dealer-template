<?php

use App\Models\Dealership;
use App\Support\OpeningHours;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::admin')] #[Title('Hours')] class extends Component
{
    use Toast;

    public array $schedule = [];

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $city = '';

    public function mount(): void
    {
        $dealership = Dealership::current() ?? new Dealership;
        $this->schedule = OpeningHours::normalize($dealership->opening_hours);
        $this->phone = (string) $dealership->phone;
        $this->email = (string) $dealership->email;
        $this->address = (string) $dealership->address;
        $this->city = (string) $dealership->city;
    }

    public function save(): void
    {
        $this->schedule = OpeningHours::normalize($this->schedule);

        $this->validate([
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'address' => ['nullable', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:80'],
            'schedule' => ['required', 'array'],
            'schedule.*.closed' => ['boolean'],
            'schedule.*.open' => ['required', 'date_format:H:i'],
            'schedule.*.close' => ['required', 'date_format:H:i'],
        ]);

        $dealership = Dealership::current() ?? Dealership::query()->create([
            'name' => config('app.name'),
        ]);

        $dealership->update([
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'opening_hours' => OpeningHours::normalize($this->schedule),
        ]);

        $this->success(__('admin.hours_saved'));
    }

    public function with(): array
    {
        return [
            'days' => OpeningHours::DAYS,
        ];
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">{{ __('admin.hours') }}</h1>

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
            <h2 class="mb-4 text-lg font-semibold">{{ __('contact.address') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('inquiry.phone') }}</span>
                    <input type="text" wire:model="phone" class="input input-bordered w-full">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('inquiry.email') }}</span>
                    <input type="email" wire:model="email" class="input input-bordered w-full">
                </label>
                <label class="form-control sm:col-span-2">
                    <span class="mb-1 text-sm">{{ __('contact.address') }}</span>
                    <input type="text" wire:model="address" class="input input-bordered w-full">
                </label>
                <label class="form-control sm:col-span-2">
                    <span class="mb-1 text-sm">{{ __('admin.city') }}</span>
                    <input type="text" wire:model="city" class="input input-bordered w-full">
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
            <h2 class="mb-4 text-lg font-semibold">{{ __('contact.hours') }}</h2>
            <div class="space-y-3">
                @foreach ($days as $day)
                    <div class="grid items-center gap-3 rounded-xl bg-base-200/60 p-3 sm:grid-cols-[8rem_auto_1fr_1fr]">
                        <p class="font-medium">{{ __('days.'.$day) }}</p>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model.live="schedule.{{ $day }}.closed" class="checkbox checkbox-sm">
                            {{ __('hours.closed') }}
                        </label>
                        <label class="form-control">
                            <span class="mb-1 text-xs text-base-content/60">{{ __('hours.opens') }}</span>
                            <input type="time" wire:model="schedule.{{ $day }}.open" class="input input-bordered input-sm w-full" @disabled($schedule[$day]['closed'] ?? false)>
                        </label>
                        <label class="form-control">
                            <span class="mb-1 text-xs text-base-content/60">{{ __('hours.closes') }}</span>
                            <input type="time" wire:model="schedule.{{ $day }}.close" class="input input-bordered input-sm w-full" @disabled($schedule[$day]['closed'] ?? false)>
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
    </form>
</div>

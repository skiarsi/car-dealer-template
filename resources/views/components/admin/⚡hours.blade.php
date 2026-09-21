<?php

use App\Models\Dealership;
use App\Support\OpeningHours;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::admin')] #[Title('Dealership')] class extends Component
{
    use Toast;

    public string $name = '';

    public string $tagline = '';

    public string $tagline_es = '';

    public string $about = '';

    public string $about_es = '';

    public array $schedule = [];

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $city = '';

    public function mount(): void
    {
        $dealership = Dealership::current() ?? new Dealership;
        $this->name = (string) $dealership->name;
        $this->tagline = (string) $dealership->tagline;
        $this->tagline_es = (string) $dealership->tagline_es;
        $this->about = (string) $dealership->about;
        $this->about_es = (string) $dealership->about_es;
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
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'tagline_es' => ['nullable', 'string', 'max:180'],
            'about' => ['nullable', 'string', 'max:5000'],
            'about_es' => ['nullable', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'address' => ['nullable', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:80'],
            'schedule' => ['required', 'array'],
            'schedule.*.closed' => ['boolean'],
            'schedule.*.open' => ['required', 'date_format:H:i'],
            'schedule.*.close' => ['required', 'date_format:H:i'],
        ]);

        $dealership = Dealership::current() ?? new Dealership;

        $dealership->fill([
            'name' => $this->name,
            'tagline' => $this->tagline ?: null,
            'tagline_es' => $this->tagline_es ?: null,
            'about' => $this->about ?: null,
            'about_es' => $this->about_es ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'opening_hours' => OpeningHours::normalize($this->schedule),
        ])->save();

        $this->success(__('admin.hours_saved'));
    }

    public function removeLogo(): void
    {
        $dealership = Dealership::current();

        if (! $dealership?->logo) {
            return;
        }

        Storage::disk('public')->delete($dealership->logo);
        $dealership->update(['logo' => null]);
        $this->success(__('admin.icon_removed'));
    }

    public function with(): array
    {
        return [
            'days' => OpeningHours::DAYS,
            'dealership' => Dealership::current(),
        ];
    }
};
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('admin.hours') }}</h1>
        <p class="mt-1 text-sm text-base-content/60">{{ __('admin.manage_hours') }}</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
            <h2 class="mb-4 text-lg font-semibold">{{ __('admin.company') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="form-control sm:col-span-2">
                    <span class="mb-1 text-sm">{{ __('admin.company_name') }}</span>
                    <input type="text" wire:model="name" class="input input-bordered w-full" required>
                    @error('name') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
                </label>
                <div class="form-control sm:col-span-2">
                    <span class="mb-1 text-sm">{{ __('admin.icon') }}</span>
                    <p class="mb-3 text-sm text-base-content/60">{{ __('admin.icon_hint') }}</p>
                    @if ($dealership?->logoUrl())
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ $dealership->logoUrl() }}" alt="" class="h-16 w-16 rounded-xl border border-base-content/10 bg-base-200 object-contain p-1">
                            <button type="button" class="btn btn-ghost btn-sm" wire:click="removeLogo">{{ __('admin.delete') }}</button>
                        </div>
                    @endif
                    <div
                        x-data="{
                            busy: false,
                            error: '',
                            endpoint: @js(route('admin.logo.store')),
                            failed: @js(__('admin.icon_failed')),
                            async queue(event) {
                                const input = event.target
                                const file = input.files?.[0]
                                input.value = ''
                                if (! file) {
                                    return
                                }
                                this.error = ''
                                this.busy = true
                                try {
                                    const form = new FormData()
                                    form.append('icon', file)
                                    const response = await fetch(this.endpoint, {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        credentials: 'same-origin',
                                        body: form,
                                    })
                                    const data = await response.json().catch(() => ({}))
                                    if (! response.ok) {
                                        this.error = (data.errors && data.errors.icon && data.errors.icon[0]) || data.message || this.failed
                                    } else {
                                        await $wire.$refresh()
                                    }
                                } catch (error) {
                                    this.error = this.failed
                                }
                                this.busy = false
                            }
                        }"
                    >
                        <input type="file" class="file-input file-input-bordered w-full max-w-md" accept="image/jpeg,image/png,image/webp,image/gif" x-on:change="queue($event)">
                        <div x-show="busy" x-cloak class="mt-2 text-sm text-base-content/60">{{ __('admin.uploading') }}</div>
                        <p x-show="error" x-text="error" class="mt-2 text-sm text-error"></p>
                    </div>
                </div>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.tagline_en') }}</span>
                    <input type="text" wire:model="tagline" class="input input-bordered w-full">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.tagline_es') }}</span>
                    <input type="text" wire:model="tagline_es" class="input input-bordered w-full">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.about_en') }}</span>
                    <textarea wire:model="about" class="textarea textarea-bordered w-full" rows="4"></textarea>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.about_es') }}</span>
                    <textarea wire:model="about_es" class="textarea textarea-bordered w-full" rows="4"></textarea>
                </label>
            </div>
        </section>

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

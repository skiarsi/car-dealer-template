<?php

use App\Models\Inquiry;
use App\Models\Vehicle;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Title('Vehicle')] class extends Component
{
    use Toast;

    public Vehicle $vehicle;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public int $activeImage = 0;

    public function mount(Vehicle $vehicle): void
    {
        abort_unless($vehicle->status === 'available' && $vehicle->is_visible, 404);

        $this->vehicle = $vehicle->load(['brand', 'vehicleModel', 'images']);
    }

    public function selectImage(int $index): void
    {
        if ($this->vehicle->images->has($index)) {
            $this->activeImage = $index;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        if (blank($this->email) && blank($this->phone)) {
            $this->addError('email', __('inquiry.contact_required'));

            return;
        }

        Inquiry::query()->create([
            'vehicle_id' => $this->vehicle->id,
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'message' => $this->message ?: null,
            'status' => 'new',
        ]);

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->success(__('inquiry.success'));
    }
};
?>

<div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
    <article class="space-y-6">
        @if ($vehicle->images->isNotEmpty())
            <div class="space-y-3">
                <img src="{{ $vehicle->images[$activeImage]->url() }}" alt="{{ $vehicle->title() }}" class="w-full rounded-2xl object-cover max-h-[28rem]">
                @if ($vehicle->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                        @foreach ($vehicle->images as $index => $image)
                            <button type="button" wire:click="selectImage({{ $index }})" class="overflow-hidden rounded-lg ring-offset-2 ring-offset-base-200 {{ $activeImage === $index ? 'ring-2 ring-primary' : '' }}">
                                <img src="{{ $image->url() }}" alt="" class="h-16 w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
        <p class="text-sm text-base-content/60">
            {{ $vehicle->year }} · {{ __('body.'.$vehicle->body_type) }}
        </p>
        <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
            {{ $vehicle->title() }}
        </h1>
        <p class="text-3xl font-semibold">{{ $vehicle->formattedPrice() }}</p>
        <p class="max-w-xl text-base-content/70">{{ $vehicle->localized('description') }}</p>

        <section>
            <h2 class="mb-4 text-lg font-semibold">{{ __('vehicle.specs') }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                <div class="rounded-xl bg-base-100 p-4">
                    <dt class="text-base-content/50">{{ __('vehicle.engine') }}</dt>
                    <dd class="mt-1 font-medium">{{ __('engine.'.$vehicle->engine_type) }}</dd>
                </div>
                <div class="rounded-xl bg-base-100 p-4">
                    <dt class="text-base-content/50">{{ __('vehicle.transmission') }}</dt>
                    <dd class="mt-1 font-medium">{{ __('transmission.'.$vehicle->transmission) }}</dd>
                </div>
                <div class="rounded-xl bg-base-100 p-4">
                    <dt class="text-base-content/50">{{ __('vehicle.seats') }}</dt>
                    <dd class="mt-1 font-medium">{{ $vehicle->seats }}</dd>
                </div>
                <div class="rounded-xl bg-base-100 p-4">
                    <dt class="text-base-content/50">{{ __('vehicle.mileage') }}</dt>
                    <dd class="mt-1 font-medium">{{ $vehicle->formattedMileage() ?? '—' }}</dd>
                </div>
                <div class="rounded-xl bg-base-100 p-4">
                    <dt class="text-base-content/50">{{ __('vehicle.color') }}</dt>
                    <dd class="mt-1 font-medium">{{ $vehicle->color ?? '—' }}</dd>
                </div>
                <div class="rounded-xl bg-base-100 p-4">
                    <dt class="text-base-content/50">{{ __('vehicle.drivetrain') }}</dt>
                    <dd class="mt-1 font-medium">{{ $vehicle->drivetrain ? __('drivetrain.'.$vehicle->drivetrain) : '—' }}</dd>
                </div>
            </dl>
        </section>
    </article>

    <aside class="h-fit rounded-2xl border border-base-content/10 bg-base-100 p-5 sm:p-6">
        <h2 class="text-lg font-semibold">{{ __('vehicle.ask') }}</h2>
        <p class="mt-2 text-sm text-base-content/70">{{ __('vehicle.ask_lead') }}</p>
        <form wire:submit="submit" class="mt-5 space-y-4">
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('inquiry.name') }}</span>
                <input type="text" wire:model="name" class="input input-bordered w-full" required>
                @error('name') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('inquiry.email') }}</span>
                <input type="email" wire:model="email" class="input input-bordered w-full">
                @error('email') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('inquiry.phone') }}</span>
                <input type="tel" wire:model="phone" class="input input-bordered w-full">
                @error('phone') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('inquiry.message') }}</span>
                <textarea wire:model="message" class="textarea textarea-bordered w-full" rows="3" placeholder="{{ __('inquiry.message_placeholder') }}"></textarea>
            </label>
            <button type="submit" class="btn btn-primary w-full">{{ __('inquiry.submit') }}</button>
        </form>
    </aside>
</div>

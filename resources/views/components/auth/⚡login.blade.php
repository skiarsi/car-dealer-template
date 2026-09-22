<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('Sign in')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public function login(): mixed
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
            $this->addError('email', __('auth.failed'));

            return null;
        }

        session()->regenerate();

        return $this->redirectRoute('dashboard', navigate: true);
    }
};
?>

<div class="mx-auto max-w-md rounded-2xl border border-base-content/10 bg-base-100 p-6 sm:p-8">
    <h1 class="text-2xl font-semibold">{{ __('auth.login') }}</h1>
    <form wire:submit="login" class="mt-6 space-y-4">
        <label class="form-control">
            <span class="mb-1 text-sm">{{ __('auth.email') }}</span>
            <input type="email" wire:model="email" class="input input-bordered w-full" required>
            @error('email') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
        </label>
        <label class="form-control">
            <span class="mb-1 text-sm">{{ __('auth.password') }}</span>
            <input type="password" wire:model="password" class="input input-bordered w-full" required>
        </label>
        <button type="submit" class="btn btn-primary w-full mt-5">{{ __('auth.submit') }}</button>
    </form>
</div>

<?php

use App\Models\ExternalLink;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::admin')] #[Title('Links')] class extends Component
{
    use Toast;

    public ?int $editingId = null;

    public string $label = '';

    public string $url = '';

    public string $platform = 'instagram';

    public string $sort_order = '0';

    public bool $is_visible = true;

    public function edit(int $id): void
    {
        $link = ExternalLink::query()->findOrFail($id);
        $this->editingId = $link->id;
        $this->label = $link->label;
        $this->url = $link->url;
        $this->platform = $link->platform;
        $this->sort_order = (string) $link->sort_order;
        $this->is_visible = $link->is_visible;
    }

    public function cancel(): void
    {
        $this->reset(['editingId', 'label', 'url', 'platform', 'sort_order', 'is_visible']);
        $this->platform = 'instagram';
        $this->sort_order = '0';
        $this->is_visible = true;
    }

    public function save(): void
    {
        $this->validate([
            'label' => ['required', 'string', 'max:80'],
            'url' => ['required', 'url', 'max:255'],
            'platform' => ['required', 'in:'.implode(',', ExternalLink::PLATFORMS)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:99'],
            'is_visible' => ['boolean'],
        ]);

        $payload = [
            'label' => $this->label,
            'url' => $this->url,
            'platform' => $this->platform,
            'sort_order' => (int) $this->sort_order,
            'is_visible' => $this->is_visible,
        ];

        if ($this->editingId) {
            ExternalLink::query()->whereKey($this->editingId)->update($payload);
        } else {
            ExternalLink::query()->create($payload);
        }

        $this->cancel();
        $this->success(__('admin.link_saved'));
    }

    public function delete(int $id): void
    {
        ExternalLink::query()->whereKey($id)->delete();
        if ($this->editingId === $id) {
            $this->cancel();
        }
        $this->success(__('admin.link_deleted'));
    }

    public function with(): array
    {
        return [
            'links' => ExternalLink::query()->orderBy('sort_order')->orderBy('id')->get(),
            'platforms' => ExternalLink::PLATFORMS,
        ];
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">{{ __('admin.links') }}</h1>
    <p class="text-sm text-base-content/70">{{ __('admin.links_lead') }}</p>

    <form wire:submit="save" class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
        <h2 class="mb-4 text-lg font-semibold">{{ $editingId ? __('admin.edit_link') : __('admin.add_link') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('admin.link_label') }}</span>
                <input type="text" wire:model="label" class="input input-bordered w-full" required>
                @error('label') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
            </label>
            <label class="form-control sm:col-span-2">
                <span class="mb-1 text-sm">{{ __('admin.link_url') }}</span>
                <input type="url" wire:model="url" class="input input-bordered w-full" placeholder="https://" required>
                @error('url') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('admin.link_platform') }}</span>
                <select wire:model="platform" class="select select-bordered w-full">
                    @foreach ($platforms as $item)
                        <option value="{{ $item }}">{{ __('social.'.$item) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('admin.link_order') }}</span>
                <input type="number" wire:model="sort_order" class="input input-bordered w-full" min="0" max="99">
            </label>
            <label class="flex items-center gap-2 sm:col-span-2">
                <input type="checkbox" wire:model="is_visible" class="checkbox checkbox-sm">
                <span>{{ __('admin.link_visible') }}</span>
            </label>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm">{{ __('admin.save') }}</button>
            @if ($editingId)
                <button type="button" class="btn btn-ghost btn-sm" wire:click="cancel">{{ __('admin.cancel') }}</button>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-base-content/10 bg-base-100">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('admin.link_order') }}</th>
                    <th>{{ __('admin.link_label') }}</th>
                    <th>{{ __('admin.link_platform') }}</th>
                    <th>{{ __('admin.link_url') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($links as $link)
                    <tr>
                        <td>{{ $link->sort_order }}</td>
                        <td class="flex items-center gap-2">
                            <x-social-icon :platform="$link->platform" />
                            {{ $link->label }}
                        </td>
                        <td>{{ __('social.'.$link->platform) }}</td>
                        <td class="max-w-xs truncate">
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="link link-hover">{{ $link->url }}</a>
                        </td>
                        <td>{{ $link->is_visible ? __('admin.visible') : __('admin.hidden') }}</td>
                        <td class="text-right">
                            <button type="button" class="btn btn-ghost btn-xs" wire:click="edit({{ $link->id }})">{{ __('admin.edit') }}</button>
                            <button type="button" class="btn btn-ghost btn-xs text-error" wire:click="delete({{ $link->id }})" wire:confirm="{{ __('admin.confirm_delete_link') }}">{{ __('admin.delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-base-content/60">{{ __('admin.no_links') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

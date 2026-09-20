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

<div wire:poll.visible.3600s>
    <x-stat :title="__('admin.new_inquiries')" :value="(string) $count" icon="o-inbox" />
</div>

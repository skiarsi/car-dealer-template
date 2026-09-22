@props([
    'min',
    'max',
    'step' => 1,
    'from' => '',
    'to' => '',
    'method',
    'format' => 'number',
])

@php
    $min = (int) $min;
    $max = max($min, (int) $max);
    $fromValue = $from === '' || $from === null ? $min : (int) $from;
    $toValue = $to === '' || $to === null ? $max : (int) $to;
    $fromValue = min(max($fromValue, $min), $max);
    $toValue = min(max($toValue, $min), $max);
@endphp

<div
    wire:key="{{ $method }}-{{ $from }}-{{ $to }}-{{ $min }}-{{ $max }}"
    x-data="{
        min: {{ $min }},
        max: {{ $max }},
        from: {{ $fromValue }},
        to: {{ $toValue }},
        format: @js($format),
        pct(value) {
            if (this.max === this.min) {
                return 0
            }

            return ((value - this.min) / (this.max - this.min)) * 100
        },
        label(value) {
            if (this.format === 'price') {
                return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value)
            }

            return String(value)
        },
        clampFrom() {
            if (this.from > this.to) {
                this.from = this.to
            }
        },
        clampTo() {
            if (this.to < this.from) {
                this.to = this.from
            }
        },
        commit() {
            this.clampFrom()
            this.clampTo()
            $wire.{{ $method }}(
                this.from <= this.min ? '' : this.from,
                this.to >= this.max ? '' : this.to
            )
        }
    }"
    class="space-y-2"
>
    <div class="flex items-center justify-between text-sm">
        <span x-text="label(from)"></span>
        <span class="text-base-content/50">–</span>
        <span x-text="label(to)"></span>
    </div>
    <div class="dual-range">
        <div class="dual-range__track mt-1"></div>
        <div class="dual-range__fill mt-1" :style="`left: ${pct(from)}%; width: ${Math.max(0, pct(to) - pct(from))}%`"></div>
        <input
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model.number="from"
            @input="clampFrom()"
            @change="commit()"
            @mouseup="commit()"
            @touchend="commit()"
            :style="`z-index: ${from > min + (max - min) * 0.5 ? 4 : 3}`"
            aria-label="{{ $format === 'price' ? __('search.price_min') : __('search.year_from') }}"
        >
        <input
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model.number="to"
            @input="clampTo()"
            @change="commit()"
            @mouseup="commit()"
            @touchend="commit()"
            :style="`z-index: ${to < min + (max - min) * 0.5 ? 4 : 3}`"
            aria-label="{{ $format === 'price' ? __('search.price_max') : __('search.year_to') }}"
        >
    </div>
</div>

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalLink extends Model
{
    /** @use HasFactory<\Database\Factories\ExternalLinkFactory> */
    use HasFactory;

    public const PLATFORMS = [
        'instagram',
        'facebook',
        'x',
        'youtube',
        'tiktok',
        'whatsapp',
        'google',
        'website',
    ];

    protected $fillable = [
        'label',
        'url',
        'platform',
        'sort_order',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderBy('sort_order')->orderBy('id');
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The Locale model represents a locale in the application.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Translation $translations
 */
final class Locale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the translations for the locale.
     *
     * @return HasMany<Translation, Locale>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Scope a query to search translations by key or value.
     *
     * @param Builder $query
     * @param ?string $search
     * @return void
     */
    public static function scopeSearch(Builder $query, ?string $search = null): void
    {
        if (isset($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('code', $search)
                ->orWhere('name', $search);
            });
        }
    }
}

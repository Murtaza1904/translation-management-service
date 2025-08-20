<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * The Translation model represents a translation in the application.
 *
 * @property int $id
 * @property int $locale_id
 * @property string $key
 * @property string $value
 * @property string|null $namespace
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Locale $locale
 * @property-read BelongsToMany<Tag, Translation> $tags
 */
final class Translation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'locale_id',
        'key',
        'value',
        'namespace',
    ];

    /**
     * Get the locale that owns the translation.
     *
     * @return BelongsTo<Locale, Translation>
     */
    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    /**
     * Get the tags associated with the translation.
     *
     * @return BelongsToMany<Tag, Translation>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Scope a query to only include translations for a given locale.
     *
     * @param Builder $query
     * @param ?string $localeCode
     * @return void
     */
    public static function scopeWithLocale(Builder $query, ?string $localeCode = null): void
    {
        if (isset($localeCode)) {
            $query->whereHas('locale', fn ($q) => $q->where('code', $localeCode));
        }
    }

    /**
     * Scope a query to only include translations with a specific tag.
     *
     * @param Builder $query
     * @param ?string $tagName
     * @return void
     */
    public static function scopeWithTag(Builder $query, ?string $tagName = null): void
    {
        if (isset($tagName)) {
            $query->whereHas('tags', fn ($q) => $q->where('name', $tagName));
        }
    }

    /**
     * Scope a query to only include translations with a specific key.
     *
     * @param Builder $query
     * @param ?string $key
     * @return void
     */
    public static function scopeWithKey(Builder $query, ?string $key = null): void
    {
        if (isset($key)) {
            $query->where('key', $key);
        }
    }

    /**
     * Scope a query to only include translations with a specific namespace.
     *
     * @param Builder $query
     * @param ?string $namespace
     * @return void
     */
    public static function scopeWithNamespace(Builder $query, ?string $namespace = null): void
    {
        if (isset($namespace)) {
            $query->where('namespace', $namespace);
        }
    }

    /**
     * Scope a query to search translations by key or value.
     *
     * @param Builder $query
     * @param ?string $term
     * @return void
     */
    public static function scopeSearch(Builder $query, ?string $term = null): void
    {
        // if (isset($term)) {
        //     $query->where(function (Builder $q) use ($term) {
        //         $q->whereFullText('key', $term)
        //         ->orWhereFullText('value', $term);
        //     });
        // }
    }
}

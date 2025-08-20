<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * The Tag model represents a tag in the application.
 *
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Translation $translations
 */
final class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the translations for the tag.
     *
     * @return BelongsToMany<Translation, Tag>
     */
    public function translations(): BelongsToMany
    {
        return $this->belongsToMany(Translation::class);
    }
}

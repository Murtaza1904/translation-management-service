<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Translation>
 */
final class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale_id' => Locale::inRandomOrder()->value('id'),
            'key'       => $this->faker->unique()->slug(3),
            'value'     => $this->faker->sentence(6),
            'namespace' => $this->faker->randomElement(['web', 'mobile', 'desktop']),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

final class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = [
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'French',  'code' => 'fr'],
            ['name' => 'Spanish', 'code' => 'es'],
        ];

        foreach ($locales as $locale) {
            Locale::firstOrCreate([
                'name' => $locale['name'],
                'code' => $locale['code'],
            ]);
        }
    }
}

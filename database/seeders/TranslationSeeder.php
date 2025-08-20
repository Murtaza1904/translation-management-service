<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = 100000;
        $chunk = 5000;

        $localeIds = Locale::pluck('id')->all();
        $tagIds = Tag::pluck('id')->all();

        $batches = (int) ceil($count / $chunk);

        for ($i = 0; $i < $batches; $i++) {
            $toMake = min($chunk, $count - ($i * $chunk));

            $rows = Translation::factory()
                ->count($toMake)
                ->make(['locale_id' => $localeIds[array_rand($localeIds)]])
                ->toArray();

            Translation::insert($rows);
        }

        $translationIds = Translation::pluck('id')->all();
        $pairs = [];
        foreach ($translationIds as $id) {
            $pairs[] = [
                'translation_id' => $id,
                'tag_id' => $tagIds[array_rand($tagIds)],
            ];
        }

        foreach (array_chunk($pairs, 5000) as $c) {
            DB::table('tag_translation')->insert($c);
        }
    }
}

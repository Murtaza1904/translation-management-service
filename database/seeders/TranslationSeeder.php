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

        $this->command->info("Seeding {$count} translations in {$batches} batches...");
        $bar = $this->command->getOutput()->createProgressBar($batches);
        $bar->start();

        for ($i = 0; $i < $batches; $i++) {
            $toMake = min($chunk, $count - ($i * $chunk));

            $rows = Translation::factory()
                ->count($toMake)
                ->make(['locale_id' => $localeIds[array_rand($localeIds)]])
                ->toArray();

            Translation::insert($rows);

            $bar->advance();
        }

        $bar->finish();

        $this->command->newLine();
        $this->command->info("Attaching tags to translations...");

        $translationIds = Translation::pluck('id')->all();
        $pairs = [];

        foreach ($translationIds as $id) {
            $pairs[] = [
                'translation_id' => $id,
                'tag_id' => $tagIds[array_rand($tagIds)],
            ];
        }

        $chunks = array_chunk($pairs, 5000);
        $bar = $this->command->getOutput()->createProgressBar(count($chunks));
        $bar->start();

        foreach ($chunks as $c) {
            DB::table('tag_translation')->insert($c);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
    }
}

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('locale_id')->constrained()->cascadeOnDelete();
            $table->string('key', 191);
            $table->text('value');
            $table->string('namespace', 64)->nullable()->index();
            $table->unique(['locale_id', 'key']);
            $table->index(['key', 'locale_id']);
            $table->timestamps();
        });

        Schema::table('translations', function (Blueprint $table): void {
            $table->fullText(['key', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};

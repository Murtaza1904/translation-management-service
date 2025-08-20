<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\V1\TranslationRequest;
use App\Http\Resources\Api\V1\TranslationResource;

final class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $translations = Translation::query()
                    ->with('locale:id,code', 'tags:id,name')
                    ->withLocale($request->input('locale'))
                    ->withTag($request->input('tag'))
                    ->withKey($request->input('key'))
                    ->withNamespace($request->input('namespace'))
                    ->search($request->input('search'))
                    ->latest()
                    ->paginate(min(100, $request->input('per_page', 50)));
        
        return TranslationResource::collection($translations)
            ->additional([
                'meta' => [
                    'total' => $translations->total(),
                    'per_page' => $translations->perPage(),
                    'current_page' => $translations->currentPage(),
                ],
            ]);
    }

    public function store(TranslationRequest $request)
    {
        $locale = Locale::where('code', $request->string('locale'))->firstOrFail();

        $t = DB::transaction(function () use ($request, $locale) {
            $t = Translation::updateOrCreate(
                ['locale_id' => $locale->id, 'key' => $request->string('key')],
                ['value' => $request->string('value'), 'namespace' => $request->input('namespace')]
            );
            if ($request->filled('tags')) {
                $tagIds = Tag::whereIn('name', $request->input('tags'))->pluck('id');
                $t->tags()->sync($tagIds);
            }
            Cache::tags(['translations'])->flush();
            return $t->load('locale:id,code', 'tags:id,name');
        });

        return response()->json($t, 201);
    }

    public function show(Translation $translation)
    {
        return $translation->load('locale:id,code', 'tags:id,name');
    }

    public function update(TranslationRequest $request, Translation $translation)
    {
        $translation->fill($request->only(['value', 'namespace']))->save();
        if ($request->filled('tags')) {
            $tagIds = Tag::whereIn('name', $request->input('tags'))->pluck('id');
            $translation->tags()->sync($tagIds);
        }
        Cache::tags(['translations'])->flush();
        return $translation->load('locale:id,code', 'tags:id,name');
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
        Cache::tags(['translations'])->flush();
        return response()->noContent();
    }
}

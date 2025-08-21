<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TranslationRequest;
use App\Http\Resources\Api\V1\TranslationResource;

final class TranslationController extends Controller
{
    /**
     * Display a listing of translations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $translations = Translation::query()
                    ->with(['locale', 'tags'])
                    ->withLocale($request->input('locale'))
                    ->withTag($request->input('tag'))
                    ->withNamespace($request->input('namespace'))
                    ->search($request->input('search'))
                    ->latest()
                    ->paginate($request->input('per_page'));

        return response()->json([
            'translations' => TranslationResource::collection($translations),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ],
        ]);
    }

    /**
     * Store a new translation.
     *
     * @param TranslationRequest $request
     * @return JsonResponse
     */
    public function store(TranslationRequest $request): JsonResponse
    {
        $locale = Locale::where('code', $request->string('locale'))->firstOrFail();

        $transaction = DB::transaction(function () use ($request, $locale) {
            $translation = Translation::create($request->validated() + [
                'locale_id' => $locale->id,
            ]);
            if ($request->filled('tags')) {
                $tagIds = Tag::whereIn('name', $request->input('tags'))->pluck('id');
                $translation->tags()->sync($tagIds);
            }

            return $translation->load(['locale', 'tags']);
        });

        return response()->json([
            'message' => 'Translation created successfully',
            'translation' => new TranslationResource($transaction),
        ], 201);
    }

    /**
     * Display a specific translation.
     *
     * @param Translation $translation
     * @return JsonResponse
     */
    public function show(Translation $translation): JsonResponse
    {
        return response()->json([
            'translation' => new TranslationResource($translation->load(['locale', 'tags'])),
        ]);
    }

    /**
     * Update a specific translation.
     *
     * @param TranslationRequest $request
     * @param Translation $translation
     * @return JsonResponse
     */
    public function update(TranslationRequest $request, Translation $translation): JsonResponse
    {
        $translation->update($request->only(['value', 'namespace']));
        if ($request->filled('tags')) {
            $tagIds = Tag::whereIn('name', $request->input('tags'))->pluck('id');
            $translation->tags()->sync($tagIds);
        }

        return response()->json([
            'message' => 'Translation updated successfully',
            'translation' => new TranslationResource($translation->load(['locale', 'tags'])),
        ]);
    }

    /**
     * Delete a specific translation.
     *
     * @param Translation $translation
     * @return JsonResponse
     */
    public function destroy(Translation $translation): JsonResponse
    {
        $translation->delete();

        return response()->json([
            'message' => 'Translation deleted successfully',
        ]);
    }
}

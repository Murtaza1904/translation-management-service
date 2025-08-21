<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string>
     */
    public function toArray(Request $request): array
    {
        unset($request);

        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'namespace' => $this->namespace,
            $this->mergeWhen($this->whenLoaded('locale') && isset($this->locale), [
                'locale' => new LocaleResource($this->locale),
            ]),
            $this->mergeWhen($this->whenLoaded('tags') && $this->tags->isNotEmpty(), [
                'tags' => TagResource::collection($this->tags),
            ]),
        ];
    }
}

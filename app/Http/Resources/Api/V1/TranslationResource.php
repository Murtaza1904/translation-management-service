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
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'namespace' => $this->namespace,
            'locale' => new LocaleResource($this->whenLoaded('locale')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'count_bags' => $this->count_bags,
            'priority' => getPriority($this->priority),
            'format' => getFormat($this->format),
            'is_chirurgical' => $this->is_chirurgical,
            'is_replace' => $this->is_replace,
            'justifications' => $this->justifications,
            'indications' => $this->indications,
            'instructions' => $this->instructions,
            'type' => $this->typeProductBlood,
        ];
    }
}

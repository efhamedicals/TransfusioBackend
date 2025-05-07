<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodBag extends JsonResource
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
            'reference' => $this->reference,
            'price' => $this->price,
            'format' => getFormat($this->format),
            'date_expiration' => $this->date_expiration,
            'status' => $this->status,
            'product' => $this->typeProductBlood,
            'type' => ($this->type_blood_id != null) ? $this->typeBlood : null,
            'blood_bank_id' => $this->blood_bank_id,
            //'bloodBank' => ($this->blood_bank_id != null) ? $this->bloodBank : null,
        ];
    }
}

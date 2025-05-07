<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PrescriptionProduct as PrescriptionProductResource;

class Prescription extends JsonResource
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
            'rai' => $this->rai,
            'patient' => $this->patient,
            'user' => $this->user,
            'hospital' => $this->hospital,
            'products' => PrescriptionProductResource::collection($this->products),
            'status' => $this->status,
            'created_at' => formatDate($this->created_at),
            'updated_at' => $this->updated_at,
            'bloodBag' => ($this->blood_bag_id != null) ? $this->bloodBag : null,
        ];
    }
}

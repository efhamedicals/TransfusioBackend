<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AntecedentProduct as AntecedentProductResource;

class Antecedent extends JsonResource
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
            'date_antecedent' => $this->date_antecedent,
            'clinic' => $this->clinic,
            'result_treatment' => $this->result_treatment,
            'treatments' => $this->treatments,
            'results_treatments' => $this->results_treatments,
            'patient_id' => $this->patient_id,
            'products' => AntecedentProductResource::collection($this->products),
            'reactions' => $this->reactions
        ];
    }
}

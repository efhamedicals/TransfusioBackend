<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PslRequest extends JsonResource
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
            'last_name' => $this->last_name,
            'reference' => $this->reference,
            'first_name' => $this->first_name,
            'prescription' => $this->prescription,
            'blood_report' => $this->blood_report,
            'end_verification' => $this->end_verification,
            'prescription_date' => $this->prescription_date,
            'prescription_fullname' => $this->prescription_fullname,
            'prescription_birth_date' => $this->prescription_birth_date,
            'prescription_age' => $this->prescription_age,
            'prescription_gender' => $this->prescription_gender,
            'prescription_blood_type' => $this->prescription_blood_type,
            'prescription_blood_rh' => $this->prescription_blood_rh,
            'prescription_diagnostic' => $this->prescription_diagnostic,
            'prescription_substitution' => $this->prescription_substitution,
            'created_at' => $this->created_at,
            'products' => $this->products,
            'payment' => $this->payment,
            'status' => $this->status
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Patient extends JsonResource
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
            'married_name' => $this->married_name,
            'first_name' => $this->first_name,
            'gender' => $this->gender,
            'birth' => $this->birth,
            'academic_level' => $this->academic_level,
            'blood_type' => $this->blood_type,
            'rhesus' => $this->rhesus,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'cni' => $this->cni,
            'count_pregnancies' => $this->count_pregnancies,
            'hospital_id' => $this->hospital_id,
            'blood_bank_id' => $this->blood_bank_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

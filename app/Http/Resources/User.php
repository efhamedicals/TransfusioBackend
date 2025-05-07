<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'first_name' => $this->first_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'token' => $this->token,
            'avatar' => $this->avatar,
            'type_user' => $this->type_user,
            'status' => $this->status,
            'blood_bank_id' => $this->blood_bank_id,
            'blood_center_id' => $this->blood_center_id,
            'hospital_id' => $this->hospital_id,
            'hospital' => ($this->blood_bank_id != null) ? $this->bloodBank->hospital : (($this->hospital_id != null) ? $this->hospital : null),
        ];
    }
}

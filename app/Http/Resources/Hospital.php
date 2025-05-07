<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Hospital extends JsonResource
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
            'name' => $this->name,
            'short_name' => $this->short_name,
            'geolocation' => $this->geolocation,
            'phone' => $this->phone,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}

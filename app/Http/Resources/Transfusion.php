<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Prescription as PrescriptionResource;
use App\Models\Transfusion as TransfusionModel;

class Transfusion extends JsonResource
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
      'image' => $this->image,
      'quantity' => $this->quantity,
      'rythm' => $this->rythm,
      'reactions' => $this->reactions,
      'hemo_file' => $this->hemo_file,
      'constantes' => $this->constantes,
      'start_transfusion' => $this->start_transfusion,
      'end_transfusion' => $this->end_transfusion,
      'prescription' => new PrescriptionResource($this->prescription),
      'status' => $this->status,
      'others' =>  TransfusionModel::join("prescriptions", 'transfusions.prescription_id', '=', 'prescriptions.id')->where('prescriptions.patient_id', '=', $this->prescription->patient_id)->where('transfusions.id', '<', $this->id)->orderBy('id', 'DESC')->get(["transfusions.*"]),
      'created_at' => formatDate($this->created_at)
    ];
  }
}

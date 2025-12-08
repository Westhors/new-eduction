<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurriculaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active ?? null,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
        ];
    }
}



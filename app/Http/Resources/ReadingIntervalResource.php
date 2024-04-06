<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingIntervalResource extends JsonResource
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
            'user_name' => $this->user_name,
            'book_name' => $this->book_name,
            'start_page' => $this->start_page,
            'end_page' => $this->end_page,
            'created_at' => $this->created_at
        ];
    }
}


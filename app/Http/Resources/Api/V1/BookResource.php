<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date,
            'user_id' => $this->user_id,
            'genres' => $this->genres->map(fn($genre) => [
                'id' => $genre->id,
                'name' => $genre->name,
            ]),
            'avg_rating' => isset($this->reviews_avg_rating) ? round((float)$this->reviews_avg_rating, 2) : 0,
            'reviews_count' => $this->reviews_count ?? 0,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
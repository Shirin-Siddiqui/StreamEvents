<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $type = explode('\\', $this->eventable_type);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => Str::snake(array_pop($type)),
            'is_read' => (bool) $this->is_read,
            'created_at' => $this->created_at->toDateTimeString(),
            'created_timestamp' => $this->created_at->timestamp,
            'eventable' => $this->eventable
        ];
    }
}

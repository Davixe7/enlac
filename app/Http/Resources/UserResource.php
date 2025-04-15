<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $actualRole = $this->roles()->whereNotIn('id', [1,2])->first();

        return array_merge($data, [
            'is_admin' => $this->roles()->find(1) ? 1 : 0,
            'is_evaluator' => $this->roles()->find(2) ? 1 : 0,
            'full_name' => $this->full_name,
            'leader' => $this->whenLoaded('leader'),
            'work_area' => $this->whenLoaded('work_area'),
            'roles' => $this->whenLoaded('roles'),
            'role_id' => $actualRole ? $actualRole->id : null,
            'role' => $actualRole,
            'notifications' => NotificationResource::collection($this->whenLoaded('notifications'))
        ]);
    }
}

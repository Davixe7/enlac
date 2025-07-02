<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        $basename = pathinfo($this->file_name, PATHINFO_FILENAME);
        $shortened = Str::limit($basename, 6, '...') . Str::substr($basename, -4);

        return array_merge(parent::toArray($request), [
            'file_name' => "{$shortened}.{$extension}",
            'detail'    => $this->getCustomProperty('detail')
        ]);
    }
}

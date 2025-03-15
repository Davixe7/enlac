<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CandidateResults extends ResourceCollection
{
    public $counts;

    public function __construct($resource, $counts)
    {
        parent::__construct($resource);
        $this->counts = $counts;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function toResponse($request)
    {
        return parent::toResponse($request);
    }
}

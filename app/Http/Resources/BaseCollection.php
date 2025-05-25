<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    protected static string|null $message = null;
    public function withMessage(?string $message): self
    {
        static::$message = $message;
        return new static(collect());
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->isPaginated() ? [
            'items' => $this->collectItems(),
            'page' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_page' => $this->lastPage(),
                'links' => [
                    'first' => $this->url(1),
                    'last' => $this->url($this->lastPage()),
                    'prev' => $this->previousPageUrl(),
                    'next' => $this->nextPageUrl(),
                ]
            ]
        ] : ['items' => $this->collectItems()];
    }
    public function collectItems()
    {
        $class = $this->collects;
        return $class ? $class::collection($this->collection) : $this->collection;
    }
    protected function isPaginated(): bool
    {
        return $this->resource instanceof \Illuminate\Pagination\AbstractPaginator;
    }
}

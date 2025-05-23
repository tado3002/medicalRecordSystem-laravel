<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected static string|null $message = null;
    public function withMessage(?string $message): self
    {
        static::$message = $message;
        return new static([]);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
    public function with(Request $request)
    {
        return [
            'success' => true,
            'message' => static::$message ?? 'OK',
            'errors' => null
        ];
    }
}

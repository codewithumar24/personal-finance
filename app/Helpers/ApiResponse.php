<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApiResponse
{
    private mixed $data = null;
    private ?string $message = null;
    private bool $success = true;
    private ?array $pagination = null;

    // Static method to create success response
    public static function success(mixed $data = null, ?string $message = null): JsonResponse
    {
        return (new static())->buildSuccess($data, $message);
    }

    // Static method to create error response
    public static function error(?string $message = null, int $code = 400, mixed $data = null): JsonResponse
    {
        return (new static())->buildError($message, $code, $data);
    }

    // Method for pagination (chainable)
    public function Pagination(Collection|LengthAwarePaginator $paginator): self
    {
        $this->pagination = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];

        return $this;
    }

    private function buildSuccess(mixed $data = null, ?string $message = null): JsonResponse
    {
        $this->success = true;
        $this->data = $data;
        $this->message = $message;

        return $this->build();
    }

    private function buildError(?string $message = null, int $code = 400, mixed $data = null): JsonResponse
    {
        $this->success = false;
        $this->message = $message;
        $this->data = $data;

        return $this->build($code);
    }

    private function build(int $code = 200): JsonResponse
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];

        if ($this->pagination) {
            $response['pagination'] = $this->pagination;
        }

        return response()->json($response, $code);
    }
}

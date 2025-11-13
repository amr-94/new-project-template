<?php

namespace App\Traits;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = '', int $code = 200)
    {
        $response = [
            'status'  => 'success',
            'message' => $message,
        ];

        // if data is paginated
        if ($data instanceof AbstractPaginator) {
            $response['data'] = $data->items();
            $response['meta'] = $this->paginationMeta($data);

            // if  data is a resource collection with a paginator
        } elseif ($data instanceof ResourceCollection && $data->resource instanceof AbstractPaginator) {
            $paginator = $data->resource;
            $response['data'] = $data->collection;
            $response['meta'] = $this->paginationMeta($paginator);

            // if not paginated data or resource collection
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null)
    {
        $response = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Extract pagination meta
     */
    private function paginationMeta($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'last_page'    => $paginator->lastPage(),
        ];
    }
}
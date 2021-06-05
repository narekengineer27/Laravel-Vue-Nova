<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(title="APP API", version="1.0")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return int
     */
    protected function perPage()
    {
        return request()->get('perPage') ?? config('settings.pagination.perPage');
    }

    /**
     * @param $data
     * @param int $responseCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($data, $responseCode = 200)
    {
        if (isset($data->resource) && $data->resource instanceof LengthAwarePaginator) {
            return response()->json($this->convertPaginatorToResponse($data), $responseCode);
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], $responseCode);
    }

    private function convertPaginatorToResponse($data)
    {
        $paginator = (new PaginatedResourceResponse($data));
        $data = $paginator->toResponse(request())->getData();
        return [
                'status' => 'ok',
                'meta' => $data->meta,
                'links' => $data->links,
                'data' => $data->data
        ];
    }


}

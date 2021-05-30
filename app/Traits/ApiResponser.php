<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser {

    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(
            [
                'error' => $message,
                'code' => $code
            ],
            $code

        );
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->showEmptyCollection('No se encontó información para el recurso solicitado');
        }

        $collection = $this->filterData($collection);
        $collection = $this->sortData($collection);
        $collection = $this->paginate($collection);
        //$collection = $this->cacheResponse($collection);

        return response()->json(
            $collection,
            $code
        );
    }

    protected function showOne(Model $instance, $code = 200)
    {
        return response()->json(['data' => $instance], $code);
    }

    protected function showEmptyCollection($message, $code = 404)
    {
        return response()->json(
            [
                'message' => $message,
                'data' => []
            ],
            $code
        );
    }

    protected function filterData(Collection $collection)
    {
        if (empty(request()->query())) {
            return $collection;
        }

        foreach (request()->query() as $query => $value) {
            if (!$query == "sort_by") {
                $collection = $collection->where($query, $value);
            }
        }

        return $collection;
    }

    protected function sortData(Collection $collection)
    {
        if (request()->has('sort_by')) {
            $attribute = request()->sort_by;
            $collection = $collection->sortBy($attribute);
        }

        return $collection;
    }

    protected function paginate(Collection $collection)
    {
        $request = [
            'pre_page' => 'integer|min:2|max:50'
        ];
        Validator::validate(request()->all(), $request);

        $prePage = 15;
        if (request()->has('pre_page')) {
            $prePage = request()->pre_page;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $results = $collection->slice( ($currentPage - 1) * $prePage, $prePage )->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $prePage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function showMessage($message, $code = 200)
    {
        return response()->json(
            [
                'message' => $message,
                'code' => $code
            ],
            $code
        );
    }

    protected function cacheResponse($data)
    {
        $ttl = 15;
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams);

        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";

        return Cache::remember($fullUrl, $ttl, function () use($data) {
            return $data;
        });

    }

}

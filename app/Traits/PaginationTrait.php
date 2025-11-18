<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    protected function paginateModel(
        Model $model,
        array $columns = ['*'],
        array $relations = [],
        int $limit = 10,
        array $clause = [],
        array $search = [],
        array $orSearch = [],
        array $relationsCount = [],
        array $relationalSearch = [],
        array $orderBy = ['id', 'desc']
    ): LengthAwarePaginator {
        $query = $model->with($relations)->select($columns);

        if (!empty($relationsCount)) {
            $query->withCount($relationsCount);
        }

        if (!empty($clause)) {
            $query->where($clause);
        }

        $query->where(function (Builder $query) use ($search, $orSearch) {
            if (!empty($search)) {
                $query->where($search);
            }
            foreach ($orSearch as $item) {
                $query->orWhere([$item]);
            }
        });

        if (!empty($relationalSearch)) {
            foreach ($relationalSearch as $method => $search) {
                $query->whereHas($method, function ($query) use ($search) {
                    [$column, $condition, $value] = $search;
                    $query->where($column, $condition, $value);
                });
            }
        }

        [$id, $direction] = $orderBy;
        $query->orderBy($id, $direction);

        return $query->paginate($limit);
    }
}

<?php

namespace App\Http\Services;

use App\Models\Buku;
use App\Models\User;
use App\Models\Pinjam;
use App\Models\Kembali;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class QueryService
{

    // protected array $models = [
    //     'user' => User::class,
    //     'buku' => Buku::class,
    //     'category' => Category::class,
    //     'kembali' => Kembali::class,
    //     'pinjam' => Pinjam::class,
    // ];

    // public function getModel($model)
    // {
    //     return $this->models[$model] ?? null;
    // }

    // public function getModel($model){
    //     $classModel = "\\App\\Models\\" . Str::ucfirst(Str::camel($model));
    //     return $classModel;
    // }

    public function getQuery($model, Request $request): Builder
    {
        try {
            $modelClass = '\\App\\Models\\' . Str::studly($model);
    
            if (!class_exists($modelClass)) {
                abort(404, 'Model not found');
            }
    
            $query = $modelClass::query();
    
            $method = Str::camel($model) . 'Query';
    
            if (method_exists($this, $method)) {
                return $this->$method($query, $request);
            }
    
            return $query;
        } catch (\Exception $e) {
            abort(500, 'Error processing request.');
        }
    }

    protected function bukuQuery(Builder $query, Request $request): Builder
    {
        $filters = $request->only(Buku::getAllowedFields('filter'));

        if (!empty($filters)) {
            if (isset($filters['category'])) {
                $query->whereHas('category', function ($q) use ($filters) {
                    $q->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$filters['category']}%"]);
                });
                unset($filters['category']);
            }
        }

        $search = $request->input('search');
        if ($search) {
            $allowedSearch = Buku::getAllowedFields('search');
            $query->where(function ($q) use ($search, $allowedSearch) {
                foreach ($allowedSearch as $field) {
                    $q->orWhereRaw("LOWER($field) LIKE LOWER(?)", ["%{$search}%"]);
                }
            });
        }

        // return $query->with((new Buku)->getRelations())
        //     ->orderByRaw('LENGTH(title), title')
        //     ->where('is_pinjam', false);

        return $query->with((new Buku)->getRelations())
            ->orderBy('created_at','DESC')
            ->where('is_pinjam', false);
    }

    protected function categoryQuery(Builder $query, Request $request): Builder
    {
        $query->select('name', 'description');

        $search = $request->input('search');
        if (!empty($search)) {
            $query->where('name', 'ILIKE', "%{$search}%");
        }

        return $query->orderBy('name', 'asc');
    }

    protected function kembaliQuery(Builder $query, Request $request): Builder
    {
        $filters = $request->only(Kembali::getAllowedFields('filter'));
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    if ($key === 'denda') {
                        $query->where('denda', $value === 'ada' ? '>' : '=', 0);
                    } else {
                        $query->whereRaw("LOWER($key) LIKE LOWER(?)", ["%{$value}%"]);
                    }
                }
            }
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('peminjaman.userPinjam', function ($userQuery) use ($search) {
                    $userQuery->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$search}%"]);
                })->orWhereHas('peminjaman.buku', function ($bukuQuery) use ($search) {
                    $bukuQuery->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$search}%"]);
                });
            });
        }

        return $query->with((new Kembali)->getRelations())->orderBy('created_at', 'DESC');
    }

    protected function pinjamQuery(Builder $query, Request $request): Builder
    {
        $filters = $request->only(Pinjam::getAllowedFields('filter'));

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    if (in_array($key, ['tgl_pinjam', 'tgl_kembali'])) {
                        $query->whereDate($key, $value);
                    } else {
                        $query->where($key, 'LIKE', "%{$value}%");
                    }
                }
            }
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('userPinjam', function ($userQuery) use ($search) {
                    $userQuery->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$search}%"]);
                })->orWhereHas('buku', function ($bukuQuery) use ($search) {
                    $bukuQuery->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$search}%"]);
                });
            });
        }

        return $query->with((new Pinjam)->getRelations())->orderBy('created_at', 'DESC');
    }
}

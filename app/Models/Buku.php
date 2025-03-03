<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buku extends Model
{
    /** @use HasFactory<\Database\Factories\BukuFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publish_date',
        'is_pinjam',
        'category',
        'image'
    ];

    protected static $rules = [ // Validation rules
        'title' => 'required|string|max:255',
        'isbn' => 'required|string|max:20|unique:bukus,isbn', // Example unique rule
        'author' => 'required|string|max:255',
        'publish_date' => 'required|date',
        'category' => 'required|exists:categories,id', // Ensure category exists
        'image'=> 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ];

    protected static $is_add = ['title', 'isbn', 'author', 'publish_date', 'category','image'];
    protected static $is_edit = ['title', 'author', 'category','image']; 
    protected static $is_delete = ['title', 'isbn', 'author', 'publish_date', 'category','image'];
    protected static $is_filter = ['category'];
    protected static $is_search = ['title', 'author'];
    

    // public function scopeIsFilter(Builder $query){
    //     return $query->with(['category:id,name,description'],['peminjaman:id,'])
    //             ->orderBy('created_at', 'DESC')
    //             ->where('is_pinjam',false);
    // }

    public static function getAllowedFields($type)
    {
        return match ($type) {
            'add' => self::$is_add,
            'edit' => self::$is_edit,
            'delete' => self::$is_delete,
            'filter' => self::$is_filter,
            'search' => self::$is_search,
            default => [],
        };
    }

    public static function getValidationRules($type,$bukuId = null)
    {
        $allowedFields = self::getAllowedFields($type);
        $rules = [];

        foreach ($allowedFields as $field) {
            if (isset(self::$rules[$field])) {
                $rules[$field] = self::$rules[$field];
            }
        }

        if ($type === 'add') {
            $rules['isbn'] = 'required|string|max:20|unique:bukus,isbn';
        } elseif ($type === 'edit') {
            $rules['isbn'] = 'nullable|string|max:20|unique:bukus,isbn,' . $bukuId;
        }

        return $rules;
    }

    // public function scopeIsFilter(Builder $query, array $filters = [])
    // {
    //     $allowedFilters = self::$is_filter;

    //     foreach ($filters as $key => $value) {
    //         if (in_array($key, $allowedFilters) && !empty($value)) {
    //             $query->whereRaw("LOWER($key) LIKE LOWER(?)", ["%{$value}%"]);
    //         }
    //     }

    //     return $query->orderBy('created_at', 'DESC')
    //         ->where('is_pinjam', false);
    // }


    // public function scopeSearch(Builder $query, $search)
    // {
    //     return $query->where(function ($q) use ($search) {
    //         $q->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$search}%"])
    //         ->orWhere('isbn', 'LIKE', "%{$search}%")
    //         ->orWhere('author', 'LIKE', "%{$search}%");
    //     });
    // }


    // public function scopeIsFilter(Builder $query, array $filters = [])
    // {
    //     $allowedFilters = $this->allowedFilters;

    //     foreach ($filters as $key => $value) {
    //         if (in_array($key, $allowedFilters) && !empty($value)) {
    //             if ($key === 'title') {
    //                 $query->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$value}%"]);
    //             } else {
    //                 $query->where($key, 'LIKE', "%{$value}%");
    //             }
    //         }
    //     }

    //     $query->orderBy('created_at', 'DESC')
    //         ->where('is_pinjam', false);    
    // }


    /**
     * Get the category that owns the Buku
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category');
    }


    /**
     * Get all of the peminjaman for the Buku
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Pinjam::class, 'id_buku', 'id');
    }

    public function getRelations()
    {
        return [
            'category' => function ($query) {
                $query->select('id', 'name'); 
            },
            'peminjaman' => function ($query) {
                $query->select('id', 'id_buku', 'id_user')
                    ->with([
                        'userPinjam:id,name'
                    ]); 
            }
        ];
    }
}
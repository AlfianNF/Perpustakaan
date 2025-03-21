<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pinjam extends Model
{
    /** @use HasFactory<\Database\Factories\PinjamFactory> */
    use HasFactory;

    protected $fillable = [
        'id_buku','id_user','tgl_pinjam','tgl_kembali','status'
    ];

    protected static $is_add = ['id_buku', 'id_user', 'tgl_pinjam', 'tgl_kembali','status'];
    protected static $is_edit = ['tgl_pinjam', 'tgl_kembali','status']; 
    protected static $is_delete = ['id_buku', 'id_user', 'tgl_pinjam', 'tgl_kembali','status'];
    protected static $is_filter = ['tgl_pinjam', 'tgl_kembali','status'];
    protected static $is_search = ['id_buku', 'id_user'];

    protected static $rules = [ // Validation rules
        'id_buku' => 'required',
        'id_user' => 'required', 
        'tgl_pinjam' => 'required|date',
        'tgl_kembali' => 'required|date',
        'status' => 'nullable',
    ];
    

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

    public static function getValidationRules($type)
    {
        $allowedFields = self::getAllowedFields($type);
        $rules = [];

        foreach ($allowedFields as $field) {
            if (isset(self::$rules[$field])) {
                $rules[$field] = self::$rules[$field];
            }
        }
        return $rules;
    }

    // public function scopeSearch(Builder $query, $search)
    // {
    //     return $query->where(function ($q) use ($search) {
    //         $q->whereRaw("CAST(id_buku AS TEXT) LIKE ?", ["%{$search}%"])
    //         ->orWhere('id_user', 'LIKE', "%{$search}%");
    //     });
    // }

    // public function scopeIsFilter(Builder $query, array $filters = [])
    // {
    //     $allowedFilters = $this->getAllowedFilters();

    //     foreach ($filters as $key => $value) {
    //         if (in_array($key, $allowedFilters) && !empty($value)) {
    //             if ($key === 'id_buku') {
    //                 $query->whereRaw("CAST(id_buku AS TEXT) ILIKE ?", ["%{$value}%"]);
    //             } else {
    //                 $query->where($key, 'ILIKE', "%{$value}%");
    //             }
    //         }
    //     }

    //     return $query->orderBy('created_at', 'DESC');
    // }



    /**
     * Get the userPinjam that owns the Peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userPinjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Get the buku that owns the Peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id');
    }

    /**
     * Get the kembali associated with the Pinjam
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kembali(): HasOne
    {
        return $this->hasOne(Kembali::class, 'id_pinjam', 'id');
    }   

    public function getRelations()
    {
        return [
            'userPinjam' => function ($query) {
                $query->select('id', 'name'); 
            },
            'buku' => function ($query) {
                $query->select('id','title','isbn', 'author','category','publish_date')
                    ->with([
                        'category:id,name' 
                    ]); 
            }
        ];
    }
}

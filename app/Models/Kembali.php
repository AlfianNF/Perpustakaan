<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kembali extends Model
{
    /** @use HasFactory<\Database\Factories\KembaliFactory> */
    use HasFactory;
    protected $fillable=[
        'id_buku',
        'id_user',
        'id_pinjam',
        'tgl_kembali',
        'denda'
    ];

    protected static $rules = [ 
        'id_buku' => 'required|exists:bukus,id',
        'id_user' => 'required|exists:users,id', 
        'id_pinjam' => 'required|exists:pinjams,id',
        'tgl_kembali' => 'required|date',
        'denda' => 'nullable|decimal', 
    ];

    protected static $is_add = ['id_buku', 'id_user', 'id_pinjam', 'tgl_kembali', 'denda'];
    protected static $is_edit = ['denda']; 
    protected static $is_delete = ['id_buku', 'id_user', 'id_pinjam', 'tgl_kembali', 'denda'];
    protected static $is_filter = ['denda'];
    protected static $is_search = ['id_buku', 'id_user'];
    

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
     * Get the peminjaman that owns the Kembali
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjam::class, 'id_pinjam','id');
    }

    public function getRelations()
    {
        return [
            'peminjaman' => function ($query) {
                $query->select('id', 'id_buku', 'id_user','tgl_kembali')
                    ->with([
                        'userPinjam:id,name',
                        'buku:id,title,category'
                    ]); 
            }
        ];
    }
}

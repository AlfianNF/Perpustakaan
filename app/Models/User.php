<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'password',
        'no_induk',
        'email',
        'is_admin',
        'image'
    ];

    // public function setIsAdminAttribute($value)
    // {
    //     $this->attributes['is_admin'] = $value ?? false;
    // }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static $is_add = ['name', 'no_induk', 'email', 'is_admin','image','password'];
    protected static $is_edit = ['name', 'email', 'is_admin','image','password']; 
    protected static $is_delete = ['name', 'no_induk', 'email', 'is_admin','image','password'];
    protected static $is_filter = ['is_admin'];
    protected static $is_search = ['name','email','no_induk'];
    

    protected static $rules = [ // Validation rules
        'name' => 'required|string|max:255',
        'no_induk' => 'required|unique:users,no_induk', 
        'email' => 'nullable',
        'password' => 'required|string|min:8',
        'is_admin' => 'nullable',
        'image' => 'nullable'
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

    public static function getValidationRules($type,$userId = null)
    {
        $allowedFields = self::getAllowedFields($type);
        $rules = [];

        foreach ($allowedFields as $field) {
            if (isset(self::$rules[$field])) {
                $rules[$field] = self::$rules[$field];
            }
        }

        if ($type === 'add') {
            $rules['no_induk'] = 'required|unique:users,no_induk';
        } else if ($type === 'edit') {
            $rules['no_induk'] = 'nullable|unique:users,no_induk,' . $userId;
            $rules['password'] = 'nullable|string|min:8'; 
        }
        return $rules;
    }

    // public function scopeSearch(Builder $query, $search)
    // {
    //     return $query->where(function ($q) use ($search) {
    //         $q->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$search}%"]);
    //     });
    // }


    // public function scopeIsFilter(Builder $query, array $filters = [])
    // {
    //     $allowedFilters = ['name'];

    //     foreach ($filters as $key => $value) {
    //         if (in_array($key, $allowedFilters) && !empty($value)) {
    //             if ($key === 'name') {
    //                 $query->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$value}%"]);
    //             } else {
    //                 $query->where($key, 'LIKE', "%{$value}%");
    //             }
    //         }
    //     }

    //     return $query;
    // }

    /**
     * Get all of the pinjam for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pinjam(): HasMany
    {
        return $this->hasMany(Pinjam::class, 'id_user', 'id');
    }
}

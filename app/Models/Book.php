<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'number_of_pages',
        'is_active'
    ];


    public function scopeIsActive($query,$is_active){
            if ($is_active == 1) {
                return $query->where('is_active', 1);
            } elseif ($is_active == 0) {
                return $query->where('is_active', 0);
            }
            return $query;
    }

    public static function getPaginatedData($perPage = 10, $page = 1){
        return  Book::isActive(request()->is_active ?? -1)
                ->orderBy('id','DESC')
                ->paginate($perPage, ['*'], 'page', $page);
    }

}

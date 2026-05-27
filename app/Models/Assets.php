<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Assets extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'value',
        'type',
        'user_id'
    ];
    
    public function assets()
    {
        return $this->hasMany(Assets::class, 'user_id', 'id');
    }

}

<?php

namespace App\Data\Models\Posts;


use App\Data\Models\BaseModel;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ReactionsModel extends BaseModel
{
    // use SoftDeletes;

    public $timestamps = true;
    public $incrementing = true;
    protected $table = 'reactions';

    public $casts = [
        'id' => 'int'
    ];

    public $fillable = [
        'post',
        'user',
        'reaction',
    ];

    public $hidden = [];

    public $rules = [
        'post' => 'sometimes|required',
        'user' => 'sometimes|required',
        'reaction' => 'sometimes|required'
    ];

     public function transactions()
     {
         return $this->morphMany();
     }
}

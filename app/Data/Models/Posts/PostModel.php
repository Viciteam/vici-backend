<?php

namespace App\Data\Models\Posts;


use App\Data\Models\BaseModel;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PostModel extends BaseModel
{
    // use SoftDeletes;

    public $timestamps = true;
    public $incrementing = true;
    protected $table = 'posts';

    public $casts = [
        'id' => 'int'
    ];

    public $fillable = [
        'user',
        'parent',
        'level',
        'content',
        'position',
        'position_id',
        'ispublic',
    ];

    public $hidden = [];

    public $rules = [
        'user' => 'sometimes|required',
        'parent' => 'sometimes|required',
        'level' => 'sometimes|required',
        'content' => 'sometimes|required',
        'position' => 'sometimes|required',
        'position_id' => 'sometimes|required',
        'ispublic' => 'sometimes|required',
    ];

     public function transactions()
     {
         return $this->morphMany();
     }
}

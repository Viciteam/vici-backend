<?php

namespace App\Data\Models\Posts;


use App\Data\Models\BaseModel;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PostMetaModel extends BaseModel
{
    // use SoftDeletes;

    public $timestamps = true;
    public $incrementing = true;
    protected $table = 'posts_meta';

    public $casts = [
        'id' => 'int'
    ];

    public $fillable = [
        'post',
        'meta_key',
        'meta_value',
    ];

    public $hidden = [];

    public $rules = [
        'post' => 'sometimes|required',
        'meta_key' => 'sometimes|required',
        'meta_value' => 'sometimes|required'
    ];

     public function transactions()
     {
         return $this->morphMany();
     }
}

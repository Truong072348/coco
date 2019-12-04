<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['title', 'comments_user_id_foreign', 'comments_product_id_foreign', 'comment'];
    /**
     * @var string
     */
    protected $table = 'comments';
    /**
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * @var bool
     */
    public $timestamps = true;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'comments';


    protected $fillable = [
        'id',
        'comment',
        // weitere Felder:
        // 'users_id',
        // 'blog_id',
        // 'created_at',
    ];
}

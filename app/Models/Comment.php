<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'user_id',
        'post_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function likes()
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('type', 1);
    }

    public function dislikes()
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('type', -1);
    }
}

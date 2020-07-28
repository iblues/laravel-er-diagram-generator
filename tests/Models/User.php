<?php

namespace BeyondCode\ErdGenerator\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    public function doSomething($foo)
    {

    }

    /**
     * posts relation
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * user's avatar
     */
    public function avatar()
    {
        return $this->hasOne(Avatar::class);
    }

    /**
     * user's comments
     */
    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }

}

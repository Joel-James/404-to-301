<?php

namespace DuckDev\EloquentWP\WP;


use DuckDev\EloquentWP\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'comment_ID';

    /**
     * Post relation for a comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function post()
    {
        return $this->hasOne('DuckDev\EloquentWP\WP\Post');
    }
}
<?php

namespace DuckDev\EloquentWP\WP;


use DuckDev\EloquentWP\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'ID';
    protected $timestamp = false;

    public function meta()
    {
        return $this->hasMany('DuckDev\EloquentWP\WP\UserMeta', 'user_id');
    }
}
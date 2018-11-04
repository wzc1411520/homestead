<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //户模型中新增与话题模型的关联
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
    
    //判断操作的用户是否是当前的用户
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }
    
    //一个用户有很多的回复
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}

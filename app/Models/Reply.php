<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content'];
    protected $table = 'replies';

    //一条回复属于一个作者    一条回复也属于一个话题
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

}

<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //一个话题有很多的回复
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

//这里我们使用了 Laravel 本地作用域 。
//本地作用域允许我们定义通用的约束集合以便在应用中复用。
//要定义这样的一个作用域，只需简单在对应 Eloquent 模型方法前加上一个 scope 前缀，
//作用域总是返回 查询构建器。
//一旦定义了作用域，则可以在查询模型时调用作用域方法。
//在进行方法调用时不需要加上 scope 前缀。
//如以上代码中的 recent() 和 recentReplied()。
    public function scopeWithOrder($query, $order)
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }
        // 预加载防止 N+1 问题
        return $query->with('user', 'category');
    }
    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }
//新建方法 link()来生成模型 URL，然后将所有调用了 route('topics.show', $topic->id) 的地方修改为 $topic->link(
    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }
}

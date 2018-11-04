<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;
use Illuminate\Support\Facades\Notification;
use think\model\Relation;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        $reply->content = clean($reply->content);
    }

    public function created(Reply $reply)
    {
        $reply->topic->increment('reply_count', 1);
        // 通知作者话题被回复了
        // notify 方法需要一个通知实例做参数：
        $reply->topic->user->notify(new TopicReplied($reply));
        //第二种通知方式
//        $user = $reply->topic->user;
//        Notification::send($user, new TopicReplied($reply));
    }

    public function updating(Reply $reply)
    {
        $reply->content = clean($reply->content);
    }

    public function saving(Reply $reply)
    {
        $reply->content = clean($reply->content);
    }

    public function deleted(Reply $reply)
    {
        $reply->topic->decrement('reply_count', 1);
    }
}
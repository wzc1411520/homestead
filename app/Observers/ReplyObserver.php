<?php

namespace App\Observers;

use App\Models\Reply;
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
    }

    public function updating(Reply $reply)
    {
        $reply->content = clean($reply->content);
    }

    public function saving(Reply $reply)
    {
        $reply->content = clean($reply->content);
    }
}
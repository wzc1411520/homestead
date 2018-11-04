<?php

namespace App\Providers;

use App\Models\Reply;
use App\Models\Topic;
use App\Observers\ReplyObserver;
use App\Observers\TopicObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *运行所有应用.
     * @return void
     */
    public function boot()
    {
//        Carbon 是继承自 PHP DateTime 类 的子类，
        Carbon::setLocale('zh');
        //创建的观察期，需要注册管擦器
        Topic::observe(TopicObserver::class);
        Reply::observe(ReplyObserver::class);

    }

    /**
     * Register any application services.
     *注册服务提供.
     * @return void
     */
    public function register()
    {
        //
    }
}

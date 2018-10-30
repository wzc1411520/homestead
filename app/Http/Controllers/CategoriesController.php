<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //方式一
    public function show_dev(Request $request,Category $category,Topic $topic)
    {
        $topics = $topic->withOrder($request->order)
            ->where('category_id',$category->id)->paginate(20);
        return view('topics.index', compact('topics', 'category'));
    }
    //方式二
    public function show(Request $request,Category $category)
    {
        $topics = Topic::withOrder($request->order)
            ->where('category_id',$category->id)->paginate(20);
        return view('topics.index', compact('topics', 'category'));
    }
}

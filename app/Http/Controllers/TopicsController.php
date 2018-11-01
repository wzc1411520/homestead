<?php

namespace App\Http\Controllers;

use App\Helper\HelperController;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic)
	{
	    //用with的方式进行预加载操作,来避免n+1的问题,对数据库读取造成压力
//		$topics = Topic::with('user', 'category')->paginate(15);
        $topics = $topic->withOrder($request->order)->paginate(20);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    //获取文章分类
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(Topic $topic,Request $request)
	{
//		$topic = Topic::create($request->all());
        $topic->fill($request->all());
        $topic->user_id = Auth::user()->id;
        $topic->save();
        return redirect()->route('topics.index')->with('success', '创建成功');
//		return redirect()->route('topics.show', $topic->id)->with('message', 'Created successfully.');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        //获取文章分类
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());
		return redirect()->route('topics.show', $topic->id)->with('success', '更新成功');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功');
	}
	
	//上传图片
    public function uploadImage(Request $request, HelperController $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }
}
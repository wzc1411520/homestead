<?php

namespace App\Http\Controllers;

use App\Helper\HelperController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * 个人页面
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users/show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //控制器中使用 authorize 方法来检验用户是否授权
        $this->authorize('update', $user);
        return view('users/edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id 更新
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        //控制器中使用 authorize 方法来检验用户是否授权
        $this->authorize('update', $user);
        $data = $request->all();
        if ($request->avatar){
            $res = HelperController::save($request->avatar,'avatars',$user->id,362);
            if ($res){
                $data['avatar'] = $res['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

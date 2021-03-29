<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);

        $this->middleware('throttle:10,10',[
            'only' => ['store']
        ]);
    }
    public function create()
    {
        return view('session.create');
    }

    public function store(Request $request)
    {
        $credntials = $this->validate($request, [
            'email' => 'email|required|max:25',
            'password' => 'required'
        ]);

        if (Auth::attempt($credntials, $request->has('remember'))) {
            if (Auth::user()->activated) {
                session()->flash('success', '欢迎回来！');
                return redirect()->route('users.show', [Auth::user()]);
            } else {
                Auth::logout();
                session()->falsh('warning', '您的账号未激活,请检查邮箱中注册邮件注册激活。');
                return redirect('/');
            }
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
         }
    }

    public function destory()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}

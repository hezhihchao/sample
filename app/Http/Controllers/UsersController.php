<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{



    //
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['show','create','store','index','confirmEmail']
        ]);

        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
            'email.blade.php' => 'required|email.blade.php|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email.blade.php' => $request->email,
            'password' => bcrypt($request->password)
        ]);

//        Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        session()->flash("success",'欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);
    }

//    protected function sendEmailConfirmationTo($user)
//    {
//        $view = 'emails.confirm';
//        $data = compact('user');
//        $from = 'aufree@yousails.com';
//        $name = 'Aufree';
//        $to = $user->email.blade.php;
//        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";
//
//        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
//            $message->from($from, $name)->to($to)->subject($subject);
//        });
//    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'johnhezhichao@126.com';
        $name = 'John';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated= true;
        $user->activation_token = null;
        $user->save();
        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request)
    {
        $this->validate($request,[
           'name'=>'required|max:50',
           'password'=>'nullable|confirmed|min:6'
        ]);

        $this->authorize('update',$user);

        $data = [];
        $data['name'] = $request->name;
        if ($request -> password){
            $data['password'] = bcrypt($request->password);
        }

        $user -> update($data);
        session()->flash('success','更新成功');
        return redirect()->route('users.show',$user->id);
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    public function destroy(User $user){

        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','用户已删除');
        return back();
    }
}

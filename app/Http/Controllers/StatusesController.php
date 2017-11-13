<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2017/11/13
 * Time: 下午4:46
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;

class StatusesController extends Controller
{

    /**
     * StatusesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);

        return redirect()->back();
    }

    public function destroy(Status $status)
    {
        $this->authorize('destroy',$status);
        $status->delete();
        session()->flash('success','微博已删除成功!');
        return redirect()->back();
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Qabul;
use Illuminate\Http\Request;

class QabulController extends Controller
{
    public function index($id)
    {
        $qabuls = Qabul::with('user','news')->where('news_id', $id)->paginate();
        return view('admin.qabul.index',compact('qabuls'));
    }
}

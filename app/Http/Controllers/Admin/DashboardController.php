<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Post;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $author_count = User::where('role_id',2)->count();
        $new_authors_today = User::where('role_id',2)
                                ->whereDate('created_at',Carbon::today())->count();

        return view('admin.dashboard',compact('author_count','new_authors_today'));
    }
}

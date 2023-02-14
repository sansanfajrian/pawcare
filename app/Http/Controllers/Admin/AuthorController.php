<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Consultation;
use App\Review;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthorController extends Controller
{
    public function index()
    { 
        $consultation_count = Consultation::where('status', 'Selesai')->count();
        $review_count = Review::all();
       $authors = User::authors()
           ->get();
       return view('admin.authors',compact('authors','consultation_count', 'review_count'));
    }

    public function destroy($id)
    {
        $author = User::findOrFail($id)->delete();
        Toastr::success('Doctor Successfully Deleted','Success');
        return redirect()->back();
    }
}

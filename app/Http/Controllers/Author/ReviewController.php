<?php

namespace App\Http\Controllers\Author;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Review;
use App\UserDoctorDetail;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $doctor = UserDoctorDetail::where('user_id', '=', $user->id)->first();
        #list of reviews
        $reviews = Review::all()->where('consultation.user_doctor_detail_id', $doctor->id);
        return view('author.review', compact('reviews'));
    }
}
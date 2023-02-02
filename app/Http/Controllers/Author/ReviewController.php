<?php

namespace App\Http\Controllers\Author;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $doctor_id = $user->userDoctorDetails->id;
        #list of reviews
        $reviews = Review::with(['consultation.user', 'consultation.userDoctorDetail'])
            ->where('consultation.user_doctor_detail_id', $doctor_id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->consultation->user->name,
                    'star' => $item->star,
                    'review' => $item->review
                ];
            });
        return view('author.review', [
            'data' => $reviews
        ]);
    }
}

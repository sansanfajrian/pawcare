<?php

namespace App\Http\Controllers\Author;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Review;

class ReviewController extends Controller
{

    public function index()
    {
        #list of reviews
        $reviews = Review::with(['consultation.user', 'consultation.userDoctorDetail'])
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

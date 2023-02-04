<?php

namespace App\Http\Controllers\Author;

use App\Consultation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Review;
use App\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
      
        return view('author.dashboard');
    }

    public function showConsultation($id)
    {
        $data = Consultation::with(['user', 'userDoctorDetail.user'])
            ->find($id);

        return view('author.show.consultation', [
            'data' => $data->toArray()
        ]);
    }

    public function showReview($id)
    {
        $data = Review::with(['consultation.user', 'consultation.userDoctorDetail.user'])
            ->find($id);
        return view('author.show.review', [
            'data' => $data->toArray()
        ]);
    }
}

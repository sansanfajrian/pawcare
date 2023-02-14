<?php

namespace App\Http\Controllers\Author;

use App\Consultation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Review;
use App\User;
use App\UserDoctorDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctor = UserDoctorDetail::where('user_id', '=', $user->id)->first();
        $consultation_count = Consultation::with(['userDoctorDetail.user', 'user'])->where('user_doctor_detail_id', $doctor->id)->where('status', 'Selesai')->orWhere('status', 'Selesai Mengulas')->count();
        $review_count = Review::whereHas('consultation', function($q) {
            $q->where('user_doctor_detail_id',  UserDoctorDetail::where('user_id', '=', Auth::user()->id)->first()->id);
        })->count();
        return view('author.dashboard', compact('consultation_count','review_count'));
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

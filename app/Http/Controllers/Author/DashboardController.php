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

        $doctor_id = $user->userDoctorDetails->id;

        #list of consultations
        $consultations = Consultation::with(['userDoctorDetail.user', 'user'])
            ->where('user_doctor_detail_id', $doctor_id)
            ->where('status', '!=', "Done")
            ->get()
            ->map(function ($item) {
                $item = $item->toArray();
                return [
                    'id' => $item['id'],
                    'patient_name' => $item['user']['name'],
                    'gender' => $item['user']['gender'],
                    'doctor_name' => $item['user_doctor_detail']['user']['name'],
                    'status' => $item['status'],
                ];
            });

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
        return view('author.dashboard', [
            'consultations' => $consultations,
            'reviews' => $reviews
        ]);
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

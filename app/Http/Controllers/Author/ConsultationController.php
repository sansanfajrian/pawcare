<?php

namespace App\Http\Controllers\Author;

use App\Consultation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
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

        #list of consultations
        $consultations = Consultation::with(['userDoctorDetail.user', 'user'])->get()
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

        return view('author.consultation', [
            'data' => $consultations
        ]);
    }
}

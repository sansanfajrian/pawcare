<?php

namespace App\Http\Controllers\Author;

use App\Consultation;
use App\UserDoctorDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $doctor = UserDoctorDetail::where('user_id', '=', $user->id)->first();

        #list of consultations
        $consultations = Consultation::with(['userDoctorDetail.user', 'user'])
            ->where('user_doctor_detail_id', $doctor->id)
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

        return view('author.consultation', [
            'data' => $consultations
        ]);
    }
}

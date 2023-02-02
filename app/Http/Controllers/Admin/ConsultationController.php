<?php

namespace App\Http\Controllers\Admin;

use App\Consultation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsultationController extends Controller
{
    public function index()
    {
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

        return view('admin.consultation', [
            'data' => $consultations
        ]);
    }
}

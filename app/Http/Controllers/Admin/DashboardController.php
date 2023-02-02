<?php

namespace App\Http\Controllers\Admin;

use App\Consultation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserDoctorDetail;

class DashboardController extends Controller
{
    public function index()
    {
        $author_count = User::where('role_id', 2)->count();
        $new_authors_today = User::where('role_id', 2)
            ->whereDate('created_at', Carbon::today())->count();

        #list of users
        $doctors = UserDoctorDetail::with(['user'])->get()
            ->map(function ($item) {
                return [
                    'name' => $item->user->name,
                    'price' => $item->price,
                    'phone' => $item->user->phone,
                    'description' => $item->description,
                    'status' => $item->status
                ];
            });

        #list of users
        $users = User::where('role_id', 3)->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'price' => $item->email,
                    'phone' => $item->phone,
                    'created_at' => $item->created_at,
                ];
            });

        #list of consultations
        $consultations = Consultation::with(['userDoctorDetail.user', 'user'])->get()
            ->map(function ($item) {
                $item = $item->toArray();
                return [
                    'patient_name' => $item['user']['name'],
                    'gender' => $item['user']['gender'],
                    'doctor_name' => $item['user_doctor_detail']['user']['name'],
                    'status' => $item['status'],
                ];
            });

        return view('admin.dashboard', compact('author_count', 'new_authors_today', 'doctors', 'users', 'consultations'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Consultation;
use App\User;
use App\AccountApproval;
use App\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserDoctorDetail;

class DashboardController extends Controller
{
    public function index()
    {
        
        $user_count = User::where('role_id', 3)->count();
        $new_users_today = User::where('role_id', 3)
            ->whereDate('created_at', Carbon::today())->count();
        $author_count = User::where('role_id', 2)->count();
        $new_authors_today = User::where('role_id', 2)
            ->whereDate('created_at', Carbon::today())->count();
        $consultation_count = Consultation::where('status', 'Selesai')->count();
        /*$earning = Payment::select(['SUM(user_doctor_details.price)'])
        ->join('consultations','consultations.id', 'consultations.user_doctor_detail_id')
        ->join('user_doctor_details','consultations.user_doctor_detail_id','user_doctor_details.id')
        ->get();*/
        $approvals = AccountApproval::all();

        #list of 
        $users = User::where('role_id', 3)->get();
        $doctors = UserDoctorDetail::with(['user'])->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->user->name,
                    'price' => $item->price,
                    'phone' => $item->user->phone,
                    'description' => $item->description,
                    'status' => $item->status
                ];
            });

        return view('admin.dashboard', compact('user_count','new_users_today','author_count', 'new_authors_today','users','doctors','consultation_count','approvals'));
    }

    public function showDoctor($id)
    {
        $data = UserDoctorDetail::with(['user'])
            ->find($id);

        // dd($data->toArray());
        return view('admin.show.doctor', [
            'data' => $data
        ]);
    }
    public function showUser($id)
    {
        $data = User::find($id);

        return view('admin.show.user', [
            'data' => $data
        ]);
    }
    public function showConsultation($id)
    {
        $data = Consultation::with(['user', 'userDoctorDetail.user'])
            ->find($id);

        // dd($data->toArray());
        return view('admin.show.consultation', [
            'data' => $data->toArray()
        ]);
    }
}

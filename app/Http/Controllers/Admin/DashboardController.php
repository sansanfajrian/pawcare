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
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch the roles from MongoDB
        $roles = DB::collection('roles')->get();
        $thirdRole = $roles->where('slug', 'user')->first();
        $secondRole = $roles->where('slug', 'dokter')->first();

        $user_count = User::where('role_id', $thirdRole['_id'])->count();
        $new_users_today = User::where('role_id', $thirdRole['_id'])
            ->whereDate('created_at', Carbon::today())->count();
        $author_count = User::where('role_id', $secondRole['_id'])->count();
        $new_authors_today = User::where('role_id', $secondRole['_id'])
            ->whereDate('created_at', Carbon::today())->count();
        $consultation_count = Consultation::where('status', 'Selesai')->orWhere('status', 'Selesai Mengulas')->count();

        // $earning = Payment::select(['SUM(user_doctor_details.price)'])
        // ->join('consultations','consultations.id', 'consultations.user_doctor_detail_id')
        // ->join('user_doctor_details','consultations.user_doctor_detail_id','user_doctor_details.id')
        // ->get();

        $payment = Payment::all();
        $approvals = AccountApproval::all();
        $new_doctors_today = UserDoctorDetail::whereDate('created_at', Carbon::today())->count();

        // List of users with the third role
        $users = User::where('role_id', $thirdRole['_id'])->get();
        $mytime = Carbon::today()->toDateString();

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

        return view('admin.dashboard', compact('user_count', 'new_users_today', 'new_doctors_today', 'payment', 'mytime', 'author_count', 'new_authors_today', 'users', 'doctors', 'consultation_count', 'approvals'));
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

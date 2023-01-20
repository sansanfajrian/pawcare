<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use App\UserDoctorDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Akun tidak ditemukan!'
            ]);
        }

        if(Hash::check($request->password, $user->password)) {
            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'status' => 'OK',
                'message' => 'Berhasil login!',
                'token' => $token
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Email dan Password tidak cocok!'
            ]);
        }
    }

    public function registerUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $message = "";
            if(!$request->message){
                $message = "-";
            }else{
                $message = $request->message;
            }

            $userId = User::insertGetId([
                    'name' => $request->name,
                    'role_id' => 3,
                    'email' => $request->email,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'password' => Hash::make($request->password),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            DB::commit();
            $isSuccess = true;
        } catch (Exception $e) {
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil Membuat Akun!' : 'Gagal Membuat Akun!'
        ]);
    }

    public function doctorList(Request $request)
    {
        $token = $this::getCurrentToken($request);
        $fetchDoctorList = UserDoctorDetail::select([
                'user_doctor_details.*',
                'users.name AS name'
            ])
            ->leftJoin('users', 'users.id', 'user_doctor_details.user_id')
            ->orderBy('users.name', 'ASC')
            ->get();

        $doctorList = [];
        foreach($fetchDoctorList as $doctor) {
            $doctorList[] = [
                'name' => $doctor->user->name,
                'address' => $doctor->user->address,
                'price' => $doctor->price,
                'description' => $doctor->description
            ];
        }
        
        return response()->json([
            'status' => 'OK',
            'results' => [
                'doctor_list' => $doctorList
            ]
        ]);
    }
}

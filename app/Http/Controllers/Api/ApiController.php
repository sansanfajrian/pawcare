<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use App\UserDoctorDetail;
use App\Banner;
use App\Consultation;
use App\Payment;
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
                    'phone' => $request->phone,
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


    public function editUser(Request $request, $id)
    {
        $token = $this::getCurrentToken($request);
		$user = User::where('id', $token->user_id)->first();
		if(empty($user)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
		}

        $isSuccess = false;

        DB::beginTransaction();
        try{
            $user = User::find($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'phone' => $request->phone,
                'image' => $request->image,
                'banner' => $request->banner
            ]);
            DB::commit();
            $isSuccess = true;
        }
        catch(Exception $e){
            DB::rollback();
            $isSuccess = false; 
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil Mengedit User!' : 'Gagal Mengedit User!',
            'result' => [
                'users' => $user
            ]
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
                'id' => $doctor->user->id,
                'user_doctor_detail_id' => $doctor->id,
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

    public function doctorDetail(Request $request, $id)
    {
        $token = $this::getCurrentToken($request);
        $fetchDoctorList = UserDoctorDetail::select([
                'user_doctor_details.*',
                'users.name AS name',
                'users.address AS address'
            ])
            ->leftJoin('users', 'users.id', 'user_doctor_details.user_id')
            ->orderBy('users.name', 'ASC')
            ->where('user_doctor_details.user_id', '=', $id)
            ->get();

        $doctorList = [];
        foreach($fetchDoctorList as $doctor) {
            $doctorList[] = [
                'id' => $doctor->user->id,
                'user_doctor_detail_id' => $doctor->id,
                'name' => $doctor->user->name,
                'address' => $doctor->user->address,
                'image'=> $doctor->user->image,
                'banner'=> $doctor->user->banner,
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

    public function bannerList(Request $request)
    {
        $token = $this::getCurrentToken($request);
        $fetchBannerList = Banner::select([
                'banners.*'
            ])
            ->orderBy('banners.sequence', 'ASC')
            ->get();

        $bannerList = [];
        foreach($fetchBannerList as $banner) {
            $bannerList[] = [
                'sequence' => $banner->sequence,
                'image' => asset('uploads/item/'.$banner->image)
            ];
        }
        
        return response()->json([
            'status' => 'OK',
            'results' => [
                'banner_list' => $bannerList
            ]
        ]);
    }

    public function consultation(Request $request)
    {
        $token = $this::getCurrentToken($request);
		$user = User::where('id', $token->user_id)->first();
		if(empty($user)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
		}

        $isSuccess = false;

        DB::beginTransaction();
        try {
            $message = "";
            if(!$request->message){
                $message = "-";
            }else{
                $message = $request->message;
            }

            $consultationId = Consultation::insertGetId([
                'user_id' => $user->id,
                'user_doctor_detail_id' => $request->user_doctor_detail_id,
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
            'message' => $isSuccess ? 'Berhasil konsultasi dengan dokter!' : 'Gagal untuk konsultasi dengan dokter!'
        ]);
    }

    public function payment(Request $request)
    {
        $token = $this::getCurrentToken($request);
		$user = User::where('id', $token->user_id)->first();
		if(empty($user)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
		}

        $isSuccess = false;

        DB::beginTransaction();
        try {
            $message = "";
            if(!$request->message){
                $message = "-";
            }else{
                $message = $request->message;
            }

            $paymentId = Payment::insertGetId([
                'consultation_id' => $request->consultation_id,
                'bank_name' => $request->bank_name,
                'sender_name' => $request->sender_name,
                'image' => $request->image,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $consultation_id = $request->consultation_id;
            $consultation = Consultation::find($consultation_id);
            $consultation->status = "Menunggu Persetujuan";
            $consultation->save();
            DB::commit();
            $isSuccess = true;
        } catch (Exception $e) {
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil mengirimkan bukti pembayaran!' : 'Gagal mengirimkan bukti pembayaran!'
        ]);
    }

}

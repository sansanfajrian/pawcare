<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use App\UserDoctorDetail;
use App\Banner;
use App\Consultation;
use App\Payment;
use App\Review;
use App\Jobs\ConsultationCancelPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
 
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'OK',
                'message' => 'Link forgot password sudah dikirim ke email anda!'
            ]);
        } else{
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Link forgot password anda gagal dikirim!'
            ]);
        }
    }

    public function registerUser(Request $request)
    {
        DB::beginTransaction();
        $roles = DB::collection('roles')->get();
        $thirdRole = optional($roles->get(2));
        $thirdRoleId = $thirdRole ? (string) $thirdRole['_id'] : null;
        try {
            $message = "";
            if(!$request->message){
                $message = "-";
            }else{
                $message = $request->message;
            }

            $userId = User::insertGetId([
                'name' => $request->name,
                'role_id' => $thirdRoleId,
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


    public function editUser(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $isSuccess = false;

        DB::beginTransaction();
        try{
            $user = User::find($this->getCurrentUserId($request));
            $user->name =  $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->phone = $request->phone;
            if($user->save()){
                $isSuccess = true;
                DB::commit();
            }
            $fetchUserDetail = User::where('_id',$user->_id)->get();
            $userDetail = [];
            foreach($fetchUserDetail as $users) {
                $userDetail[] = [
                    'id' => $users->_id,
                    'name' => $users->name,
                    'gender' => $users->gender,
                    'email' => $users->email,
                    'address' => $users->address,
                    'phone' => $users->phone,
                    'image' => asset('uploads/profile/'.$users->image),
                    'banner' => asset('uploads/banner/'.$users->banner),
                ];
            }
        } 
        catch(Exception $e){
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil Mengedit User!' : 'Gagal Mengedit User!',
            'result' => [
                'users' => $userDetail
            ]
        ]);
    }

    public function editBannerImage(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $isSuccess = false;


        $this->validate($request,[
            'banner' => 'mimes:jpeg,bmp,png,jpg'
        ]);

        DB::beginTransaction();
        try{
            $slug = Str::slug($request->name);
            $image = $request->file('banner');
            $user = User::find($this->getCurrentUserId($request));
            if (isset($image))
            {
                $currentDate = Carbon::now()->toDateString();
                $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                if (!file_exists('uploads/banner'))
                {
                    mkdir('uploads/banner',0777,true);
                }
                $image->move('uploads/banner',$imagename);
                
                $user->banner = $imagename;

            } else {
                $user->banner = 'default.png';
            }
            if($user->save()){
                $isSuccess = true;
                DB::commit();
            }
            $fetchUserDetail = User::where('_id',$user->_id)->get();
            $userDetail = [];
            foreach($fetchUserDetail as $users) {
                $userDetail[] = [
                    'banner' => asset('uploads/banner/'.$users->banner)
                ];
            }
        } 
        catch(Exception $e){
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil Mengedit Profile Image!' : 'Gagal Mengedit Profile Image!',
            'result' => [
                'users' => $userDetail
            ]
        ]);
    }

    public function editProfileImage(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $isSuccess = false;


        $this->validate($request,[
            'image' => 'mimes:jpeg,bmp,png,jpg'
        ]);

        DB::beginTransaction();
        try{
            $slug = Str::slug($request->name);
            $image = $request->file('image');
            $user = User::find($this->getCurrentUserId($request));
            if (isset($image))
            {
                $currentDate = Carbon::now()->toDateString();
                $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                if (!file_exists('uploads/profile'))
                {
                    mkdir('uploads/profile',0777,true);
                }
                $image->move('uploads/profile',$imagename);
                
                $user->image = $imagename;

            } else {
                $user->image = 'default.png';
            }
            if($user->save()){
                $isSuccess = true;
                DB::commit();
            }
            $fetchUserDetail = User::where('_id',$user->_id)->get();
            $userDetail = [];
            foreach($fetchUserDetail as $users) {
                $userDetail[] = [
                    'image' => asset('uploads/profile/'.$users->image)
                ];
            }
        } 
        catch(Exception $e){
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil Mengedit Profile Image!' : 'Gagal Mengedit Profile Image!',
            'result' => [
                'users' => $userDetail
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $isSuccess = false;
        DB::beginTransaction();
        try{
            if(Hash::check($request->old_password, $user->password)){
                $user->password = Hash::make($request->new_password);
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
                DB::commit();
                $isSuccess = true;
            }else{
                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'Maaf kata sandi lama yang anda masukkan tidak sesuai, mohon coba lagi.'
                ]);
            }
        } 
        catch(Exception $e){
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil mengedit kata sandi!' : 'Gagal mengedit kata sandi!'
        ]);
    }

    public function userDetail(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $fetchUserList = User::where('_id', $this->getCurrentUserId($request))->get();
        $userList = [];
        foreach($fetchUserList as $user) {
            $userList[] = [
                'id' => $user->_id,
                'name' => $user->name,
                'gender' => $user->gender,
                'email' => $user->email,
                'address' => $user->address,
                'phone' => $user->phone,
                'image'=> asset('uploads/profile/'.$user->image),
                'banner'=> asset('uploads/banner/'.$user->banner)
            ];
        }

        return response()->json([
            'status' => 'OK',
            'results' => [
                'user_detail' => $userList
            ]
        ]);
    }   

    public function doctorList(Request $request)
    {
        $request->validate([
            'search' => 'nullable'
        ]);

        $token = $this->getCurrentToken($request);
        $fetchDoctorList = UserDoctorDetail::query()
        ->leftJoin('users', 'users._id', '=', 'user_doctor_details.user_id')
        ->orderBy('users.name', 'ASC')
        ->where('is_approved',true);
        # order by average review ratings
        $reviewsWithConsultation = Consultation::select(
            'user_doctor_detail_id',
            DB::raw('AVG(star) as avg_star'), 
            DB::raw('COUNT(_id) AS consultation_count')
        )
        ->groupBy('user_doctor_detail_id');
        // $fetchDoctorList->leftJoinSub($reviewsWithConsultation, 'rev_sub', 'rev_sub.doctor_id', 'user_doctor_details._id')
        // ->addSelect(
        //     DB::raw(
        //         "CASE WHEN rev_sub.avg_star is null THEN '-' ".
        //         "ELSE rev_sub.avg_star ".
        //         "END as ratings"
        //     ),
        //     'consultation_count'
        // )
        // ->orderBy('ratings', 'desc')
        // ->orderBy('user_doctor_details.created_at', 'asc');
        # handling search parameter
        if ($request->has('search')) {
            $search = $request->search;
            # searchable attributes (name, address, price, description)
            $fetchDoctorList->orWhere('users.name', 'like', "$search")
                ->orWhere('users.address', 'like', "$search")
                ->orWhere('user_doctor_details.description', 'like', "$search");
        }

        if ($request->has('items')) {
            $items = $request->items;
            $fetchDoctorList = $fetchDoctorList->paginate($items);
        } else{
            $fetchDoctorList = $fetchDoctorList->paginate(7);
        }
        
        $doctorList = [];
        foreach($fetchDoctorList as $doctor) {

            // Find matching aggregated data for the current user doctor detail
            $aggregated = Consultation::raw(function($collection) use ($doctor) {
                return $collection->aggregate([
                    ['$match' => ['user_doctor_detail_id' => (string) $doctor->_id]],
                    ['$group' => [
                        '_id' => '$user_doctor_detail_id',
                        'avg_star' => ['$avg' => '$star'],
                        'consultation_count' => ['$sum' => 1]
                    ]]
                ]);
            })->first();

            // If aggregated data exists, set ratings and consultation count
            if ($aggregated) {
                $ratings = $aggregated->avg_star;
                $consultationCount = $aggregated->consultation_count;
            }
            $doctorList[] = [
                'id' => $doctor->user->_id,
                'user_doctor_detail_id' => $doctor->_id,
                'name' => $doctor->user->name,
                'vet_name' => $doctor->vet_name,
                'image' => asset('uploads/profile/'.$doctor->user->image),
                'price' => $doctor->price,
                'discount' => $doctor->discount ?? 0,
                'discounted_price' => ($doctor->price - (($doctor->price * $doctor->discount)/100)),
                'avg_ratings' => number_format((float)($ratings), 1),
                'consultation_count' => $consultationCount ?? 0
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
        $token = $this->getCurrentToken($request);
        $fetchDoctorList = UserDoctorDetail::where('user_id', $id)->get();

        $reviewsWithConsultation = Consultation::select(
            'user_doctor_detail_id',
            DB::raw('AVG(star) as avg_star'), 
            DB::raw('COUNT(_id) AS consultation_count')
        )
        ->groupBy('user_doctor_detail_id');
        // $reviewsWithConsultation = DB::table('consultations as c')
        // ->select(
        //     DB::raw('AVG(r.star) as avg_star'), 
        //     DB::raw('COUNT(r.id) AS consultation_count'),
        //     'c.user_doctor_detail_id as doctor_id')
        // ->groupBy('c.user_doctor_detail_id')
        // ->join('reviews as r', 'r.consultation_id', 'c.id');
        // $fetchDoctorList->leftJoinSub($reviewsWithConsultation, 'rev_sub', 'rev_sub.doctor_id', 'user_doctor_details.id')
        // ->addSelect(
        //     DB::raw(
        //         "CASE WHEN rev_sub.avg_star is null THEN '-' ".
        //         "ELSE rev_sub.avg_star ".
        //         "END as ratings"
        //     ),
        //     'consultation_count'
        // );

        $doctorList = [];
        foreach($fetchDoctorList as $doctor) {
            // Find matching aggregated data for the current user doctor detail
            $aggregated = Consultation::raw(function($collection) use ($doctor) {
                return $collection->aggregate([
                    ['$match' => ['user_doctor_detail_id' => (string) $doctor->_id]],
                    ['$group' => [
                        '_id' => '$user_doctor_detail_id',
                        'avg_star' => ['$avg' => '$star'],
                        'consultation_count' => ['$sum' => 1]
                    ]]
                ]);
            })->first();

            // If aggregated data exists, set ratings and consultation count
            if ($aggregated) {
                $ratings = $aggregated->avg_star;
                $consultationCount = $aggregated->consultation_count;
            }

            $doctorList[] = [
                'id' => $doctor->user->_id,
                'user_doctor_detail_id' => $doctor->_id,
                'name' => $doctor->user->name,
                'phone' => $doctor->user->phone,
                'address' => $doctor->user->address,
                'vet_name' => $doctor->vet_name,
                'image'=> asset('uploads/profile/'.$doctor->user->image),
                'banner'=> asset('uploads/banner/'.$doctor->user->banner),
                'price' => $doctor->price,
                'discount' => $doctor->discount ?? 0,
                'discounted_price' => ($doctor->price - (($doctor->price * $doctor->discount)/100)),
                'description' => $doctor->description,
                'avg_ratings' => number_format((float)($ratings), 1),
                'consultation_count' => $consultationCount ?? 0
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
        $token = $this->getCurrentToken($request);
        $fetchBannerList = Banner::orderBy('sequence', 'ASC')->get();

        $bannerList = [];
        foreach($fetchBannerList as $banner) {
            $bannerList[] = [
                'sequence' => $banner->sequence,
                'image' => asset('uploads/banner/'.$banner->image)
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
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
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
            $doctor = UserDoctorDetail::where('_id', $request->user_doctor_detail_id)->firstOrFail();

            $consultationId = Consultation::insertGetId([
                'user_id' => $user->_id,
                'user_doctor_detail_id' => $doctor->_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $fetchConsultationDetail = Consultation::where('_id',$consultationId)->get();
            $consultationDetail = [];
            foreach($fetchConsultationDetail as $consultation) {
                $consultationDetail[] = [
                    'id' => $consultationId,
                    'status' => $consultation->status,
                    'consultation_date' => $consultation->created_at->format('Y-m-d h:i:s'),
                    'patient' => $consultation->user->name,
                    'doctor' => $consultation->userDoctorDetail->user->name,
                    'address' => $consultation->userDoctorDetail->user->address,
                    'vet_name' => $consultation->userDoctorDetail->vet_name,
                    'price' => $consultation->userDoctorDetail->price,
                    'discount' => $doctor->discount ?? 0,
                    'discounted_price' => ($doctor->price - (($doctor->price * $doctor->discount)/100)),
                    'description' => $consultation->userDoctorDetail->description,
                    'max_payment_time' => Carbon::parse($consultation->created_at)->addHours(24)->format('Y-m-d h:i:s'),
                ];
            }  
            DB::commit();
            $isSuccess = true;
        } catch (Exception $e) {
            DB::rollback();
            $isSuccess = false;
        }
        $consultationDate = Consultation::find($consultationId);
        ConsultationCancelPayment::dispatch($consultationId)->delay(Carbon::parse($consultationDate->created_at)->addHours(24)); 
        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil konsultasi dengan dokter!' : 'Gagal untuk konsultasi dengan dokter!',
            'results' => [
                'consultation' => $consultationDetail
            ]
        ]);
    }

    public function consultationDetail(Request $request, $id)
    {
        $token = $this->getCurrentToken($request);
        $consultation = Consultation::find($id);
        $payment = Payment::where('consultation_id', $consultation->_id)->first();
        
        $consultationDetail[] = [
            'id' => $consultation->_id,
            'status' => $consultation->status,
            'consultation_date' => $consultation->created_at->format('Y-m-d h:i:s'),
            'patient' => $consultation->user->name,
            'doctor' => $consultation->userDoctorDetail->user->name,
            'phone' => $consultation->userDoctorDetail->user->phone,
            'address' => $consultation->userDoctorDetail->user->address,
            'image' => asset('uploads/profile/'.$consultation->userDoctorDetail->user->image),
            'vet_name' => $consultation->userDoctorDetail->vet_name,
            'price' => $consultation->userDoctorDetail->price,
            'discount' => $consultation->userDoctorDetail->discount ?? 0,
            'discounted_price' => ($consultation->userDoctorDetail->price - (($consultation->userDoctorDetail->price * $consultation->userDoctorDetail->discount)/100)),
            'description' => $consultation->userDoctorDetail->description,
            'max_payment_time' => Carbon::parse($consultation->created_at)->addHours(24)->format('Y-m-d h:i:s'),
            'pay_at' => is_null($payment) ? null : $payment->created_at->format('Y-m-d h:i:s'),
            'approved_at' => $consultation->approved_at ? Carbon::parse($consultation->approved_at)->format('Y-m-d h:i:s') : null,
            'rejected_at' =>  $consultation->rejected_at ? Carbon::parse($consultation->rejected_at)->format('Y-m-d h:i:s') : null,
        ];
    
        return response()->json([
            'status' => 'OK',
            'results' => [
                'consultation' => $consultationDetail
            ]
        ]);
    }

    public function consultationList(Request $request)
    {
        $request->validate([
            'search' => 'nullable'
        ]);
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $fetchConsultationList = Consultation::orderBy('created_at', 'DESC')->where('user_id', $user->_id);
        # handling search parameters
        if ($request->has('search')) {
            # search variable assignment
            $search = $request->search;
            # get the detail of the doctor with users & user_doctor_details attributes
            $doctors = DB::table('user_doctor_details as ud')
                ->select('u.name as doctor_name', 'ud.id as sub_id')
                ->join('users as u', 'u.id', 'ud.user_id');
            # joined doctors with the consultation query
            # in order not to break the where clause logic, the search values need to be in brackets
            $fetchConsultationList->joinSub($doctors, 'd_sub', 'd_sub.sub_id', 'consultations.user_doctor_detail_id')
                ->whereRaw(
                    "(d_sub.doctor_name like '%$search%' OR consultations.status like '%$search%')"
                );
        }
        if ($request->has('items')) {
            $items = $request->items;
            $fetchConsultationList = $fetchConsultationList->paginate(items);
        } else{
            $fetchConsultationList = $fetchConsultationList->paginate(7);
        }
        
        $consultationList = [];
        foreach($fetchConsultationList as $consultation) {
            $consultationList[] = [
                'id' => $consultation->_id,
                'doctor' => $consultation->userDoctorDetail->user->name,
                'vet_name' => $consultation->userDoctorDetail->vet_name,
                'price' => $consultation->userDoctorDetail->price,
                'discount' => $consultation->userDoctorDetail->discount ?? 0,
                'discounted_price' => ($consultation->userDoctorDetail->price - (($consultation->userDoctorDetail->price * $consultation->userDoctorDetail->discount)/100)),
                'created_at'=> $consultation->created_at->format('d-m-Y'),
                'updated_at'=> $consultation->updated_at->format('d-m-Y')
            ];
        }

        return response()->json([
            'status' => 'OK',
            'results' => [
                'consultations' => $consultationList
            ]
        ]);
    }

    public function payment(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $isSuccess = false;

        DB::beginTransaction();

        $consultation_id = $request->consultation_id;

        if(Consultation::find($consultation_id)->status == 'Dibatalkan'){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Maaf anda terlambat untuk melakukan pembayaran silahkan konsultasi ulang dengan dokter!'
            ]);
        } else{
            try {
                $message = "";
                if(!$request->message){
                    $message = "-";
                }else{
                    $message = $request->message;
                }
    
                $this->validate($request,[
                    'image' => 'mimes:jpeg,bmp,png,jpg'
                ]);
                // get form image
                $image = $request->file('image');
                $slug = Str::slug($request->sender_name);
                if (isset($image)) 
                {
                    //            make unique name for image
                    $currentDate = Carbon::now()->toDateString();
                    $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
    
                    if (!file_exists('uploads/payments')) 
                    {
                        mkdir('uploads/payments',0777,true);
                    }
                    $image->move('uploads/payments',$imagename);
    
                } else {
                    $imagename = "default.png";
                }
    
                $paymentId = Payment::insertGetId([
                    'consultation_id' => $request->consultation_id,
                    'bank_name' => $request->bank_name,
                    'sender_name' => $request->sender_name,
                    'image' => $imagename,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $consultation = Consultation::find($consultation_id);
                $consultation->status = "Menunggu Konfirmasi Pembayaran";
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

    public function review(Request $request)
    {
        $token = $this->getCurrentToken($request);
        $user = User::where('_id', $this->getCurrentUserId($request))->first();
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

            $reviewId = Review::insertGetId([
                'consultation_id' => $request->consultation_id,
                'star' => $request->star,
                'review' => $request->review,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();
            $isSuccess = true;

            $consultation = Consultation::find($request->consultation_id);
            $consultation->status = "Selesai Mengulas";
            $consultation->save();
            $fetchReviewList = Review::where('_id', $reviewId)->get();

            $reviewList = [];
            foreach($fetchReviewList as $rev) {
                $reviewList[] = [
                    'id' => $rev->_id,
                    'consultation_id'=> $rev->consultation_id,
                    'star'=> $rev->star,
                    'review' => $rev->review
                ];
            }
        } catch (Exception $e) {
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'message' => $isSuccess ? 'Berhasil mengirimkan review!' : 'Gagal mengirimkan review!',
            'result' => [
                'review' => $reviewList,
            ]
        ]);
    }

    public function reviewList(Request $request, $id)
    {
        $token = $this->getCurrentToken($request);

        $isSuccess = false;

        DB::beginTransaction();
        try {
            $message = "";
            if(!$request->message){
                $message = "-";
            }else{
                $message = $request->message;
            }

            $fetchReviewList = Review::select([
                'reviews.*'
            ])
                ->orderBy('reviews.created_at', 'DESC')
                ->join('consultations', 'consultations.id', 'reviews.consultation_id')
                ->where('consultations.user_doctor_detail_id', $id)
                ->get();
            $isSuccess = true;

            $reviewList = [];
            foreach($fetchReviewList as $rev) {
                $reviewList[] = [
                    'id' => $rev->_id,
                    'user_name'=> $rev->consultation->user->name,
                    'user_image'=> asset('uploads/profile/'.$rev->consultation->user->image),
                    'star'=> $rev->star,
                    'review' => $rev->review,
                    'created_at' => $rev->created_at->format('Y-m-d h:i:s'),
                ];
            }
        } catch (Exception $e) {
            DB::rollback();
            $isSuccess = false;
        }

        return response()->json([
            'status' => $isSuccess ? 'OK' : 'FAIL',
            'results' => [
                'review' => $reviewList,
            ]
        ]);
    }

    private function getCurrentUserId(Request $request)
    {
        $token = $this->getCurrentToken($request);
        if (!$token) {
            return null; // Or handle the case when no token is provided
        }

        $accessToken = DB::table('oauth_access_tokens')
            ->where('id', $token)
            ->first();
        return $accessToken ? $accessToken['user_id'] : null;
    }
}

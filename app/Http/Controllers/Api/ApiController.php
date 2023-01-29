<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use App\UserDoctorDetail;
use App\Banner;
use App\Consultation;
use App\Payment;
use App\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

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
        $user = User::where('id', $id)->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $isSuccess = false;


        $this->validate($request,[
            'image' => 'mimes:jpeg,bmp,png,jpg',
            'banner' => 'mimes:jpeg,bmp,png,jpg',
        ]);

        DB::beginTransaction();
        try{
            $slug = str_slug($request->name);
            $image = $request->file('image');
            $imagename = "";
            if (isset($image))
            {
                $currentDate = Carbon::now()->toDateString();
                $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                if (!file_exists('uploads/profile'))
                {
                    mkdir('uploads/profile',0777,true);
                }
                $image->move('uploads/profile',$imagename);

            } else {
                $imagename =  "default.png";
            }

            $banner = $request->file('banner');
            $bannername = "";
            if (isset($banner)) 
            {
                $currentDate = Carbon::now()->toDateString();
                $bannername = $slug.'-'.$currentDate.'-'.uniqid().'.'.$banner->getClientOriginalExtension();

                if (!file_exists('uploads/banner')) 
                {
                    mkdir('uploads/banner',0777,true);
                }
                $banner->move('uploads/banner',$bannername);

            } else {
                $bannername =  "default.png";
            }
            $user = User::find($id);
            $user->name =  $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->image = $imagename;
            $user->banner = $bannername;

            if($user->save()){
                $isSuccess = true;
            }
            $fetchUserDetail = User::where('id','=',$user->id)->get();
            $userDetail = [];
            foreach($fetchUserDetail as $users) {
                $userDetail[] = [
                    'id' => $users->id,
                    'name' => $users->name,
                    'gender' => $users->gender,
                    'email' => $users->email,
                    'address' => $users->address,
                    'phone' => $users->phone,
                    'image' => asset('uploads/profile/'.$users->image),
                    'banner' => asset('uploads/baner/'.$users->banner),
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

    public function doctorList(Request $request)
    {
        $request->validate([
            'search' => 'nullable'
        ]);

        $token = $this::getCurrentToken($request);
        $fetchDoctorList = UserDoctorDetail::select([
            'user_doctor_details.*',
            'users.name AS name'
        ])
            ->leftJoin('users', 'users.id', 'user_doctor_details.user_id');
        # order by average review ratings
        $reviewsWithConsultation = DB::table('consultations as c')
        ->select(
            DB::raw('AVG(r.star) as avg_star'), 
            DB::raw('COUNT(r.id) AS consultation_count'),
            'c.user_doctor_detail_id as doctor_id')
        ->groupBy('c.user_doctor_detail_id')
        ->join('reviews as r', 'r.consultation_id', 'c.id');
        $fetchDoctorList->leftJoinSub($reviewsWithConsultation, 'rev_sub', 'rev_sub.doctor_id', 'user_doctor_details.id')
        ->addSelect(
            DB::raw(
                "CASE WHEN rev_sub.avg_star is null THEN '-' ".
                "ELSE rev_sub.avg_star ".
                "END as ratings"
            ),
            'consultation_count'
        )
        ->orderBy('ratings', 'desc')
        ->orderBy('user_doctor_details.created_at', 'asc');
        # handling search parameter
        if ($request->has('search')) {
            $search = $request->search;
            # searchable attributes (name, address, price, description)
            $fetchDoctorList->orWhere('name', 'like', "%$search%")
                ->orWhere('users.name', 'like', "%$search%")
                ->orWhere('users.address', 'like', "%$search%")
                ->orWhere('user_doctor_details.description', 'like', "%$search%");
        }

        if ($request->has('items')) {
            $items = $request->items;
            $fetchDoctorList = $fetchDoctorList->paginate($items);
        } else{
            $fetchDoctorList = $fetchDoctorList->paginate(7);
        }
        
        $doctorList = [];
        foreach($fetchDoctorList as $doctor) {
            $doctorList[] = [
                'id' => $doctor->user->id,
                'user_doctor_detail_id' => $doctor->id,
                'name' => $doctor->user->name,
                'vet_name' => $doctor->vet_name,
                'image' => asset('uploads/profile/'.$doctor->user->image),
                'price' => $doctor->price,
                'avg_ratings' => $doctor->ratings,
                'consultation_count' => $doctor->consultation_count
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
            ->where('user_doctor_details.user_id', '=', $id);
        
        $reviewsWithConsultation = DB::table('consultations as c')
        ->select(
            DB::raw('AVG(r.star) as avg_star'), 
            DB::raw('COUNT(r.id) AS consultation_count'),
            'c.user_doctor_detail_id as doctor_id')
        ->groupBy('c.user_doctor_detail_id')
        ->join('reviews as r', 'r.consultation_id', 'c.id');
        $fetchDoctorList->leftJoinSub($reviewsWithConsultation, 'rev_sub', 'rev_sub.doctor_id', 'user_doctor_details.id')
        ->addSelect(
            DB::raw(
                "CASE WHEN rev_sub.avg_star is null THEN '-' ".
                "ELSE rev_sub.avg_star ".
                "END as ratings"
            ),
            'consultation_count'
        );

        $fetchDoctorList = $fetchDoctorList->get();
        $doctorList = [];
        foreach($fetchDoctorList as $doctor) {
            $doctorList[] = [
                'id' => $doctor->user->id,
                'user_doctor_detail_id' => $doctor->id,
                'name' => $doctor->user->name,
                'address' => $doctor->user->address,
                'vet_name' => $doctor->vet_name,
                'image'=> asset('uploads/profile/'.$doctor->user->image),
                'banner'=> asset('uploads/banner/'.$doctor->user->banner),
                'price' => $doctor->price,
                'description' => $doctor->description,
                'avg_ratings' => $doctor->ratings,
                'consultation_count' => $doctor->consultation_count
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
            $doctor = UserDoctorDetail::where('id', $request->user_doctor_detail_id)->first();

            $consultationId = Consultation::insertGetId([
                'user_id' => $user->id,
                'user_doctor_detail_id' => $doctor->id,
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

    public function consultationList(Request $request)
    {
        $request->validate([
            'search' => 'nullable'
        ]);
        $token = $this::getCurrentToken($request);
        $user = User::where('id', $token->user_id)->first();
        if(empty($user)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Invalid User ID'
            ]);
        }

        $fetchConsultationList = Consultation::select([
            'consultations.*'
        ])
            ->orderBy('consultations.created_at', 'DESC')
            ->where('consultations.user_id', '=', $user->id);
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
                'doctor' => $consultation->userDoctorDetail->user->name,
                'price' => $consultation->userDoctorDetail->price,
                'status'=> $consultation->status,
                'created_at'=> $consultation->created_at->format('d-m-Y'),
                'updated_at'=> $consultation->updated_at->format('d-m-Y')
            ];
        }

        return response()->json([
            'status' => 'OK',
            'results' => [
                'banner_list' => $consultationList
            ]
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

            $this->validate($request,[
                'image' => 'mimes:jpeg,bmp,png,jpg'
            ]);
            // get form image
            $image = $request->file('image');
            $slug = str_slug($request->sender_name);
            if (isset($image)) 
            {
                //            make unique name for image
                $currentDate = Carbon::now()->toDateString();
                $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
                //            check banner dir is exists
                /*if (!Storage::disk('public')->exists('banner'))
                {
                    Storage::disk('public')->makeDirectory('banner');
                }
    //            delete old image
                if (Storage::disk('public')->exists('banner/'.$banner->image))
                {
                    Storage::disk('public')->delete('banner/'.$banner->image);
                }
    //            resize image for banner and upload
                $bannerimage = Image::make($image)->resize(1600,479)->stream();
                Storage::disk('public')->put('banner/'.$imagename,$bannerimage);*/

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

    public function review(Request $request)
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

            $reviewId = Review::insertGetId([
                'consultation_id' => $request->consultation_id,
                'star' => $request->star,
                'review' => $request->review,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $consultation_id = $request->consultation_id;
            $consultation = Consultation::find($consultation_id);
            $consultation->status = "Done";
            $consultation->save();
            DB::commit();
            $isSuccess = true;

            $fetchReviewList = Review::where('id', $reviewId)->get();

            $reviewList = [];
            foreach($fetchReviewList as $rev) {
                $reviewList[] = [
                    'id' => $rev->id,
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


    
}

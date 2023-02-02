<?php

namespace App\Http\Controllers\Auth;

use App\AccountApproval;
use App\User;
use App\UserDoctorDetail;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    //protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|min:8|max:14',
            'image' => 'required|mimes:jpeg,bmp,png,jpg',
            'banner' => 'required|mimes:jpeg,bmp,png,jpg'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $request = request();
        $user = new User();
        $image = $request->file('image');
        $imageName = "";
        $slug = str_slug($data['name']);
        if (isset($image))
        {
            $currentDate = Carbon::now()->toDateString();
            $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('uploads/profile'))
            {
                Storage::disk('public')->makeDirectory('uploads/profile');
            }
//            Delete old image form profile folder
            if (Storage::disk('public')->exists('uploads/profile/'.$user->image))
            {
                Storage::disk('public')->delete('uploads/profile/'.$user->image);
            }
            $image->move('uploads/profile',$imageName);
        } else {
            $imageName = $user->image;
        }

        $banner = $request->file('banner');
        $bannerName = "";
        if (isset($banner))
        {
            $currentDate = Carbon::now()->toDateString();
            $bannerName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$banner->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('uploads/banner'))
            {
                Storage::disk('public')->makeDirectory('uploads/banner');
            }
//            Delete old image form profile folder
            if (Storage::disk('public')->exists('uploads/banner/'.$user->banner))
            {
                Storage::disk('public')->delete('uploads/banner/'.$user->banner);
            }
            $banner->move('uploads/banner',$bannerName);
        } else {
            $bannerName = $user->image;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->address = $data['address'];
        $user->gender = $data['gender'];
        $user->phone = $data['phone'];
        $user->image = $imageName;
        $user->banner = $bannerName;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        if ($user->save()) {
            UserDoctorDetail::insertGetId([
                'user_id' => $user->id,
                'vet_name' => $data['vet_name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'discount' => $data['discount'],
                'is_approved' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            # create doctor approval
            $approval = new AccountApproval();
            $approval->approver_id = null;
            $approval->requester_id = $user->id;
            $approval->status = 0;
            $approval->save();
            Toastr::success('Account succesfuly created, please wait until our admin approve your account', 'Success');
            return view('auth.register');
        }
    }
}

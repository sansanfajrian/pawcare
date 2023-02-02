<?php

namespace App\Http\Controllers\Author;

use App\User;
use App\UserDoctorDetail;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SettingsController extends Controller
{
    public function index()
    {
        return view('author.settings');
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'mimes:jpeg,bmp,png,jpg',
            'banner' => 'mimes:jpeg,bmp,png,jpg'
        ]);
        
        $user = User::findOrFail(Auth::id());
        $image = $request->file('image');
        $slug = str_slug($request->name);
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
        $slug = str_slug($request->name);
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
        $user->name = $request->name;
        $user->email = $request->email;
        $user->image = $imageName;
        $user->banner = $bannerName;
        $user->address = $request->address;
        $user->phone = $request->phone;
        if($user->save()){
            $doctor = UserDoctorDetail::where('user_id', $user->id)->first();
            $doctor->vet_name = $request->vet_name;
            $doctor->price = $request->price;
            $doctor->discount = $request->discount;
            $doctor->description = $request->description;
            $doctor->save();
        }
        Toastr::success('Profile Successfully Updated :)','Success');
        return redirect()->back();
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request,[
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password,$hashedPassword))
        {
            if (!Hash::check($request->password,$hashedPassword))
            {
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->password);
                $user->save();
                Toastr::success('Password Successfully Changed','Success');
                Auth::logout();
                return redirect()->back();
            } else {
                Toastr::error('New password cannot be the same as old password.','Error');
                return redirect()->back();
            }
        } else {
            Toastr::error('Current password not match.','Error');
            return redirect()->back();
        }

    }
}

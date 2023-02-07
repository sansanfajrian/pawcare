<?php

namespace App\Http\Controllers\Author;

use App\Payment;
use App\Consultation;
use App\UserDoctorDetail;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $doctor = UserDoctorDetail::where('user_id', '=', $user->id)->first();
        $payments = Payment::all()
        ->where('consultation.user_doctor_detail_id', $doctor->id);
        return view('author.payment.index',compact('payments'));
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payment::find($id);
        return view('author.payment.show', compact('payment'));
    }
}

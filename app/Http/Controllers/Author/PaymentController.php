<?php

namespace App\Http\Controllers\Author;

use App\Payment;
use App\Consultation;
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

        $doctor_id = $user->userDoctorDetails->id;
        $payments = Payment::all()
        ->where('consultation.user_doctor_detail_id', $doctor_id);
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

    public function status($id){
        $consultation_id = Payment::find($id)->first()->consultation_id;
        $consultation = Consultation::find($consultation_id);
        $consultation->status = "On Progress";
        if($consultation->save()){
            Toastr::success('Payment Approved!','Success',["positionClass"=>"toast-top-right"]);
            return redirect()->back();
        }else{
            return redirect()->back()->with('successMsg','Gagal di konfirmasi');
        }
    }

    public function statusDeny($id){
        $consultation_id = Payment::find($id)->first()->consultation_id;
        $consultation = Consultation::find($consultation_id);
        $consultation->status = "Ditolak";
        if($consultation->save()){
            Toastr::success('Payment Denied!','Success',["positionClass"=>"toast-top-right"]);
            return redirect()->back();
        }else{
            return redirect()->back()->with('successMsg','Gagal di konfirmasi');
        }
    }
}

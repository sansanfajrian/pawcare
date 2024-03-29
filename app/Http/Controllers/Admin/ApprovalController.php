<?php

namespace App\Http\Controllers\Admin;

use App\AccountApproval;
use App\Mail\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\UserDoctorDetail;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDoctors(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable',
            'page' => 'nullable'
        ]);
        $approvals = AccountApproval::with('requester.userDoctorDetails')
            ->paginate($request->perPage, ['*'], 'page', $request->page);
        return view('admin.approval', $approvals->toArray());
    }

    private function getStatusLabel($status)
    {
        switch ($status) {
            case 0:
                $label = 'not-approved';
                break;
            case 1:
                $label = 'pending';
                break;
            case 2:
                $label = 'rejected';
                break;
            default:
                $label = 'approved';
                break;
        }
        return $label;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * change approve status on user_doctor_details table.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required',
            'approval_id' => 'required'
        ], [
            'doctor_id.required' => 'failed to get doctor_id',
            'approval_id.required' => 'failed to get approval_id'
        ]);

        # this feature is critical so it needs to double check
        # to avoid unwanted errors

        # handling approval on user_doctor_details
        if ($request->has('doctor_id') && $request->has('approval_id')) {
            $doctor = UserDoctorDetail::find($request->doctor_id);
            $doctor->is_approved = true;
            $approval = AccountApproval::where('requester_id', $request->approval_id)->first();
            if($doctor->save() && $approval->delete()){
                $data = [
                    'name' => $doctor->user->name
                ];
                Toastr::success('account has been successfully approved.', 'Success');
                Mail::to($doctor->user->email)->send(new SendEmail($data));
            }
        }

        return redirect()->back();
    }
}

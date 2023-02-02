<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserDoctorDetail;

class DoctorController extends Controller
{
    public function index()
    {

        #list of users
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

        return view('admin.doctor', [
            'data' => $doctors

        ]);
    }
}

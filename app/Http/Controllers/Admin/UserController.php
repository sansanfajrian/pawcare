<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        #list of users
        $users = User::where('role_id', 3)->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->email,
                    'phone' => $item->phone,
                    'created_at' => $item->created_at,
                ];
            });

        return view('admin.user', [
            'data' => $users
        ]);
    }
}

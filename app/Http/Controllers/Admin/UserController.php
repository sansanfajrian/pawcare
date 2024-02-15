<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use DB;

class UserController extends Controller
{
    public function index()
    {
        #list of users
        $roles = DB::collection('roles')->get();
        $thirdRole = optional($roles->get(2));
        $thirdRoleId = $thirdRole ? (string) $thirdRole['_id'] : null;
        $users = User::where('role_id', $thirdRoleId)->get()
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

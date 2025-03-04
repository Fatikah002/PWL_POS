<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Termwind\Components\Dd;

class UserController extends Controller
{
        public function index()
        {
               $user = UserModel::all();
               return view('user', ['data' => $user]);

                // dd($user);
                // $data = [
                //     'username' => 'customer-1',
                //     'nama' => 'Pelanggan',
                //     'password' => Hash::make('12345'),
                //     'level_id' => 4
                // ];
                // $data=[
                //     'level_id' => 2,
                //     'username' => 'manager_tiga',
                //     'nama' => 'Manager 3',
                //     'password' => Hash::make('12345')
                // ];
                // UserModel::create($data);

                // $user = UserModel::firstWhere('level_id', 1);

        }
}

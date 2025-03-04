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
                $user = UserModel::create([
                        'username' => 'manager55',
                        'nama' => 'Manager55',
                        'password' => Hash::make('12345'),
                        'level_id' => 2,

                ]);

                $user->username = 'manager56';

                $user->isDirty(); // true
                $user->isDirty('username'); // true
                $user->isDirty('nama'); // false
                $user->isDirty(['nama', 'username']); // true

                $user->isClean(); // false
                $user->isClean('username'); // false
                $user->isClean('nama'); // true
                $user->isClean(['nama', 'username']); // false

                $user->save();

                $user->isDirty(); // false
                $user->isClean(); // true
                dd($user->isDirty());


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

                // // UserModel::insert($data); //tambahkan data ke tabel user
                // UserModel::where('username', 'customer-1')->update($data);
                // // coba akses model UserModel

        }
}

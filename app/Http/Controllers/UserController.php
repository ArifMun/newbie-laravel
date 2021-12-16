<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function index()
    {
        $data = DB::table('users')
            ->select('id', 'name', 'email', 'username', 'phone')
            // ->limit(20)
            ->get();
        return view('users/data_users', ['data' => $data]);
    }

    public function tambah_data()
    {
        $header = "Tambah Data Pengguna";
        return view('users/tambah_data', ['header' => $header]);
    }

    public function proses_tambah_data(Request $request)
    {
        date_default_timezone_set('Asia/jakarta');
        $validated = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
                //unique:users agar email unik dicek melalui tabel users
                'email' => 'required|email:rfc,dns|unique:users',
                'username' => 'required|unique:users',
                'password' => [
                    'required',
                    Password::min(8)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ]

            ],
            [
                'nama.required' => 'Nama Lengkap tidak boleh kosong',
                'email.required' => "Email tidak boleh kosong",
                'username.required' => 'Username tidak boleh kosong',
                'password.required' => 'Password tidak boleh kosong',
                'email.unique' => 'Email sudah terdaftar' //unique apabila email sudah digunakan 
            ]
        );

        if ($validated->fails()) {
            return redirect('users/tambah_data')
                ->withErrors($validated)
                ->withInput();
        } else {
            $name = $request->input('nama');
            $email = $request->input('email');
            $username = $request->input('username');
            $password = bcrypt($request->input('password'));
            $phone = $request->input('phone');
            $roles = $request->input('roles');

            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'phone' => $phone,
                'roles' => $roles,
                'password' => $password,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect('/users')->with('berhasil', 'Data Pengguna Berhasil di Simpan');
        }
    }
}
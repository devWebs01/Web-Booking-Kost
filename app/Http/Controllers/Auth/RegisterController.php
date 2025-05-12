<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected string $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'telp' => ['required', 'numeric', 'digits_between:10,12', 'unique:users,telp'],
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telp' => $data['telp'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        $user->sendEmailVerificationNotification();

        return $user;
    }

    protected function registered(Request $request, $user)
    {

        return redirect('/welcome')->with('success', 'Registrasi berhasil! Silakan periksa email Anda untuk memverifikasi akun Anda.');
    }
}

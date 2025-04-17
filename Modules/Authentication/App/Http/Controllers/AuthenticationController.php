<?php

namespace Modules\Authentication\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Authentication\App\Http\Requests\LoginRequest;

class AuthenticationController extends Controller
{
    public function getLoginPage()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();
        if (Auth::attempt($validatedData)) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->withErrors(['email' => 'The credentials you provided do not exist.']);
        }
    }
}

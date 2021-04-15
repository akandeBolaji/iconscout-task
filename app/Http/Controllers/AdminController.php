<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;

class AdminController extends Controller
{
    public function logout()
    {
        Auth::logout(); // log the user out of our application
        return Redirect::to('admin/login'); // redirect the user to the login screen
    }

    public function index(Request $request)
    {
        return view('admin.index');
    }

    public function login(Request $request)
    {
        return view('admin.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'type' => ['team-member', 'team-admin']])) {
            $request->session()->regenerate();

            return redirect()->intended('admin');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}

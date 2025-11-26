<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function loginPage(){
        if(Auth::guard("external")->check()){
            return redirect("/portal");
        }
        return view("Login");
    }

    public function login(Request $request)
    {
       $credentials = $request->only('username', 'password');
       
       if(!Auth::guard("external")->attempt($credentials)) {
            return redirect()->route("portal.login")->with("error", "Invalid username or password");
        }

        return redirect('/portal');
    }
}

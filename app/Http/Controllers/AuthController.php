<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExternalEmployees;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function loginPage()
    {
        if (Auth::guard("external")->check()) {
            return redirect("/portal");
        }
        return view("Login");
    }

    public function registerPage()
    {
        $email = "";
        if (request()->has("email")) {
            $email = request()->email;
        }
        return view("Register", ["email" => $email]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!Auth::guard("external")->attempt($credentials)) {
            return redirect()->route("portal.login")->with("error", "Invalid username or password");
        }

        return redirect('/portal');
    }

    public function SaveUser($user)
    {


        $startBiometric = 8000;

        $latest = ExternalEmployees::where('biometric_id', '>=', $startBiometric)
            ->whereNotIn('biometric_id', function ($query) {
                $query->select('biometric_id')
                    ->from('employee_profiles');
            })
            ->orderBy('biometric_id', 'desc')
            ->value('biometric_id');

        $nextBiometric = $latest ? $latest + 1 : $startBiometric;

        $user = ExternalEmployees::firstOrCreate(
            [
                'email' => $user['email'],
                'contact_number' => $user['contact_number'],
                'last_name' => $user['last_name'],
                'first_name' => $user['first_name'],
                'username' => $user['username'],
            ],
            [
                'middle_name' => $user['middle_name'],
                'ext_name' => $user['ext_name'],
                'email' => $user['email'],
                'address' => $user['address'],
                'agency' => $user['agency'],
                'position' => $user['position'],
                'username' => $user['username'],
                'password' => Hash::make($user['password']),
                'biometric_id' => $nextBiometric,
            ]
        );

        return $nextBiometric;
    }

    public function register(Request $request)
    {
        $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'ext_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:external_employees,email|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'agency' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'username' => 'required|string|unique:external_employees,username|max:255',
            'password' => 'required|string|min:4|confirmed',
        ]);

        session()->put("user", $request->all());

        if ($request->has("email")) {

            $nextBiometric = $this->SaveUser($request->all());

            return redirect()->route("portal.successful", ["biometric_id" => $nextBiometric]);
        }
        return redirect()->route("portal.sendConfirmation");
    }



    public function activate()
    {

        if (!session()->has("user")) {
            return redirect()->route("portal.expire");
        }

        $user = session()->get("user");

        $nextBiometric = $this->SaveUser($user);


        return redirect()->route("portal.AccountActivated", ["biometric_id" => $nextBiometric]);
    }

    public function AccountActivated(Request $request)
    {
        return view("AccountActivated", ["biometric_id" => $request->biometric_id]);
    }

    public function checkEmail()
    {
        $user = session()->get("user");
        return view("CheckEmail", ['user' => $user]);
    }


    public function expire()
    {
        return view("Expire");
    }

    public function successful(Request $request)
    {
        return view("Successful", ["biometric_id" => $request->biometric_id]);
    }
}

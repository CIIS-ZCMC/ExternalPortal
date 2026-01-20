<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Mailer\MailConfig;
use App\Models\ExternalEmployees;
use Carbon\Carbon;
use Livewire\Features\SupportQueryString\BaseUrl;

class MailController extends Controller
{
    public function sendConfirmation()
    {

        $user = session()->get("user");
        $mail = new MailConfig();

        $mail->send([
            "To_receiver" => $user["email"],
            "Receiver_Name" => $user["first_name"] . " " . $user["last_name"],
            "Subject" => "ZCMC External Employee Portal Registration",
            "Body" => "
            <h3>Welcome!</h3>
            <p>Thank you for registering to the ZCMC External Employee Portal.</p>
            <p>Please click the link below to confirm your registration:</p>
             <br>
            <a href='" . request()->getSchemeAndHttpHost() . "/activate?data=" . encrypt(session()->get("user")) . "'>Verify Account</a>
            ",
        ]);

        return redirect()->route("portal.checkEmail");
    }

    public function sendResetPassword(Request $request)
    {

        $email = $request->email;

        $user = ExternalEmployees::firstWhere("email", $email);

        if (!$user) {
            return redirect()->route("portal.forgotPassword")->with("error", "User not found");
        }

        session()->put("user", $user);

        $mail = new MailConfig();

        $mail->send([
            "To_receiver" => $user->email,
            "Receiver_Name" => $user->first_name . " " . $user->last_name,
            "Subject" => "ZCMC External Employee Portal Password recovery",
            "Body" => "
            <h3>Hi! " . $user->first_name . " " . $user->last_name . "</h3>
            <p>Here is the link to reset your password:</p>
             <br>
            <a href='" . request()->getSchemeAndHttpHost() . "/portal/reset-password?data=" . encrypt([
                "email" => $user->email,
                "id" => $user->id,
                "timer" => Carbon::now()->addMinutes(30)->timestamp,

            ]) . "'>Reset Password</a>
            ",
        ]);

        return redirect()->route("portal.checkEmail", ["isResetPassword" => true]);
    }
}

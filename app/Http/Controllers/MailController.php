<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Mailer\MailConfig;
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
            <a href='" . request()->getSchemeAndHttpHost() . "/activate'>Verify Account</a>
            ",
        ]);

        return redirect()->route("portal.checkEmail");
    }
}

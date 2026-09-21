<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExternalEmployees;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function ValidateLogin($redirect)
    {
        $adminAccounts = json_decode(file_get_contents(base_path("Admin_Accounts.json")));

        if (!session()->has("admin_user")) {
            if (request()->has("employeeId")) {

                if (in_array(request("employeeId"), $adminAccounts->admin_accounts)) {
                    session()->put("admin_user", true);
                    session()->forget("error");
                    return true;
                }
                session()->put("error", "Access Denied");
                return false;
            }
        }
    }
    public function loginPage()
    {
        if (Auth::guard("external")->check()) {
            return redirect("/portal");
        }
        return view("Login");
    }

    public function forgotPasswordPage()
    {
        return view("ForgotPassword");
    }

    public function resetPasswordPage(Request $request)
    {

        try {
            $data = decrypt($request->data);

            if (Carbon::parse($data['timer'])->isPast()) {
                abort(419);
            }



            return view("ResetPassword", ["email" => $data["email"]]);
        } catch (DecryptException $th) {
            abort(404);
        }
    }

    public function savePassword(Request $request)
    {


        $email = $request->email;
        $password = $request->password;
        $user = ExternalEmployees::firstWhere("email", $email);

        $user->update([
            "password" => Hash::make($password)
        ]);

        return redirect()->route("portal.successful", ['is_password_changed' => true]);
    }


    public function adminLogin()
    {
        if (session()->has("admin_user")) {
            return redirect("/admin/users-lists");
        }
        return view("AdminLogin");
    }

    public function AdminSignin(Request $request)
    {
        $validate = $this->ValidateLogin("admin.login");

        if ($validate) {
            session()->put("admin_user", true);
            return redirect('admin/users-lists');
        }
        return redirect()->route("admin.login")->with("error", "Invalid Employee ID");
    }

    public function registerPage()
    {
        $email = "";
        if (request()->has("email_address")) {
            $email = request()->email_address;
        }

        $prefilledAgencies = [
            'Zamboanga City Medical Center (ZCMC)',
            'Department of Health (DOH)',
            'PhilHealth',
            'Food and Drug Administration (FDA)',
            'Other Hospital / Medical Institution',
            'Other Hospital/ Medical Institution',
            'Department of Education (DepEd)',
            'Department of the Interior and Local Government (DILG)',
            'Department of Social Welfare and Development (DSWD)',
            'Department of Finance (DOF)',
            'Department of Budget and Management (DBM)',
            'Department of Science and Technology (DOST)',
            'Department of Tourism (DOT)',
            'Department of Justice (DOJ)',
            'Department of Agriculture (DA)',
            'Department of Labor and Employment (DOLE)',
            'Department of National Defense (DND)',
            'Department of Transportation (DOTr)',
            'Department of Public Works and Highways (DPWH)',
            'Department of Trade and Industry (DTI)',
            'Department of Environment and Natural Resources (DENR)',
            'Commission on Elections (COMELEC)',
            'Commission on Higher Education (CHED)',
            'Technical Education and Skills Development Authority (TESDA)',
            'Civil Service Commission (CSC)',
            'Professional Regulation Commission (PRC)',
            'Commission on Audit (COA)',
            'Government Service Insurance System (GSIS)',
            'Provincial Government',
            'City Government',
            'Municipal Government',
            'Barangay Government',
            'Philippine National Police (PNP)',
            'Armed Forces of the Philippines (AFP)',
            'Other Government Agency',
        ];

        $normalizedPrefilled = array_map(fn($item) => strtolower(trim($item)), $prefilledAgencies);

        $uniqueAgencies = ExternalEmployees::select('agency')
            ->distinct()
            ->whereNotNull('agency')
            ->where('agency', '!=', '')
            ->pluck('agency')
            ->map(fn($item) => trim($item))
            ->filter(fn($agency) => !empty($agency) && !in_array(strtolower($agency), $normalizedPrefilled))
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return view("Register", [
            "email" => $email,
            "uniqueAgencies" => $uniqueAgencies,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard("external")->attempt($credentials)) {
            $user = Auth::guard("external")->user();

            if (is_null($user->email_verified_at)) {
                Auth::guard("external")->logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect()->route("portal.login")->with("error", "Your email address has not been verified yet. Please check your email to verify your account before logging in.");
            }

            return redirect('/portal');
        }

        if (Auth::guard("administrator")->attempt($credentials)) {
            return redirect("/administratorPanel");
        }

        return redirect()->route("portal.login")->with("error", "Invalid username or password");
    }

    public function SaveUser($user, $isVerified = false)
    {
        $startBiometric = 8000;

        $latest = ExternalEmployees::withTrashed()
            ->where('biometric_id', '>=', $startBiometric)
            ->whereNotIn('biometric_id', function ($query) {
                $query->select('biometric_id')
                    ->from('employee_profiles');
            })
            ->orderBy('biometric_id', 'desc')
            ->value('biometric_id');

        $nextBiometric = $latest ? $latest + 1 : $startBiometric;

        $employee = ExternalEmployees::firstOrCreate(
            [
                'email' => $user['email'],
                'contact_number' => $user['contact_number'],
                'last_name' => $user['last_name'],
                'first_name' => $user['first_name'],
                'username' => $user['username'],
            ],
            [
                'middle_name' => $user['middle_name'] ?? null,
                'ext_name' => $user['ext_name'] ?? null,
                'email' => $user['email'],
                'address' => $user['address'] ?? '',
                'agency' => $user['agency'] ?? null,
                'position' => $user['position'] ?? null,
                'username' => $user['username'],
                'password' => Hash::make($user['password']),
                'biometric_id' => $nextBiometric,
                'email_verified_at' => $isVerified ? Carbon::now() : null,
            ]
        );

        return $employee;
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

        $isGoogle = $request->has("email_address") && !empty($request->get("email_address"));

        // Save directly to external_employees table
        $employee = $this->SaveUser($request->all(), $isGoogle);

        if ($isGoogle) {
            return redirect()->route("portal.successful", ["biometric_id" => $employee->biometric_id]);
        }

        session()->put("user", $employee->toArray());

        return redirect()->route("portal.sendConfirmation");
    }

    public function activate(Request $request)
    {
        if (!$request->has('data')) {
            return redirect()->route("portal.expire");
        }

        try {
            $data = decrypt($request->data);
        } catch (\Throwable $th) {
            return redirect()->route("portal.expire");
        }

        $employee = null;

        if (is_array($data)) {
            if (!empty($data['id'])) {
                $employee = ExternalEmployees::find($data['id']);
            }
            if (!$employee && !empty($data['email'])) {
                $employee = ExternalEmployees::where('email', $data['email'])->first();
            }
        } elseif (is_numeric($data)) {
            $employee = ExternalEmployees::find($data);
        } elseif (is_string($data)) {
            $employee = ExternalEmployees::where('email', $data)->first();
        }

        if (!$employee) {
            return redirect()->route("portal.expire");
        }

        // Do NOT create/save user, simply mark the email_verified_at date
        if (is_null($employee->email_verified_at)) {
            $employee->update([
                'email_verified_at' => Carbon::now(),
            ]);
        }

        return redirect()->route("portal.AccountActivated", ["biometric_id" => $employee->biometric_id]);
    }

    public function AccountActivated(Request $request)
    {
        session()->forget("user");
        return view("AccountActivated", ["biometric_id" => $request->biometric_id]);
    }

    public function checkEmail()
    {
        $user = session()->get("user");
        if (!$user) {
            return redirect()->route("portal.login");
        }
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

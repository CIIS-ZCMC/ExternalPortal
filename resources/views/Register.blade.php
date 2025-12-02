<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ZCMC External Portal - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1064a3, #1cb572);

            display: flex;
            justify-content: center;
            align-items: center;
        }


        .register-card {
            max-width: 850px;
            width: 700px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.08);
        }

        .header-title {
            font-weight: 600;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="register-card" id="registerBox">

        <h2 class="header-title">ZCMC External Employee Portal Registration</h2>

        <form action="/register" method="POST" id="registerForm">
            <!-- Laravel CSRF -->
            @csrf

            <!-- Personal Information -->
            <div class="row mb-3">
                <div class="col-md-12 mb-2 col-lg-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-12 col-lg-6">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 mb-2 col-lg-6">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name') }}">
                </div>
                <div class="col-md-12 col-lg-6">
                    <label class="form-label">Extension (Jr., III, etc.)</label>
                    <input type="text" class="form-control" name="ext_name" value="{{ old('ext_name') }}">
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required value="{{ old('email') ?? $email }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact_number" required
                    value="{{ old('contact_number') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Complete Address</label>
                <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
            </div>

            <!-- Work Info -->
            <div class="row mb-3">
                <div class="col-md-12 mb-2 col-lg-6">
                    <label class="form-label">Agency</label>
                    <label class="form-label">Government Agency</label>
                    <select class="form-select" name="agency" required value="{{ old('agency') }}">
                        <option value="" selected disabled>-- Select Government Agency --</option>

                        <!-- National Government Agencies -->
                        <option value="Department of Health (DOH)">Department of Health (DOH)</option>
                        <option value="Department of Education (DepEd)">Department of Education (DepEd)</option>
                        <option value="Department of the Interior and Local Government (DILG)">Department of the
                            Interior and Local Government (DILG)</option>
                        <option value="Department of Social Welfare and Development (DSWD)">Department of Social Welfare
                            and Development (DSWD)</option>
                        <option value="Department of Finance (DOF)">Department of Finance (DOF)</option>
                        <option value="Department of Budget and Management (DBM)">Department of Budget and Management
                            (DBM)</option>
                        <option value="Department of Science and Technology (DOST)">Department of Science and Technology
                            (DOST)</option>
                        <option value="Department of Tourism (DOT)">Department of Tourism (DOT)</option>
                        <option value="Department of Justice (DOJ)">Department of Justice (DOJ)</option>
                        <option value="Department of Agriculture (DA)">Department of Agriculture (DA)</option>
                        <option value="Department of Labor and Employment (DOLE)">Department of Labor and Employment
                            (DOLE)</option>
                        <option value="Department of National Defense (DND)">Department of National Defense (DND)
                        </option>
                        <option value="Department of Transportation (DOTr)">Department of Transportation (DOTr)</option>
                        <option value="Department of Public Works and Highways (DPWH)">Department of Public Works and
                            Highways (DPWH)</option>
                        <option value="Department of Trade and Industry (DTI)">Department of Trade and Industry (DTI)
                        </option>
                        <option value="Department of Environment and Natural Resources (DENR)">Department of Environment
                            and Natural Resources (DENR)</option>

                        <!-- Health System Agencies -->
                        <option value="PhilHealth">PhilHealth</option>
                        <option value="Food and Drug Administration (FDA)">Food and Drug Administration (FDA)</option>
                        <option value="Professional Regulation Commission (PRC)">Professional Regulation Commission
                            (PRC)</option>
                        <option value="Commission on Audit (COA)">Commission on Audit (COA)</option>
                        <option value="Government Service Insurance System (GSIS)">Government Service Insurance System
                            (GSIS)</option>
                        <option value="Civil Service Commission (CSC)">Civil Service Commission (CSC)</option>

                        <!-- Local Government -->
                        <option value="Provincial Government">Provincial Government</option>
                        <option value="City Government">City Government</option>
                        <option value="Municipal Government">Municipal Government</option>
                        <option value="Barangay Government">Barangay Government</option>

                        <!-- Special Agencies -->
                        <option value="Commission on Elections (COMELEC)">Commission on Elections (COMELEC)</option>
                        <option value="Commission on Higher Education (CHED)">Commission on Higher Education (CHED)
                        </option>
                        <option value="Technical Education and Skills Development Authority (TESDA)">Technical Education
                            and Skills Development Authority (TESDA)</option>
                        <option value="Philippine National Police (PNP)">Philippine National Police (PNP)</option>
                        <option value="Armed Forces of the Philippines (AFP)">Armed Forces of the Philippines (AFP)
                        </option>

                        <!-- Hospital / Medical Facilities -->
                        <option value="Zamboanga City Medical Center (ZCMC)">Zamboanga City Medical Center (ZCMC)
                        </option>
                        <option value="Other Hospital / Medical Institution">Other Hospital / Medical Institution
                        </option>

                        <!-- Catch-all -->
                        <option value="Other Government Agency">Other Government Agency</option>
                    </select>

                </div>

                <div class="col-md-12 col-lg-6">
                    <label class="form-label">Position</label>
                    <input type="text" class="form-control" name="position" value="{{ old('position') }}">
                </div>
            </div>

            <!-- Login Credentials -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required value="{{ old('username') }}">
            </div>

            <div class="row mb-3">
                <div class="col-md-12 mb-2 col-lg-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required
                        value="{{ old('password') }}">
                </div>
                <div class="col-md-12 col-lg-6">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password_confirmation" required>

                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" id="registerBtn" class="btn btn-primary w-100">Register</button>

            <div class="text-center mt-3">
                <a href="/portal/login">Already have an account? Login</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById("registerForm");
        const button = document.getElementById("registerBtn");
        const wrapper = document.getElementById("registerBox");

        form.addEventListener("submit", () => {
            button.classList.add("loading");
            wrapper.classList.add("submitting");
            button.disabled = true;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

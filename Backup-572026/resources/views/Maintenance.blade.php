<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | We'll be back soon!</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: inline-block;
            animation: wrench 2.5s ease infinite;
        }

        @keyframes wrench {
            0%, 100% { transform: rotate(0deg); }
            20% { transform: rotate(-15deg); }
            40% { transform: rotate(15deg); }
            60% { transform: rotate(-5deg); }
            80% { transform: rotate(5deg); }
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-box {
            background-color: #ebf5fb;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
            color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="icon">🛠️</div>
        <h1>We’re busy updating!</h1>
        <p>
            The system is currently undergoing scheduled maintenance to improve your experience. 
            We apologize for the inconvenience and appreciate your patience.
        </p>
        
        <div class="status-box">
            <!-- <div class="loader"></div> -->
            Expected to be back online shortly.
        </div>
    </div>

</body>
</html>
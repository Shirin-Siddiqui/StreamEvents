<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stream Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .login-options {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .login-button {
            display: inline-block;
            background-color: #4285f4;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #357ae8;
        }
    </style>
</head>
<body>
    <div class="welcome-message">Welcome to Stream Events</div>

    <div class="login-options">
        <button class="login-button" id="google-login-button">Login with Google</button>
        <!-- Add other login options here (email/password) -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const googleLoginButton = document.getElementById('google-login-button');

            googleLoginButton.addEventListener('click', function() {
                // Redirect to the Google OAuth endpoint
                window.location.href = '/auth/google';
            });
        });
    </script>
</body>
</html>

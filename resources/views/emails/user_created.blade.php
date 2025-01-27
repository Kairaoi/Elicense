<!DOCTYPE html>
<html>
<head>
    <title>Your Account Details</title>
</head>
<body>
    <h1>Hello, {{ $userName }}!</h1>
    <p>Your account has been successfully created. Below are your login credentials:</p>
    <ul>
        <li>Email: {{ $userEmail }}</li>
        <li>Password: {{ $password }}</li>
    </ul>
    <p>Please log in and change your password as soon as possible.</p>
    <p>Thank you for joining us!</p>
</body>
</html>

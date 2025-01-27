<!-- resources/views/emails/credentials.blade.php -->
<h2>Welcome {{ $credentials['name'] }}!</h2>

<p>Your application has been received successfully. Here are your login credentials:</p>

<p><strong>Username:</strong> {{ $credentials['username'] }}</p>
<p><strong>Password:</strong> {{ $credentials['password'] }}</p>

<p>Please keep these credentials safe. You can use them to log in to your account and track your application status.</p>

<p>Best regards,<br>
The Application Team</p>
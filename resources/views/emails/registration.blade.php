{{-- resources/views/emails/registration.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <title>Welcome to Finance Manager</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .credentials { background: #fff; padding: 15px; border-left: 4px solid #4F46E5; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Welcome to Finance Manager!</h1>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>

        <p>Your account has been successfully created by an administrator.</p>

        <div class="credentials">
            <h3>Your Login Credentials:</h3>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
        </div>

        <p>You can now login to your account and start managing your finances effectively.</p>

        <p style="text-align: center; margin: 25px 0;">
            <a href="{{ url('/login') }}"
               style="background: #4F46E5; color: white; padding: 12px 24px;
                          text-decoration: none; border-radius: 5px; display: inline-block;">
                Login to Your Account
            </a>
        </p>

        <p><strong>Important:</strong> For security reasons, please change your password after your first login.</p>
    </div>

    <div class="footer">
        <p>Thank you for using Finance Manager!</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</div>
</body>
</html>

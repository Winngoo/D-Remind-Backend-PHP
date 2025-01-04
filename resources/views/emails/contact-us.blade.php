<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: #1D24CA;
            color: #fff;
            text-align: center;
            padding: 20px 10px;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin: 10px 0;
            line-height: 1.6;
        }
        .content strong {
            color: #1D24CA;
        }
        .footer {
            background: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #777;
        }
        .footer a {
            color: #1D24CA;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            New Contact Us Submission
        </div>
        <div class="content">
            <p><strong>First Name:</strong> {{ $contact->first_name }}</p>
            <p><strong>Last Name:</strong> {{ $contact->last_name }}</p>
            <p><strong>Email:</strong> {{ $contact->email }}</a></p>
            <p><strong>Message:</strong></p>
            <p>{{ $contact->message }}</p>
        </div>
        <div class="footer">
            <p>This message was sent from the Contact Us form on your website.</p>
        </div>
    </div>
</body>
</html>

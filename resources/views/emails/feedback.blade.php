<!DOCTYPE html>
<html>
<head>
    <title>New Feedback Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            color: #0056b3;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Feedback Received</h1>
        <p><span class="label">Name:</span> {{ $name }}</p>
        <p><span class="label">Email:</span> {{ $email }}</p>
        <p><span class="label">Title:</span> {{ $title }}</p>
        <p><span class="label">Description:</span> {{ $description }}</p>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
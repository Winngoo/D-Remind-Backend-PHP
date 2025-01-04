<!DOCTYPE html>
<html>
<head>
    <title>Verify Account</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <p>Hello,</p>
    <p>Click the link below to verify your account :</p>
    <a href="{{ $viewLink }}">{{ $viewLink }}</a>
</body>
</html>
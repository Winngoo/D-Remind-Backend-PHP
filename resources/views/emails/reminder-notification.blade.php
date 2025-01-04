<!DOCTYPE html>
<html>
<head>
    <title>Reminder Notification</title>
</head>
<body>
    <h1>{{ $emailData['title'] }}</h1>
    
    <p>Category: {{ $emailData['category'] }}</p>
    <p>Subcategory: {{ $emailData['subcategory'] }}</p>
    <p>Due Date: {{ $emailData['due_date'] }}</p>
    <p>Remaining Time: {{ $emailData['time_left'] }}</p>
    <p>Time: {{ $emailData['time'] }}</p>
    <p>Description: {{ $emailData['description'] }}</p>
    <p>Provider: {{ $emailData['provider'] }}</p>
    <p>Cost: ${{ $emailData['cost'] }}</p>

    <p>Please take action on this reminder.</p>
</body>
</html>
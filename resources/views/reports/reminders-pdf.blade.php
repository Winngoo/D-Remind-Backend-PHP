<!DOCTYPE html>
<html>
<head>
    <title>Users Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Reminders Report</h1>
    <table>
        <thead>
            <tr>
                <th>Reminder Title</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Due Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Provider</th>
                <th>Cost</th>
                <th>Payment Frequency</th>
                <th>Reminder Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reminders as $reminder)
                <tr>
                    <td>{{ $reminder->title }}</td>
                    <td>{{ $reminder->category }}</td>
                    <td>{{ $reminder->subcategory }}</td>
                    <td>{{ $reminder->due_date }}</td>
                    <td>{{ $reminder->time }}</td>
                    <td>{{ $reminder->description }}</td>
                    <td>{{ $reminder->provider }}</td>
                    <td>{{ $reminder->cost }}</td>
                    <td>{{ $reminder->payment_frequency }}</td>
                    <td>{{ $reminder->reminder_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

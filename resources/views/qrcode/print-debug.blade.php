<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Debug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>QR Code Debug Information</h1>
    
    <h2>User Data</h2>
    <table>
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>User ID</td>
            <td>{{ $user->user_id ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>{{ $user->name ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{ $user->email ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Role</td>
            <td>{{ $user->role->name ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>{{ $user->address ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Date of Birth</td>
            <td>{{ $user->date_of_birth ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Gender</td>
            <td>{{ $user->gender ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Mobile Number</td>
            <td>{{ $user->mobile_number ?? 'Not set' }}</td>
        </tr>
    </table>
    
    <h2>QR Code Data</h2>
    <table>
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>QR Code</td>
            <td>{{ $qrCode->code ?? 'Not set' }}</td>
        </tr>
        <tr>
            <td>Created At</td>
            <td>{{ $qrCode->created_at ?? 'Not set' }}</td>
        </tr>
    </table>
    
    <h2>All User Data (Raw)</h2>
    <pre>{{ print_r($user->toArray(), true) }}</pre>
    
    <h2>All QR Code Data (Raw)</h2>
    <pre>{{ print_r($qrCode->toArray(), true) }}</pre>
    
    <h2>User Data From Controller</h2>
    <pre>{{ print_r($userData ?? [], true) }}</pre>
</body>
</html> 
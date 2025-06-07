<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #991b1b;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin: 0 auto;
            display: block;
        }
        h1 {
            color: #991b1b;
            font-size: 24px;
            margin: 5px 0;
        }
        h2 {
            color: #666;
            font-size: 16px;
            margin: 5px 0 15px 0;
            font-weight: normal;
        }
        .alert-box {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f5f5f5;
            color: #333;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .critical {
            color: #991b1b;
            font-weight: bold;
        }
        .warning {
            color: #e65100;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
            border: 1px dashed #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Knights of the Altar - Attendance Monitoring System</h1>
            <h1>{{ $title }}</h1>
            <h2>{{ $subtitle }}</h2>
        </div>
        
        <div class="alert-box">
            <p><strong>Attendance Rule:</strong> A member is only marked as absent for a Sunday if they miss ALL 4 masses on that day. Attending at least one mass on Sunday will count as present for that Sunday.</p>
        </div>
        
        @if(count($members) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Consecutive Absences</th>
                        <th>Last Notification</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        <tr>
                            <td>{{ $member['name'] }}</td>
                            <td>{{ $member['email'] }}</td>
                            <td>{{ $member['phone'] }}</td>
                            <td class="{{ $member['absences'] >= 4 ? 'critical' : 'warning' }}">
                                {{ $member['absences'] }}
                            </td>
                            <td>{{ $member['last_notification'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="summary">
                <h3>Detailed Information</h3>
                @foreach($members as $member)
                    <div style="margin-bottom: 15px; border-bottom: 1px dashed #ddd; padding-bottom: 10px;">
                        <p><strong>{{ $member['name'] }}</strong></p>
                        <p><strong>Email:</strong> {{ $member['email'] }}</p>
                        <p><strong>Phone:</strong> {{ $member['phone'] }}</p>
                        <p><strong>Address:</strong> {{ $member['address'] }}</p>
                        <p><strong>Consecutive Sunday Absences:</strong> 
                            <span class="{{ $member['absences'] >= 4 ? 'critical' : 'warning' }}">
                                {{ $member['absences'] }}
                            </span>
                        </p>
                        <p><strong>Missed Sundays:</strong> {{ $member['missed_sundays'] }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-data">
                <p>No members found with the specified consecutive Sunday absences.</p>
            </div>
        @endif
        
        <div class="footer">
            <p>Report generated on: {{ $today }}</p>
            <p>Knights of the Altar - Attendance Monitoring System</p>
        </div>
    </div>
</body>
</html> 
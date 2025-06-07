<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Warning</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3949ab;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.8em;
            text-align: center;
            color: #777;
        }
        .dates {
            margin: 15px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-left: 3px solid #3949ab;
        }
        .warning {
            font-weight: bold;
            color: #d32f2f;
        }
        .action {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8eaf6;
            border-radius: 5px;
        }
        .logo {
            max-width: 150px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="KofA Logo" class="logo">
        <h2>Knights of the Altar</h2>
    </div>

    <div class="content">
        <p>Dear {{ $user->name }},</p>

        <p>We hope this message finds you well. We're reaching out because our records indicate that you have been <span class="warning">absent from Sunday masses for three consecutive weeks</span>.</p>

        <div class="dates">
            <p><strong>Missed Sunday Dates:</strong></p>
            <ul>
                @foreach($missedDates as $date)
                    <li>{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</li>
                @endforeach
            </ul>
        </div>

        <p>As a valued member of our organization, your presence and participation in Sunday masses are important. We understand that circumstances can sometimes prevent attendance, but we want to ensure you're aware of our attendance policy:</p>
        
        <ul>
            <li>Each Sunday has 4 masses</li>
            <li>Attending at least one of these masses counts as present for that Sunday</li>
            <li>Missing all 4 masses counts as an absence</li>
        </ul>

        <div class="action">
            <p><strong>Required Action:</strong> According to our organization's guidelines, you will need to undergo counseling at our next meeting on {{ \Carbon\Carbon::now()->next('Sunday')->format('F j, Y') }}.</p>
            
            <p>This counseling session is intended to be supportive and helpful, not punitive. We want to understand if there are any challenges you're facing and how we can assist you.</p>
        </div>

        <p>If there are specific circumstances preventing your attendance or if you need any form of assistance, please don't hesitate to contact us at <a href="mailto:kofa.attendance@gmail.com">kofa.attendance@gmail.com</a> or call our Secretary at (123) 456-7890.</p>

        <p>We look forward to seeing you at the next Sunday mass.</p>

        <p>Sincerely,<br>
        Knights of the Altar Council</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the Knights of the Altar Attendance Monitoring System.</p>
        <p>&copy; {{ date('Y') }} Knights of the Altar. All rights reserved.</p>
    </div>
</body>
</html> 
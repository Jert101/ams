<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serious Attendance Warning</title>
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
            border-bottom: 2px solid #d32f2f;
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
            background-color: #ffebee;
            border-left: 3px solid #d32f2f;
        }
        .warning {
            font-weight: bold;
            color: #d32f2f;
        }
        .action {
            margin-top: 20px;
            padding: 15px;
            background-color: #ffebee;
            border-radius: 5px;
            border: 1px solid #d32f2f;
        }
        .logo {
            max-width: 150px;
            margin: 0 auto;
            display: block;
        }
        .urgent {
            display: inline-block;
            background-color: #d32f2f;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="KofA Logo" class="logo">
        <h2>Knights of the Altar</h2>
        <p class="urgent">URGENT ATTENTION REQUIRED</p>
    </div>

    <div class="content">
        <p>Dear {{ $user->name }},</p>

        <p>We are contacting you with <span class="warning">serious concern regarding your attendance</span>. Our records show that you have been <span class="warning">absent from Sunday masses for four or more consecutive weeks</span>.</p>

        <div class="dates">
            <p><strong>Missed Sunday Dates:</strong></p>
            <ul>
                @foreach($missedDates as $date)
                    <li>{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</li>
                @endforeach
            </ul>
        </div>

        <p>As a committed member of our organization, your consistent attendance at Sunday masses is not only expected but is a fundamental aspect of your membership. Please remember our attendance policy:</p>
        
        <ul>
            <li>Each Sunday has 4 masses</li>
            <li>Attending at least one of these masses counts as present for that Sunday</li>
            <li>Missing all 4 masses counts as an absence</li>
        </ul>

        <div class="action">
            <p><strong>REQUIRED IMMEDIATE ACTION:</strong></p>
            <ol>
                <li>You must attend a <strong>serious counseling session</strong> on <strong>{{ \Carbon\Carbon::now()->next('Sunday')->format('F j, Y') }}</strong> immediately following the mass.</li>
                <li>If you fail to attend this counseling session, the council will be obligated to visit you at your home to address this matter in person.</li>
                <li>Please confirm receipt of this email by replying or contacting our Secretary at (123) 456-7890 <strong>within 24 hours</strong>.</li>
            </ol>
        </div>

        <p>We understand that there might be legitimate reasons for your absence. If you are facing any difficulties, health issues, or other challenges that are preventing your attendance, please inform us immediately so that we can provide appropriate support.</p>

        <p>Your membership in the Knights of the Altar is valued, but consistent participation is a requirement to maintain active membership status. Please take this notification seriously and take the necessary steps to address this situation.</p>

        <p>For any clarification or assistance, please contact us at <a href="mailto:kofa.attendance@gmail.com">kofa.attendance@gmail.com</a> or call our Secretary at (123) 456-7890 as soon as possible.</p>

        <p>Sincerely,<br>
        Knights of the Altar Council</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the Knights of the Altar Attendance Monitoring System.</p>
        <p>&copy; {{ date('Y') }} Knights of the Altar. All rights reserved.</p>
    </div>
</body>
</html> 
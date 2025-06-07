<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Election Results: Congratulations!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 15px;
        }
        h1 {
            color: #991b1b;
            margin-bottom: 20px;
        }
        .congrats {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #4b5563;
        }
        .details {
            background-color: #f9fafb;
            border-left: 4px solid #991b1b;
            padding: 15px;
            margin-bottom: 25px;
        }
        .details h2 {
            color: #4b5563;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .details p {
            margin: 8px 0;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('kofa.png') }}" alt="KofA Logo">
        <h1>Election Results</h1>
    </div>
    
    <p class="congrats">Congratulations, {{ $candidate->user->name }}!</p>
    
    <p>We are pleased to inform you that you have been elected as <strong>{{ $position->title }}</strong> in our recent election.</p>
    
    <div class="details">
        <h2>Election Details</h2>
        <p><strong>Position:</strong> {{ $position->title }}</p>
        <p><strong>Votes Received:</strong> {{ $voteCount }}</p>
        <p><strong>Election Date:</strong> {{ $position->electionSetting->voting_end_date->format('F j, Y') }}</p>
    </div>
    
    <p>Your platform and qualifications were recognized by your peers, and they have placed their trust in you to fulfill this important role. We are confident that you will bring dedication and excellence to this position.</p>
    
    <p>The organization will be in touch soon regarding the next steps, including an official induction ceremony and transition briefing.</p>
    
    <p>Thank you for your commitment to serving our community.</p>
    
    <p>Best regards,<br>
    KofA Admin Team</p>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Knights of Altar Association (KofA). All rights reserved.</p>
    </div>
</body>
</html> 
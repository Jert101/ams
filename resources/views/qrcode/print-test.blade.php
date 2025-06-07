<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple QR Code Print Test</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            color: #b91c1c;
            text-align: center;
        }
        .user-info {
            margin: 20px 0;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 8px;
        }
        .qr-container {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #b91c1c;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .button:hover {
            background-color: #9b1c1c;
        }
        .button-gray {
            background-color: #6b7280;
        }
        .button-gray:hover {
            background-color: #4b5563;
        }
        .controls {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simple QR Code Print Test</h1>
        
        <div class="user-info">
            <p><strong>User Name:</strong> @php echo $user->name; @endphp</p>
            <p><strong>User ID:</strong> @php echo $user->user_id; @endphp</p>
            <p><strong>Email:</strong> @php echo $user->email; @endphp</p>
            <p><strong>Role:</strong> @php echo $user->role ? $user->role->name : 'Member'; @endphp</p>
            <p><strong>QR Code:</strong> @php echo $qrCode->code; @endphp</p>
        </div>
        
        <div class="qr-container">
            <div id="qrcode"></div>
        </div>
        
        <div class="controls">
            <button onclick="window.print()" class="button">Print This Page</button>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.qrcode.manage') }}" class="button button-gray">Back to QR Management</a>
            @else
            <a href="{{ route('dashboard') }}" class="button button-gray">Back to Dashboard</a>
            @endif
        </div>
</div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "@php echo $qrCode->code; @endphp",
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>

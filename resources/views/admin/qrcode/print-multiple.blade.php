<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Codes</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: white;
        }
        .page-container {
            width: 210mm;
            padding: 5mm;
            box-sizing: border-box;
            page-break-after: always;
        }
        .qr-card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10mm;
        }
        .qr-card {
            border: 1px solid #ccc;
            border-radius: 5mm;
            padding: 10mm;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            page-break-inside: avoid;
            position: relative;
            overflow: hidden;
        }
        .qr-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 30mm;
            height: 30mm;
            background: linear-gradient(to bottom left, #dc2626 50%, transparent 50%);
            z-index: 0;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 5mm;
            width: 100%;
            z-index: 1;
        }
        .logo {
            width: 15mm;
            height: 15mm;
            margin-right: 5mm;
            object-fit: contain;
        }
        .title {
            flex-grow: 1;
        }
        .title h1 {
            margin: 0;
            font-size: 14pt;
            color: #b91c1c;
        }
        .title h2 {
            margin: 0;
            font-size: 10pt;
            color: #4b5563;
        }
        .qr-container {
            margin: 5mm 0;
            padding: 5mm;
            background-color: white;
            border-radius: 2mm;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .user-info {
            display: flex;
            align-items: center;
            width: 100%;
            margin-top: 5mm;
            z-index: 1;
        }
        .avatar {
            width: 20mm;
            height: 20mm;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 5mm;
            border: 1px solid #e5e7eb;
        }
        .user-details {
            flex-grow: 1;
        }
        .user-details h3 {
            margin: 0 0 2mm 0;
            font-size: 12pt;
        }
        .user-details p {
            margin: 0;
            font-size: 8pt;
            color: #6b7280;
        }
        .code {
            font-family: monospace;
            font-size: 8pt;
            color: #374151;
            margin-top: 2mm;
            text-align: center;
            width: 100%;
            z-index: 1;
        }
        .watermark {
            position: absolute;
            bottom: 5mm;
            right: 5mm;
            font-size: 7pt;
            color: #9ca3af;
            z-index: 1;
        }
        
        /* Hide elements when printing */
        @media print {
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 20px; background-color: #f9fafb; position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0; font-size: 18px;">Print QR Codes</h1>
        <div>
            <button onclick="window.print()" style="background-color: #b91c1c; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; margin-right: 10px;">Print</button>
            <a href="{{ route('admin.qrcode.manage') }}" style="background-color: #6b7280; color: white; border: none; padding: 10px 15px; border-radius: 5px; text-decoration: none;">Back</a>
        </div>
    </div>

    <div style="margin-top: 80px;">
        @php $counter = 0; @endphp
        
        @foreach($users->chunk(4) as $userChunk)
            <div class="page-container">
                <div class="qr-card-grid">
                    @foreach($userChunk as $user)
                        <div class="qr-card">
                            <div class="header">
                                <img src="{{ asset('kofa.png') }}" alt="Logo" class="logo">
                                <div class="title">
                                    <h1>KofA AMS</h1>
                                    <h2>Attendance Management System</h2>
                                </div>
                            </div>
                            
                            <div class="qr-container">
                                <div id="qrcode-{{ $user->user_id }}"></div>
                            </div>
                            
                            <div class="user-info">
                                <img src="{{ $user->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ $user->name }}" class="avatar">
                                <div class="user-details">
                                    <h3>{{ $user->name }}</h3>
                                    <p>ID: {{ $user->user_id }}</p>
                                    <p>Role: {{ $user->role->name ?? 'Member' }}</p>
                                </div>
                            </div>
                            
                            <div class="code">{{ $user->qrCode->code }}</div>
                            
                            <div class="watermark">Generated on {{ date('F j, Y') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @php $counter += count($userChunk); @endphp
            
            @if($counter < count($users))
                <div class="page-break"></div>
            @endif
        @endforeach
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($users as $user)
                new QRCode(document.getElementById("qrcode-{{ $user->user_id }}"), {
                    text: "{{ $user->qrCode->code }}",
                    width: 120,
                    height: 120,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            @endforeach
            
            // Auto print after load
            window.addEventListener('load', function() {
                setTimeout(function() {
                    // Uncomment the line below if you want automatic printing
                    // window.print();
                }, 1000);
            });
        });
    </script>
</body>
</html>

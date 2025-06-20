<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print QR Code ID Card</title>
    <?php 
    // Simple debug check to verify PHP is processing correctly
    // echo "Debug - User ID: " . ($user->user_id ?? 'Not set');
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #dc2626;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }
        
        .card-container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        
        .card-header {
            background: linear-gradient(to right, #ffffff, #ffffff 80%, #dc2626 80%, #dc2626);
            padding: 20px;
            display: flex;
            align-items: center;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background-color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .logo svg {
            width: 30px;
            height: 30px;
            fill: white;
        }
        
        .header-text h1 {
            margin: 0;
            font-size: 20px;
            color: #dc2626;
        }
        
        .header-text h2 {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        
        .qr-section {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        #qrcode {
            margin: 10px 0;
        }
        
        .qr-caption {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        
        .info-section {
            padding: 20px;
            border-top: 1px solid #eee;
        }
        
        .user-info {
            display: flex;
            align-items: flex-start;
        }
        
        .avatar {
            width: 70px;
            height: 70px;
            background-color: #f5f5f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            border: 1px solid #eee;
        }
        
        .avatar svg {
            width: 40px;
            height: 40px;
            fill: #999;
        }
        
        .details {
            flex: 1;
        }
        
        .details h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .details table tr {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .details table tr:last-child {
            border-bottom: none;
        }
        
        .details table td {
            padding: 5px 0;
            vertical-align: top;
            font-size: 14px;
        }
        
        .details table td:first-child {
            font-weight: bold;
            width: 100px;
        }
        
        .card-footer {
            padding: 10px 20px;
            background-color: #f9f9f9;
            font-size: 12px;
            color: #999;
            text-align: right;
        }
        
        @media print {
            body {
                background-color: white;
            }
            
            .print-button {
                display: none;
            }
            
            .card-container {
                box-shadow: none;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">Print QR Code</button>
    
    <div class="card-container">
        <div class="card-header">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-14v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>KofA AMS</h1>
                <h2>Attendance Management System</h2>
            </div>
        </div>
        
        <div class="qr-section">
            <div id="qrcode"></div>
            <div class="qr-caption">Scan this code for attendance</div>
        </div>
        
        <div class="info-section">
            <div class="user-info">
                <div class="avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                <div class="details">
                    <h3><?php echo $user->name ?? 'N/A'; ?></h3>
                    <table>
                        <tr>
                            <td>ID:</td>
                            <td><?php echo $user->user_id ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td><?php echo $user->email ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td><?php echo $user->address ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>Birth Date:</td>
                            <td><?php echo $user->date_of_birth ? date('F j, Y', strtotime($user->date_of_birth)) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>Gender:</td>
                            <td><?php echo $user->gender ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>Mobile:</td>
                            <td><?php echo $user->mobile_number ?? 'N/A'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card-footer">
            Generated on <?php echo date('F j, Y'); ?> | QR Code: <?php echo $qrCode->code ?? 'N/A'; ?>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: "<?php echo addslashes($qrCode->code ?? 'ERROR'); ?>",
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

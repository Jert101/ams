@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Your QR Code</h1>
        <a href="{{ route('member.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
        <div class="text-center mb-6">
            <p class="text-lg font-medium text-gray-700 mb-1">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-500">ID: {{ auth()->user()->id }}</p>
            @if($qrCode->last_used_at)
                <p class="text-xs text-gray-500 mt-1">Last used: {{ $qrCode->last_used_at->format('M d, Y h:i A') }}</p>
            @endif
        </div>
        
        <div class="mb-6">
            <div id="qrcode-container" class="flex justify-center"></div>
        </div>
        
        <div class="text-center">
            <p class="text-gray-700 mb-2">QR Code: <span class="font-mono">{{ $qrCode->code }}</span></p>
            <p class="text-sm text-gray-500">Please present this QR code to the officer for attendance</p>
        </div>
        
        <div class="mt-6 flex justify-center">
            <button id="download-qr" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                Download QR Code
            </button>
            <button id="print-qr" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Print QR Code
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate QR code
        const qrCodeContainer = document.getElementById('qrcode-container');
        const qrCode = new QRCode(qrCodeContainer, {
            text: "{{ $qrCode->code }}",
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        // Download function
        document.getElementById('download-qr').addEventListener('click', function() {
            const canvas = document.querySelector("#qrcode-container canvas");
            const link = document.createElement('a');
            link.download = 'qrcode-{{ auth()->user()->id }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
        
        // Print function
        document.getElementById('print-qr').addEventListener('click', function() {
            window.print();
        });
    });
</script>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        #qrcode-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>
@endpush
@endsection

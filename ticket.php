<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departure Board - Packing List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face {
            font-family: 'DotMatrix';
            src: url('https://fonts.gstatic.com/s/dotgothic16/v15/v6-QGYjBJFKgyw5nSoDAGH7wJiEjQA.woff2') format('woff2');
        }
        
        .dot-matrix {
            font-family: 'DotMatrix', monospace;
            letter-spacing: 2px;
        }
        
        @keyframes flicker {
            0%, 92%, 100% { opacity: 1; }
            90%, 96% { opacity: 0.9; }
        }
        
        .flicker {
            animation: flicker 4s infinite;
        }
        
        .board-row {
            position: relative;
            height: 40px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .board-row::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.15),
                rgba(0, 0, 0, 0.15) 1px,
                transparent 1px,
                transparent 2px
            );
            pointer-events: none;
        }
        
        .board-container {
            background-color: #000000;
            background-image: radial-gradient(rgba(0, 0, 0, 0.7) 2px, transparent 0);
            background-size: 5px 5px;
        }
        
        .glow {
            text-shadow: 0 0 5px rgba(255, 222, 0, 0.5);
        }
    </style>
</head>
<body class="bg-black min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-3xl bg-black border border-gray-800 shadow-lg shadow-yellow-900/10 board-container">
        <!-- Header with airplane icon -->
        <div class="bg-black p-4 flex items-center justify-between border-b border-gray-800">
            <div class="flex items-center">
                <div class="bg-yellow-300 rounded-sm p-1 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                </div>
                <h1 class="text-yellow-300 text-3xl font-bold dot-matrix tracking-widest glow">DEPARTURES</h1>
            </div>
            <div class="text-yellow-300 dot-matrix text-lg">
                <?php echo date('H:i'); ?>
            </div>
        </div>
        
        <!-- Column Headers -->
        <div class="flex text-gray-500 text-sm p-2 border-b border-gray-800 dot-matrix">
            <div class="w-1/5 pl-4">Time</div>
            <div class="w-3/5">Item</div>
            <div class="w-1/5">Status</div>
        </div>
        
        <!-- Departure board -->
        <div class="w-full overflow-hidden flicker">
            <!-- Items -->
            <div class="board-row border-b border-gray-800 bg-black">
                <div class="w-1/5 pl-4">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">08:30</span>
                </div>
                <div class="w-3/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix tracking-wider glow">NOTEBOOK</span>
                </div>
                <div class="w-1/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">READY</span>
                </div>
            </div>
            
            <div class="board-row border-b border-gray-800 bg-black">
                <div class="w-1/5 pl-4">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">09:15</span>
                </div>
                <div class="w-3/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix tracking-wider glow">PEN</span>
                </div>
                <div class="w-1/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">READY</span>
                </div>
            </div>
            
            <div class="board-row border-b border-gray-800 bg-black">
                <div class="w-1/5 pl-4">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">09:45</span>
                </div>
                <div class="w-3/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix tracking-wider glow">TUMBLER</span>
                </div>
                <div class="w-1/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">READY</span>
                </div>
            </div>
            
            <div class="board-row border-b border-gray-800 bg-black">
                <div class="w-1/5 pl-4">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">10:00</span>
                </div>
                <div class="w-3/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix tracking-wider glow">ROSARY</span>
                </div>
                <div class="w-1/5">
                    <span class="text-yellow-300 text-xl font-bold dot-matrix">READY</span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="p-3 text-right text-gray-600 text-xs">
            Parish Youth Day - <?php echo date('Y-m-d'); ?> | shutterstock.com
        </div>
    </div>
</body>
</html>
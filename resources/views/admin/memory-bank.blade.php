@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-[#B22234] mb-6">Memory Bank Management</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Memory Usage Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Memory Usage</h2>
            <div id="memory-usage-container">
                <p class="text-gray-500 italic">Loading memory usage statistics...</p>
            </div>
        </div>
        
        <!-- Cleanup Tools -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Cleanup Tools</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="font-medium mb-2">Clear Categories</h3>
                    <div class="flex flex-wrap gap-2">
                        <button 
                            onclick="clearCategory('attendance')" 
                            class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200"
                        >
                            Attendance Data
                        </button>
                        <button 
                            onclick="clearCategory('sessions')" 
                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                        >
                            Session Data
                        </button>
                        <button 
                            onclick="clearCategory('progress')" 
                            class="px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200"
                        >
                            Progress Data
                        </button>
                        <button 
                            onclick="clearCategory('scans')" 
                            class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200"
                        >
                            Scan History
                        </button>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium mb-2">Clean Old Data</h3>
                    <div class="flex gap-3">
                        <button 
                            onclick="cleanOldData(30)" 
                            class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200"
                        >
                            Clean data older than 30 days
                        </button>
                        <button 
                            onclick="cleanOldData(90)" 
                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200"
                        >
                            Clean data older than 90 days
                        </button>
                    </div>
                </div>
                
                <div class="pt-3 border-t border-gray-200">
                    <h3 class="font-medium text-red-600 mb-2">Danger Zone</h3>
                    <button 
                        onclick="clearAllData()" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Clear All Memory Bank Data
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Recent Operations -->
        <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
            <h2 class="text-xl font-semibold mb-4">Recent Operations</h2>
            <div id="operations-log" class="h-64 overflow-y-auto p-3 bg-gray-50 rounded border border-gray-200 font-mono text-sm">
                <p class="text-gray-500">No operations performed yet.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if MemoryBank is available
        if (!window.MemoryBank || !window.MemoryBankAPI || !window.MemoryCleaner) {
            document.getElementById('memory-usage-container').innerHTML = `
                <div class="p-4 bg-red-100 text-red-700 rounded">
                    <p>Memory Bank API is not available. Please ensure the JavaScript is loaded correctly.</p>
                </div>
            `;
            return;
        }
        
        // Update memory usage display
        updateMemoryUsage();
        
        // Log initialization
        logOperation('Memory Bank admin page initialized');
    });
    
    // Display memory usage statistics
    function updateMemoryUsage() {
        const usageStats = window.MemoryCleaner.getMemoryUsage();
        
        if (!usageStats.available) {
            document.getElementById('memory-usage-container').innerHTML = `
                <div class="p-4 bg-red-100 text-red-700 rounded">
                    <p>Could not retrieve memory usage: ${usageStats.error || 'Unknown error'}</p>
                </div>
            `;
            return;
        }
        
        const html = `
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700">Total Entries:</span>
                    <span class="font-semibold">${usageStats.totalEntries}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700">Total Size:</span>
                    <span class="font-semibold">${usageStats.totalSizeKB} KB</span>
                </div>
                
                <div class="mt-4">
                    <h3 class="font-medium mb-2">Categories</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Attendance Data:</span>
                            <span>${usageStats.categories.attendance.count} entries (${usageStats.categories.attendance.sizeKB} KB)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Session Data:</span>
                            <span>${usageStats.categories.sessions.count} entries (${usageStats.categories.sessions.sizeKB} KB)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Progress Data:</span>
                            <span>${usageStats.categories.progress.count} entries (${usageStats.categories.progress.sizeKB} KB)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Scan History:</span>
                            <span>${usageStats.categories.scans.count} entries (${usageStats.categories.scans.sizeKB} KB)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Other Data:</span>
                            <span>${usageStats.categories.other.count} entries (${usageStats.categories.other.sizeKB} KB)</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h3 class="font-medium mb-2">Storage Usage</h3>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${usageStats.remaining.percentUsed}%"></div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                        ${usageStats.remaining.percentUsed}% used (approximately ${usageStats.remaining.remainingSizeKB} KB remaining)
                    </p>
                </div>
                
                <div class="mt-4 text-right">
                    <button 
                        onclick="updateMemoryUsage()" 
                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm"
                    >
                        Refresh
                    </button>
                </div>
            </div>
        `;
        
        document.getElementById('memory-usage-container').innerHTML = html;
    }
    
    // Clear specific category of data
    function clearCategory(category) {
        if (confirm(`Are you sure you want to clear all ${category} data?`)) {
            const result = window.MemoryCleaner.clearCategories([category]);
            
            if (result.success) {
                logOperation(`Cleared ${result.deletedCount} items in category: ${category}`);
                updateMemoryUsage();
            } else {
                logOperation(`Failed to clear category ${category}: ${result.error}`, true);
            }
        }
    }
    
    // Clean old data
    function cleanOldData(days) {
        if (confirm(`Are you sure you want to clean data older than ${days} days?`)) {
            const result = window.MemoryCleaner.cleanExpiredData(days);
            
            if (result.success) {
                logOperation(`Cleaned ${result.deletedCount} items older than ${days} days`);
                updateMemoryUsage();
            } else {
                logOperation(`Failed to clean old data: ${result.error}`, true);
            }
        }
    }
    
    // Clear all memory bank data
    function clearAllData() {
        if (confirm('WARNING: This will delete ALL memory bank data. This action cannot be undone. Are you absolutely sure?')) {
            const result = window.MemoryCleaner.clearAll();
            
            if (result) {
                logOperation('Cleared all memory bank data');
                updateMemoryUsage();
            } else {
                logOperation('Failed to clear all memory bank data', true);
            }
        }
    }
    
    // Log operation to the operations log
    function logOperation(message, isError = false) {
        const logElement = document.getElementById('operations-log');
        const timestamp = new Date().toLocaleTimeString();
        
        // Clear placeholder if it's the first log
        if (logElement.querySelector('p.text-gray-500')) {
            logElement.innerHTML = '';
        }
        
        const logEntryClass = isError ? 'text-red-600' : '';
        const logEntry = document.createElement('div');
        logEntry.className = logEntryClass;
        logEntry.innerHTML = `<span class="text-gray-500">[${timestamp}]</span> ${message}`;
        
        logElement.insertBefore(logEntry, logElement.firstChild);
    }
</script>
@endpush
@endsection 
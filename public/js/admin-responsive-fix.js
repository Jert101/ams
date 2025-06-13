/**
 * Admin Responsive Fix
 * Additional fixes for admin interface responsiveness
 */

document.addEventListener('DOMContentLoaded', function() {
    // Fix for tables on small screens
    function makeTablesResponsive() {
        // Find all tables that should be responsive
        const tables = document.querySelectorAll('table.table-responsive');
        
        tables.forEach(table => {
            // Get all header cells
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            
            // Add data-label attributes to all body cells if not already present
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (index < headers.length && !cell.hasAttribute('data-label')) {
                        cell.setAttribute('data-label', headers[index]);
                    }
                });
            });
        });
    }
    
    // Fix for flexbox layouts on small screens
    function fixFlexLayouts() {
        // Convert horizontal flex layouts to vertical on small screens
        if (window.innerWidth <= 640) {
            // Header sections with title and buttons
            document.querySelectorAll('.flex.justify-between:not(.flex-col)').forEach(flex => {
                flex.classList.add('flex-col');
                flex.classList.add('items-stretch');
                flex.classList.add('gap-4');
                
                // Make buttons full width
                const buttons = flex.querySelectorAll('a.btn, button.btn, a.bg-red-700, a.bg-blue-500, a.bg-green-500');
                buttons.forEach(button => {
                    button.style.width = '100%';
                    button.style.textAlign = 'center';
                    button.style.justifyContent = 'center';
                });
            });
            
            // Action button groups
            document.querySelectorAll('.flex.space-x-2, .flex.space-x-3, .flex.space-x-4').forEach(flex => {
                flex.classList.add('flex-col');
                flex.classList.add('space-y-2');
                flex.classList.remove('space-x-2');
                flex.classList.remove('space-x-3');
                flex.classList.remove('space-x-4');
                
                // Make buttons better visible
                const buttons = flex.querySelectorAll('a, button');
                buttons.forEach(button => {
                    button.classList.add('py-2');
                    button.classList.add('px-3');
                    button.classList.add('block');
                    button.classList.add('text-center');
                    button.classList.add('w-full');
                });
            });
        }
    }
    
    // Fix form layouts on small screens
    function fixFormLayouts() {
        if (window.innerWidth <= 640) {
            // Make inputs and selects more touch-friendly
            document.querySelectorAll('input, select, textarea').forEach(input => {
                input.style.padding = '0.625rem';
                input.style.fontSize = '16px'; // Prevents iOS zoom
            });
            
            // Fix search forms
            document.querySelectorAll('form.flex').forEach(form => {
                const inputs = form.querySelectorAll('input');
                const buttons = form.querySelectorAll('button, input[type="submit"]');
                
                if (inputs.length > 0 && buttons.length > 0) {
                    form.classList.add('flex-col');
                    form.classList.add('gap-2');
                    
                    buttons.forEach(button => {
                        button.style.width = '100%';
                    });
                }
            });
        }
    }
    
    // Fix card layouts
    function fixCardLayouts() {
        if (window.innerWidth <= 640) {
            // Add more padding to cards
            document.querySelectorAll('.card, .bg-white').forEach(card => {
                if (!card.classList.contains('table-responsive')) {
                    card.style.padding = '1rem';
                }
            });
        }
    }
    
    // Run all fixes
    function applyResponsiveFixes() {
        makeTablesResponsive();
        fixFlexLayouts();
        fixFormLayouts();
        fixCardLayouts();
    }
    
    // Run on page load and window resize
    applyResponsiveFixes();
    window.addEventListener('resize', applyResponsiveFixes);
    
    // Fix sidebar toggle behavior
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar-container');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target) && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    }
}); 
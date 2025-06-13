/**
 * Admin Responsive JavaScript
 * Enhances mobile responsiveness for admin tables
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add data-label attributes to table cells based on their column headers
    const tables = document.querySelectorAll('table.table-responsive');
    
    tables.forEach(table => {
        const headerCells = table.querySelectorAll('thead th');
        const headerLabels = Array.from(headerCells).map(th => th.textContent.trim());
        
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (index < headerLabels.length && !cell.hasAttribute('data-label')) {
                    cell.setAttribute('data-label', headerLabels[index]);
                }
            });
        });
    });
    
    // Fix flex layouts on small screens
    function adjustLayoutsForMobile() {
        const width = window.innerWidth;
        
        // Fix header layout on small screens
        const headerFlex = document.querySelectorAll('.flex.justify-between.items-center');
        headerFlex.forEach(flex => {
            if (width <= 640) {
                flex.classList.add('flex-col');
                flex.classList.add('items-stretch');
                flex.classList.remove('items-center');
            } else {
                flex.classList.remove('flex-col');
                flex.classList.remove('items-stretch');
                flex.classList.add('items-center');
            }
        });
        
        // Fix button containers
        const buttonContainers = document.querySelectorAll('.flex.space-x-2');
        if (width <= 640) {
            buttonContainers.forEach(container => {
                container.classList.add('flex-col');
                container.classList.add('space-y-2');
                container.classList.remove('space-x-2');
            });
        } else {
            buttonContainers.forEach(container => {
                container.classList.remove('flex-col');
                container.classList.remove('space-y-2');
                container.classList.add('space-x-2');
            });
        }
    }
    
    // Run on page load and window resize
    adjustLayoutsForMobile();
    window.addEventListener('resize', adjustLayoutsForMobile);
    
    // Fix sidebar toggle functionality
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar-container');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = sidebarToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    }
}); 
/**
 * Responsive Tables Enhancement
 * 
 * This script automatically makes tables responsive by adding data-label attributes
 * based on the table headers, allowing for proper stacking on mobile devices.
 * 
 * Usage:
 * 1. Add class "table-mobile-friendly" to your tables
 * 2. Include this script
 * 3. Tables will automatically become responsive on mobile devices
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all mobile-friendly tables
    const tables = document.querySelectorAll('.table-mobile-friendly');
    
    tables.forEach(function(table) {
        // Get all headers
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        
        // Process each row in the tbody
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(function(row) {
            const cells = row.querySelectorAll('td');
            
            // Add data-label attribute to each cell based on corresponding header
            cells.forEach(function(cell, index) {
                if (index < headers.length && !cell.hasAttribute('data-label')) {
                    cell.setAttribute('data-label', headers[index]);
                }
            });
        });
    });
    
    // Observer for dynamically added tables
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const newTables = Array.from(mutation.addedNodes)
                    .filter(node => node.nodeType === Node.ELEMENT_NODE)
                    .reduce((tables, element) => {
                        if (element.classList && element.classList.contains('table-mobile-friendly')) {
                            tables.push(element);
                        }
                        const childTables = element.querySelectorAll('.table-mobile-friendly');
                        if (childTables.length) {
                            tables.push(...childTables);
                        }
                        return tables;
                    }, []);
                
                newTables.forEach(function(table) {
                    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(function(row) {
                        const cells = row.querySelectorAll('td');
                        cells.forEach(function(cell, index) {
                            if (index < headers.length && !cell.hasAttribute('data-label')) {
                                cell.setAttribute('data-label', headers[index]);
                            }
                        });
                    });
                });
            }
        });
    });
    
    // Start observing the document with configured parameters
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Helper function for manually processing a specific table
function makeTableResponsive(tableSelector) {
    const table = document.querySelector(tableSelector);
    if (!table) return;
    
    // Add mobile-friendly class if not present
    if (!table.classList.contains('table-mobile-friendly')) {
        table.classList.add('table-mobile-friendly');
    }
    
    // Get all headers
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    
    // Process each row in the tbody
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(function(row) {
        const cells = row.querySelectorAll('td');
        
        // Add data-label attribute to each cell based on corresponding header
        cells.forEach(function(cell, index) {
            if (index < headers.length) {
                cell.setAttribute('data-label', headers[index]);
            }
        });
    });
} 
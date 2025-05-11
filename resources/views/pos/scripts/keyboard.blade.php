<script>
    // ===============================================================
    // Keyboard Shortcuts & Navigation
    // ===============================================================
    function initializeKeyboardShortcuts() {
        console.log('Keyboard shortcuts initialized...');

        function handleShortcuts(e) {
            // Allow shortcuts even when in input fields for specific function keys
            if (e.key === 'F2' || e.key === 'F3' || e.key === 'F8') {
                e.preventDefault();

                switch (e.key) {
                    case 'F2': // Clear Cart
                        console.log('F2 pressed - Clear Cart');
                        const clearBtn = document.getElementById('btn-clear-cart');
                        if (clearBtn) clearBtn.click();
                        break;

                    case 'F3': // Focus Barcode
                        console.log('F3 pressed - Focus Barcode');
                        const barcodeInput = document.getElementById('pos_barcode');
                        if (barcodeInput) barcodeInput.focus();
                        break;

                    case 'F8': // Save Transaction
                        console.log('F8 pressed - Save Transaction');
                        const saveBtn = document.getElementById('btn-save');
                        if (saveBtn) saveBtn.click();
                        break;
                }
            }
        }

        // Add event listener with high priority (capturing phase)
        document.addEventListener('keydown', handleShortcuts, true);
        console.log('Keyboard shortcuts for F2, F3, and F8 are now active.');
    }

    function setupModalKeyboardNavigation() {
        const modal = this;
        const links = modal.querySelectorAll('a.btn-primary');
        let currentIndex = -1;

        modal.addEventListener('keydown', function(e) {
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    currentIndex = Math.min(currentIndex + 1, links.length - 1);
                    links[currentIndex]?.focus();
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    currentIndex = Math.max(currentIndex - 1, 0);
                    links[currentIndex]?.focus();
                    break;

                case 'Escape':
                    e.preventDefault();
                    bootstrap.Modal.getInstance(modal).hide();
                    break;
            }
        });
    }
</script>

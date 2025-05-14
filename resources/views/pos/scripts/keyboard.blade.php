<script>
    // ===============================================================
    // Keyboard Shortcuts & Navigation
    // ===============================================================
    function initializeKeyboardShortcuts() {
        console.log('Keyboard shortcuts initialized...');

        function handleShortcuts(e) {
            // Allow shortcuts even when in input fields for specific function keys
            if (e.key === 'F6' || e.key === 'F1' || e.key === 'F4') {
                e.preventDefault();

                switch (e.key) {
                    case 'F6': // Clear Cart
                        console.log('F6 pressed - Clear Cart');
                        const clearBtn = document.getElementById('btn-clear-cart');
                        if (clearBtn) clearBtn.click();
                        break;

                    case 'F1': // Focus Barcode
                        console.log('F1 pressed - Focus Barcode');
                        const barcodeInput = document.getElementById('pos_barcode');
                        if (barcodeInput) barcodeInput.focus();
                        break;

                    case 'F4': // Save Transaction
                        console.log('F4 pressed - Save Transaction');
                        const saveBtn = document.getElementById('btn-save');
                        if (saveBtn) saveBtn.click();
                        break;
                }
            }
        }

        // Add event listener with high priority (capturing phase)
        document.addEventListener('keydown', handleShortcuts, true);
        console.log('Keyboard shortcuts for F6, F1, and F4 are now active.');
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

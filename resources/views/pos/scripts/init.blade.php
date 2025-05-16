<!-- Core JS -->
<script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('sneat/assets/js/main.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
    // ===============================================================
    // Global Variables & Data Initialization
    // ===============================================================
    let pendingTransactionId = null;

    // Check and initialize cart data if exists
    const cartData = @json(session('cart_data'));
    if (cartData) {
        pendingTransactionId = cartData.pending_transaction_id;
        document.getElementById('pos_invoice_number').value = cartData.invoice_number;
        document.getElementById('pos_customer_id').value = cartData.customer_id;
        document.getElementById('pos_payment_type').value = cartData.payment_type;

        cart = cartData.items;
        cart.forEach(item => {
            productDetails[item.product_id] = {
                id: item.product_id,
                name: item.product_name,
                available_units: item.available_units
            };
        });

        updateCartTable();
        calculateTotals();
    }

    // ===============================================================
    // Document Ready & App Initialization
    // ===============================================================
    document.addEventListener('DOMContentLoaded', () => {
        initializePOS();
        initializeKeyboardShortcuts();
    });

    function initializePOS() {
        setupEventListeners();
        initializeSelect2();
        initializePaymentTypeHandling();
        updateEmptyCartMessage();
        enhanceTableRows();
    }

    // Add Windows styling to Select2
    $(document).ready(function () {
        // Style Select2 with Windows classic look
        $(document).on('select2:open', function () {
            $('.select2-dropdown').css({
                'box-shadow': '2px 2px 3px rgba(0, 0, 0, 0.2)',
                'border': '2px solid #919b9c',
                'border-radius': '0'
            });

            $('.select2-search__field').css({
                'border': '1px solid #919b9c',
                'font-family': 'Tahoma, Arial, sans-serif',
                'padding': '3px 6px',
                'background-color': '#fff',
                'box-shadow': 'inset 1px 1px 2px rgba(0, 0, 0, 0.2)'
            });

            $('.select2-results__options').css({
                'font-family': 'Tahoma, Arial, sans-serif'
            });
        });

        // Add Windows classic click effect to buttons
        $('.btn').on('mousedown', function () {
            $(this).css('box-shadow', 'inset 2px 2px 3px rgba(0, 0, 0, 0.2)');
        }).on('mouseup mouseleave', function () {
            $(this).css('box-shadow', '1px 1px 2px rgba(0, 0, 0, 0.3)');
        });
    });
</script>

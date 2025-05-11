<!-- Core JS -->
<script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('sneat/assets/js/main.js') }}"></script>

<!-- POS Application Script -->
<script>
    // ===============================================================
    // Global Variables & Data Initialization
    // ===============================================================
    let cart = [];
    let productDetails = {};
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

    // ===============================================================
    // Event Listeners & UI Initialization
    // ===============================================================
    function setupEventListeners() {
        // Barcode scanner handling
        document.getElementById('pos_barcode').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                getProduct(this.value);
                this.value = '';
            }
        });

        // Product search
        document.getElementById('pos_search_product').addEventListener('input', handleProductSearch);
        document.getElementById('pos_search_product').addEventListener('keydown', function(e) {
            const productList = document.getElementById('pos_product_list');
            if (productList.style.display === 'block') {
                handleProductListNavigation(e, productList);
            }
        });

        // Start shopping button
        document.getElementById('btn-start-shopping')?.addEventListener('click', function() {
            document.getElementById('pos_search_product').focus();
        });

        // Transaction action buttons
        document.getElementById('btn-save')?.addEventListener('click', saveTransaction);
        document.getElementById('btn-pending')?.addEventListener('click', saveAsPending);
        document.getElementById('btn-show-pending')?.addEventListener('click', showPendingTransactions);
        document.getElementById('btn-clear-cart')?.addEventListener('click', clearCart);

        // Observe cart changes for UI updates
        const cartTableBody = document.querySelector('#cart-table tbody');
        if (cartTableBody) {
            const observer = new MutationObserver(updateEmptyCartMessage);
            observer.observe(cartTableBody, { childList: true });

            const rowObserver = new MutationObserver(enhanceTableRows);
            rowObserver.observe(cartTableBody, { childList: true });
        }

        // Pending transactions modal keyboard navigation
        document.getElementById('pendingTransactionsModal').addEventListener('shown.bs.modal', setupModalKeyboardNavigation);

        // Cash amount input for calculating change
        document.getElementById('pos_cash_amount').addEventListener('input', calculateChange);
    }

    function initializePaymentTypeHandling() {
        // Payment type change handler
        document.getElementById('pos_payment_type').addEventListener('change', function() {
            const isCash = this.value === 'cash';
            document.getElementById('pos_cash_amount_container').style.display = isCash ? 'block' : 'none';
            document.getElementById('pos_change_container').style.display = isCash ? 'block' : 'none';

            // Reset cash amount if not cash payment
            if (!isCash) {
                document.getElementById('pos_cash_amount').value = '';
                document.getElementById('pos_change').value = '';
            }
        });

        // Initialize display based on current payment type
        const event = new Event('change');
        document.getElementById('pos_payment_type').dispatchEvent(event);
    }

    function initializeSelect2() {
        $(document).ready(function() {
            $('#pos_search_product').select2({
                placeholder: 'Cari nama produk..',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('pos.search-product') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    product: item.product_data
                                };
                            }),
                            pagination: data.pagination
                        };
                    },
                    cache: true
                },
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            }).on('select2:select', function(e) {
                const data = e.params.data;
                if (data && data.product) {
                    addProductFromSearch(data.product);
                    $(this).val(null).trigger('change');
                }
            });

            function formatProduct(product) {
                if (!product.id) return product.text;
                if (!product.product || !product.product.default_unit) return product.text;

                const defaultUnit = product.product.default_unit;
                const stockClass = defaultUnit.stock <= 0 ? 'text-danger' : 'text-muted';
                const stockText = defaultUnit.stock <= 0 ? `Stock: ${defaultUnit.stock} (Minus)` :
                    `Stock: ${defaultUnit.stock}`;

                return $(`
                    <div class="product-info">
                        <span class="product-name">${product.product.name}</span>
                        <span class="product-details">
                            ${product.product.barcode || 'No Barcode'} -
                            <span class="${stockClass}">${stockText}</span> -
                            ${formatCurrency(defaultUnit.selling_price)}
                        </span>
                    </div>
                `);
            }

            function formatProductSelection(product) {
                return product.text || 'Cari produk';
            }
        });
    }

    // Tambahkan script untuk memberikan efek Windows Classic pada komponen Select2
    $(document).ready(function(){
        // Gaya Windows classic pada select2
        $(document).on('select2:open', function() {
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

        // Tambahkan efek klik Windows classic pada semua tombol
        $('.btn').on('mousedown', function() {
            $(this).css('box-shadow', 'inset 2px 2px 3px rgba(0, 0, 0, 0.2)');
        }).on('mouseup mouseleave', function() {
            $(this).css('box-shadow', '1px 1px 2px rgba(0, 0, 0, 0.3)');
        });
    });

    // Tambahkan efek suara Windows classic pada klik tombol (opsional)
    function playWindowsSound() {
        const audio = new Audio();
        audio.src = 'data:audio/mp3;base64,SUQzAwAAAAAAJlRQRTEAAAAcAAAAU291bmRKYXkuY29tIFNvdW5kIEVmZmVjdHNUQUxCAAAAGAAAAFNvdW5kSm...';
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Auto-play prevented'));
    }

    // Menambahkan efek suara pada tombol-tombol utama (jika diinginkan)
    document.querySelectorAll('.btn-primary, .btn-warning, .btn-danger').forEach(button => {
        button.addEventListener('click', playWindowsSound);
    });

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

    function handleProductListNavigation(e, productList) {
        const items = productList.querySelectorAll('.product-item');
        const activeItem = productList.querySelector('.product-item.active');
        let nextActive = null;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (!activeItem) {
                    nextActive = items[0];
                } else {
                    const currentIndex = Array.from(items).indexOf(activeItem);
                    nextActive = items[currentIndex + 1] || items[0];
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (!activeItem) {
                    nextActive = items[items.length - 1];
                } else {
                    const currentIndex = Array.from(items).indexOf(activeItem);
                    nextActive = items[currentIndex - 1] || items[items.length - 1];
                }
                break;

            case 'Enter':
                e.preventDefault();
                if (activeItem) {
                    activeItem.click();
                }
                break;
        }

        if (nextActive) {
            if (activeItem) activeItem.classList.remove('active');
            nextActive.classList.add('active');
            nextActive.scrollIntoView({ block: 'nearest' });
        }
    }

    // ===============================================================
    // Product Search & Cart Operations
    // ===============================================================
    async function handleProductSearch() {
        const productList = document.getElementById('pos_product_list');

        if (this.value.length >= 3) {
            try {
                const response = await fetch(`{{ route('pos.search-product') }}?search=${this.value}`);
                const result = await response.json();

                productList.innerHTML = '';

                if (result.success && result.data.length > 0) {
                    productList.style.display = 'block';

                    result.data.forEach(product => {
                        const defaultUnit = product.available_units.find(unit => unit.is_default === 1);
                        if (defaultUnit) {
                            const div = document.createElement('div');
                            div.className = 'product-item';
                            div.innerHTML = `
                                ${product.name}
                                <br>
                                <small class="text-muted">
                                    ${product.barcode || 'No Barcode'} - Stock: ${defaultUnit.stock}
                                    - ${formatCurrency(defaultUnit.selling_price)}
                                </small>
                            `;
                            div.onclick = () => {
                                addProductFromSearch(product);
                                productList.style.display = 'none';
                                this.value = '';
                            };
                            productList.appendChild(div);
                        }
                    });
                } else {
                    productList.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                productList.style.display = 'none';
            }
        } else {
            productList.style.display = 'none';
        }
    }

    async function getProduct(barcode) {
        try {
            const response = await fetch(`{{ route('pos.get-product') }}?barcode=${barcode}`);
            const data = await response.json();

            if (!response.ok) {
                showErrorModal(data.message || 'Failed to retrieve product data');
                return;
            }

            if (!data.results || data.results.length === 0) {
                showErrorModal('Product not found');
                return;
            }

            const productResult = data.results[0];
            const product = productResult.product_data;

            productDetails[product.id] = product;
            addToCart(product);

        } catch (error) {
            console.error('Error:', error);
            showErrorModal('An error occurred while fetching product data');
        }
    }

    function addProductFromSearch(product) {
        const formattedProduct = {
            id: product.id,
            name: product.name,
            barcode: product.barcode,
            available_units: product.available_units,
            tax: product.tax,
            discount: product.discount,
            default_unit: product.default_unit
        };

        productDetails[product.id] = formattedProduct;
        addToCart(formattedProduct);
    }

    function addToCart(product) {
        let defaultUnit;

        if (product.default_unit) {
            defaultUnit = product.default_unit;
        } else {
            defaultUnit = product.available_units.find(unit => unit.is_default == true);
        }

        if (!defaultUnit) {
            showErrorModal('Produk tidak memiliki unit default!');
            return;
        }

        // Note: Allowing products with zero or negative stock
        const selectedUnitId = defaultUnit.unit_id || defaultUnit.product_unit_id;
        const existingItemIndex = cart.findIndex(item =>
            item.product_id === product.id &&
            item.unit_id === selectedUnitId
        );

        if (existingItemIndex !== -1) {
            cart[existingItemIndex].quantity += 1;
            calculateItemSubtotal(cart[existingItemIndex]);
        } else {
            const newItem = {
                product_id: product.id,
                product_name: product.name,
                unit_id: selectedUnitId,
                unit_name: defaultUnit.unit_name,
                available_units: product.available_units,
                quantity: 1,
                unit_price: parseFloat(defaultUnit.selling_price),
                tax_rate: product.tax ? parseFloat(product.tax.rate) : 0,
                discount: product.discount ? calculateDiscount(product) : 0
            };

            calculateItemSubtotal(newItem);
            cart.push(newItem);
        }

        updateCartTable();
        calculateTotals();
    }

    function updateQuantity(index, newQuantity) {
        const item = cart[index];
        const product = productDetails[item.product_id];

        item.quantity = parseFloat(newQuantity);
        item.unit_price = getUnitPrice(product, item.quantity, item.unit_id);

        calculateItemSubtotal(item);
        updateCartTable();
        calculateTotals();
    }

    function updateUnit(index, newUnitId) {
        const item = cart[index];
        const product = productDetails[item.product_id];
        const unit = product.available_units.find(u => u.unit_id === parseInt(newUnitId));

        item.unit_id = parseInt(newUnitId);
        item.unit_name = unit.unit_name;
        item.unit_price = getUnitPrice(product, item.quantity, item.unit_id);

        calculateItemSubtotal(item);
        updateCartTable();
        calculateTotals();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        updateCartTable();
        calculateTotals();
    }

    // ===============================================================
    // Cart UI Management
    // ===============================================================
    function updateCartTable() {
        const tbody = document.querySelector('#cart-table tbody');
        tbody.innerHTML = '';

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            const unit = item.available_units.find(u => u.unit_id === item.unit_id);

            // Check if stock is negative or zero
            const stockWarning = parseFloat(unit.stock) <= 0 ?
                `<span class="text-danger">(Stok: ${unit.stock})</span>` : '';

            const conversionInfo = parseFloat(unit.conversion_factor) > 1 ?
                `(1 ${unit.unit_name} = ${unit.conversion_factor} ${unit.base_unit_name || unit.unit_name})` : '';

            tr.innerHTML = `
                <td>
                    ${item.product_name}
                    <br>
                    <small class="text-muted">${conversionInfo} ${stockWarning}</small>
                </td>
                <td>
                    <select class="form-select form-select-sm"
                            onchange="updateUnit(${index}, this.value)">
                        ${item.available_units.map(unit => `
                        <option value="${unit.unit_id}"
                                ${unit.unit_id === item.unit_id ? 'selected' : ''}>
                        ${unit.unit_name}
                        </option>
                        `).join('')}
                    </select>
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm quantity-input"
                           value="${parseFloat(item.quantity)}"
                           step="1"
                           min="1"
                           onchange="updateQuantity(${index}, this.value)">
                </td>
                <td>${formatCurrency(item.unit_price)}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                        Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Add keyboard navigation for quantity inputs
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach((input) => {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    const currentIndex = Array.from(quantityInputs).indexOf(input);
                    const nextIndex = e.key === 'ArrowDown' ?
                        (currentIndex + 1) % quantityInputs.length :
                        (currentIndex - 1 + quantityInputs.length) % quantityInputs.length;
                    quantityInputs[nextIndex].focus();
                    e.preventDefault();
                }
            });
        });
    }

    function updateEmptyCartMessage() {
        const cartTable = document.querySelector('#cart-table tbody');
        const emptyCartMessage = document.getElementById('empty-cart-message');

        if (cartTable && emptyCartMessage) {
            if (cartTable.children.length === 0) {
                emptyCartMessage.style.display = 'block';
            } else {
                emptyCartMessage.style.display = 'none';
            }
        }
    }

    function enhanceTableRows() {
        const tableRows = document.querySelectorAll('#cart-table tbody tr');
        tableRows.forEach((row, index) => {
            // Add zebra striping
            if (index % 2 === 0) {
                row.classList.add('table-light');
            }

            // Add hover effect
            row.addEventListener('mouseenter', function() {
                this.classList.add('table-active');
            });

            row.addEventListener('mouseleave', function() {
                if (index % 2 === 0) {
                    this.classList.remove('table-active');
                    this.classList.add('table-light');
                } else {
                    this.classList.remove('table-active');
                }
            });
        });
    }

    // ===============================================================
    // Transaction Management
    // ===============================================================
    async function saveTransaction() {
        if (cart.length === 0) {
            showErrorModal('Keranjang masih kosong!');
            return;
        }

        const paymentType = document.getElementById('pos_payment_type').value;

        // Prepare the transaction data
        const transactionData = {
            invoice_number: document.getElementById('pos_invoice_number').value,
            store_id: document.getElementById('pos_store_id').value,
            customer_id: document.getElementById('pos_customer_id').value,
            items: cart,
            payment_type: paymentType,
            total_amount: parseFloat(document.getElementById('pos_subtotal').value.replace(/[^0-9.-]+/g, "")),
            tax_amount: parseFloat(document.getElementById('pos_tax_amount').value.replace(/[^0-9.-]+/g, "")),
            discount_amount: parseFloat(document.getElementById('pos_discount_amount').value.replace(/[^0-9.-]+/g, "")),
            final_amount: parseFloat(document.getElementById('pos_final_amount').value.replace(/[^0-9.-]+/g, "")),
            pending_transaction_id: pendingTransactionId,
            status: 'success',
            cash_amount: parseFloat(document.getElementById('pos_cash_amount').value || 0)
        };

        try {
            const response = await fetch('{{ route('pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(transactionData)
            });

            const result = await response.json();

            if (result.success) {
                showSuccessModal('Transaksi berhasil disimpan!', () => {
                    window.open(
                        `{{ url('pos/invoice') }}/${result.transaction_id}`,
                        'InvoiceWindow',
                        'width=400,height=600');
                    window.location.reload();
                });
            } else {
                showErrorModal(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat menyimpan transaksi');
        }
    }

    async function saveAsPending() {
        if (cart.length === 0) {
            showErrorModal('Keranjang masih kosong!');
            return;
        }

        try {
            const data = {
                invoice_number: document.getElementById('pos_invoice_number').value,
                store_id: document.getElementById('pos_store_id').value,
                customer_id: document.getElementById('pos_customer_id').value,
                items: cart,
                payment_type: document.getElementById('pos_payment_type').value,
                total_amount: parseFloat(document.getElementById('pos_subtotal').value.replace(/[^0-9.-]+/g, "")),
                tax_amount: parseFloat(document.getElementById('pos_tax_amount').value.replace(/[^0-9.-]+/g, "")),
                discount_amount: parseFloat(document.getElementById('pos_discount_amount').value.replace(/[^0-9.-]+/g, "")),
                final_amount: parseFloat(document.getElementById('pos_final_amount').value.replace(/[^0-9.-]+/g, "")),
                pending_transaction_id: pendingTransactionId,
                status: 'pending',
                cash_amount: parseFloat(document.getElementById('pos_cash_amount').value || 0)
            };

            const response = await fetch('{{ route('pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showSuccessModal('Transaksi Pending berhasil disimpan', () => {
                    window.location.href = '{{ route('pos.index') }}';
                });
            } else {
                showErrorModal(result.message || 'Terjadi kesalahan saat menyimpan transaksi');
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat menyimpan transaksi');
        }
    }

    async function showPendingTransactions() {
        try {
            const response = await fetch('{{ route('transactions.index') }}?' + new URLSearchParams({
                status: 'pending'
            }), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            const tbody = document.querySelector('#pending-transactions-table tbody');
            tbody.innerHTML = '';

            if (result.data && result.data.length > 0) {
                result.data.forEach(transaction => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${transaction.invoice_number}</td>
                        <td>${transaction.invoice_date_formatted}</td>
                        <td>${transaction.customer_name}</td>
                        <td>${transaction.final_amount_formatted}</td>
                        <td>
                            <a href="{{ url('transactions') }}/${transaction.id}/continue"
                               class="btn btn-primary btn-sm">
                                Lanjutkan
                            </a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">
                            Tidak ada transaksi pending
                        </td>
                    </tr>
                `;
            }

            const modal = new bootstrap.Modal(document.getElementById('pendingTransactionsModal'));
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat mengambil data transaksi pending');
        }
    }

    async function clearCart() {
        if (cart.length === 0) {
            showErrorModal('Keranjang sudah kosong!');
            return;
        }

        const confirmClear = confirm('Apakah Anda yakin ingin membersihkan keranjang?');
        if (!confirmClear) return;

        try {
            // If there's a pending transaction, clear it from the server
            if (pendingTransactionId) {
                const response = await fetch(`{{ route('pos.clear-pending') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        transaction_id: pendingTransactionId
                    })
                });

                const result = await response.json();
                if (!result.success) {
                    showErrorModal('Gagal membersihkan data pending!');
                    return;
                }
            }

            // Clear local cart data
            cart = [];
            productDetails = {};
            pendingTransactionId = null;

            // Reset form fields
            document.getElementById('pos_invoice_number').value = '{{ $invoiceNumber }}';
            document.getElementById('pos_customer_id').value = '1'; // Reset to default customer
            document.getElementById('pos_payment_type').value = 'cash';

            // Update UI
            updateCartTable();
            calculateTotals();

            showSuccessModal('Keranjang berhasil dibersihkan!');
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat membersihkan keranjang');
        }
    }

    // ===============================================================
    // Calculation Functions
    // ===============================================================
    function calculateTotals() {
        const subtotal = cart.reduce((sum, item) => {
            return sum + (parseFloat(item.subtotal) || 0);
        }, 0);

        const taxAmount = cart.reduce((sum, item) => {
            const itemTax = (parseFloat(item.subtotal) || 0) * (parseFloat(item.tax_rate) || 0) / 100;
            return sum + itemTax;
        }, 0);

        const discountAmount = cart.reduce((sum, item) => {
            return sum + ((parseFloat(item.discount) || 0) * (parseFloat(item.quantity) || 0));
        }, 0);

        const finalAmount = subtotal + taxAmount;

        // Update count of items
        document.getElementById('item-count').textContent = cart.length;

        // Update monetary values
        document.getElementById('pos_subtotal').value = formatCurrency(subtotal);
        document.getElementById('pos_tax_amount').value = formatCurrency(taxAmount);
        document.getElementById('pos_discount_amount').value = formatCurrency(discountAmount);
        document.getElementById('pos_final_amount').value = formatCurrency(finalAmount);
    }

    function calculateItemSubtotal(item) {
        const quantity = parseFloat(item.quantity) || 0;
        const unitPrice = parseFloat(item.unit_price) || 0;
        const discount = parseFloat(item.discount) || 0;

        // Calculate base subtotal (quantity * unit price)
        const baseSubtotal = quantity * unitPrice;

        // Calculate final subtotal after discount
        item.subtotal = baseSubtotal - (discount * quantity);

        return item.subtotal;
    }

    function calculateDiscount(product) {
        if (!product.discount) return 0;

        const defaultUnit = product.available_units.find(unit =>
            unit.unit_id === product.default_unit_id
        );
        const basePrice = defaultUnit ? parseFloat(defaultUnit.selling_price) : 0;

        if (product.discount.type === 'percentage') {
            return basePrice * parseFloat(product.discount.value) / 100;
        }
        return parseFloat(product.discount.value);
    }

    function calculateChange() {
        // Clear change field if no cash amount entered
        if (!this.value) {
            document.getElementById('pos_change').value = '';
            return;
        }

        // Get cash amount directly from input (number type)
        const cashAmount = parseFloat(this.value) || 0;

        // Get final amount and convert from Rupiah to number
        const finalAmountStr = document.getElementById('pos_final_amount').value;
        const cleanAmount = finalAmountStr
            .replace(/[Rp\s]/g, '') // Remove 'Rp' and spaces
            .replace(/\./g, '')      // Remove dots (thousand separators)
            .replace(/,/g, '.');     // Replace comma with dot for decimal

        const finalAmount = parseFloat(cleanAmount) || 0;

        // Calculate change - only show positive value
        const change = cashAmount - finalAmount;
        const displayChange = Math.max(0, Math.round(change * 100) / 100);

        // Format and display the change
        document.getElementById('pos_change').value = formatCurrency(displayChange);
    }

    function getUnitPrice(product, quantity, unitId) {
        // Find the product unit
        const productUnit = product.available_units.find(unit => unit.unit_id === unitId);
        if (!productUnit) return 0;

        // Check if there are tiered prices
        if (productUnit.prices && productUnit.prices.length > 0) {
            // Find the applicable price tier based on quantity
            const applicableTier = productUnit.prices.find(price =>
                quantity >= parseFloat(price.min_quantity)
            );

            if (applicableTier) {
                return parseFloat(applicableTier.price);
            }
        }

        // If no tiered price is found, return the default selling price
        return parseFloat(productUnit.selling_price);
    }

    // ===============================================================
    // Helper Functions
    // ===============================================================
    function formatCurrency(amount) {
        // Ensure amount is rounded to 2 decimal places
        const roundedAmount = Math.round(amount * 100) / 100;

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(roundedAmount);
    }

    function showSuccessModal(message, callback) {
        const modalHtml = `
            <div class="modal fade" id="successModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p>${message}</p>
                            <button type="button" class="btn btn-primary" id="successOkButton">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('successModal'));

        const handleSuccess = () => {
            modal.hide();
            if (callback) callback();
        };

        document.getElementById('successOkButton').addEventListener('click', handleSuccess);

        document.getElementById('successModal').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleSuccess();
            }
        });

        document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

        modal.show();
        document.getElementById('successOkButton').focus();
    }

    function showErrorModal(message) {
        const modalHtml = `
            <div class="modal fade" id="errorModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                            <p>${message}</p>
                            <button type="button" class="btn btn-primary" id="errorOkButton">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));

        const handleError = () => {
            modal.hide();
        };

        document.getElementById('errorOkButton').addEventListener('click', handleError);

        document.getElementById('errorModal').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleError();
            }
        });

        document.getElementById('errorModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

        modal.show();
        document.getElementById('errorOkButton').focus();
    }
</script>

// Tambahkan di resources/views/pos/partials/scripts.blade.php
<script>
    // Function to apply Windows 7 styling to dynamically created elements
    function applyWin7Styling() {
        // Fix Select2 elements
        $('.select2-container--default .select2-selection--single').css({
            'border': '1px solid #7b9ebd',
            'height': '28px',
            'border-radius': '2px'
        });

        $('.select2-container--default .select2-selection--single .select2-selection__rendered').css({
            'line-height': '26px',
            'font-family': 'Tahoma, Arial, sans-serif',
            'font-size': '12px',
            'padding-left': '8px'
        });

        $('.select2-container--default .select2-selection--single .select2-selection__arrow').css({
            'height': '26px',
            'background': 'linear-gradient(to bottom, #f3f6fb 0%, #e1e6f6 100%)',
            'border-left': '1px solid #7b9ebd'
        });

        // Fix any dynamically created buttons
        $('.btn').css({
            'font-size': '12px',
            'padding': '3px 10px',
            'height': '28px',
            'display': 'inline-flex',
            'align-items': 'center',
            'justify-content': 'center',
            'border': '1px solid #7b9ebd'
        });

        // Ensure form elements are styled
        $('.form-control, .form-select').css({
            'height': '28px',
            'padding': '3px 5px',
            'font-size': '12px'
        });
    }

    // Run initially and after DOM changes
    $(document).ready(function() {
        applyWin7Styling();

        // Watch for Select2 events
        $(document).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-dropdown').css({
                    'border': '1px solid #7b9ebd',
                    'border-radius': '2px',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.2)',
                    'font-family': 'Tahoma, Arial, sans-serif',
                    'font-size': '12px'
                });

                $('.select2-search__field').css({
                    'border': '1px solid #7b9ebd',
                    'border-radius': '2px',
                    'height': '24px',
                    'font-family': 'Tahoma, Arial, sans-serif',
                    'font-size': '12px'
                });

                $('.select2-results__option').css({
                    'font-family': 'Tahoma, Arial, sans-serif',
                    'font-size': '12px',
                    'padding': '4px 8px'
                });
            }, 0);
        });

        // Force reapply styles when cart is updated
        const observer = new MutationObserver(function(mutations) {
            applyWin7Styling();
        });

        // Observe cart table for changes
        const cartTable = document.querySelector('#cart-table tbody');
        if (cartTable) {
            observer.observe(cartTable, { childList: true, subtree: true });
        }
    });
</script>

<script>
    // Function to apply specific class names to buttons
    function applyButtonClasses() {
        // Apply specific classes based on button content/icons
        $('.btn').each(function() {
            const btnText = $(this).text().trim().toLowerCase();

            // Dashboard button
            if ($(this).find('.bx-home-alt').length || btnText.includes('dashboard')) {
                $(this).addClass('btn-dashboard');
            }

            // Tertunda button
            if ($(this).find('.bx-time').length || btnText.includes('tertunda')) {
                $(this).addClass('btn-tertunda');
            }

            // Hapus button
            if ($(this).find('.bx-trash').length || btnText.includes('hapus')) {
                $(this).addClass('btn-hapus');
            }

            // Bayar button
            if ($(this).find('.bx-check-circle').length || btnText.includes('bayar')) {
                $(this).addClass('btn-bayar');
            }

            // Pending button
            if ($(this).find('.bx-time-five').length || btnText.includes('pending')) {
                $(this).addClass('btn-pending');
            }
        });
    }

    $(document).ready(function() {
        applyButtonClasses();

        // Reapply when DOM changes
        const observer = new MutationObserver(function() {
            applyButtonClasses();
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>

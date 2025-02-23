<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
{{--<script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>--}}

<!-- Main JS -->
<script src="{{ asset('sneat/assets/js/main.js') }}"></script>

<!-- Page JS -->
{{--<script src="{{ asset('sneat/assets/js/dashboards-analytics.js') }}"></script>--}}



<script>
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

    // Initialize everything when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize transaction summary functionality
        updateTransactionSummary();
        setInterval(updateTransactionSummary, 300000); // 5 minutes
        adjustTickerAnimation();
        window.addEventListener('resize', adjustTickerAnimation);

        // Initialize Select2 and other POS functionality
        initializePOS();
    });

    function initializePOS() {
        // Initialize Select2
        $('#pos_search_product').select2({
            // Select2 configuration...
        });

        // Add event listeners
        setupEventListeners();
    }

    function setupEventListeners() {
        // Add all your event listeners here
        // Barcode scanner handling
        document.getElementById('pos_barcode').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                getProduct(this.value);
                this.value = '';
            }
        });

        document.getElementById('pos_search_product').addEventListener('input', async function () {
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
        });

        document.getElementById('pos_search_product').addEventListener('keydown', function (e) {
            const productList = document.getElementById('pos_product_list');
            if (productList.style.display === 'block') {
                handleProductListNavigation(e, productList);
            }
        });

        // Transaction handlers
        // Perbaikan pada event listener btn-save
        // Perbaikan pada event listener btn-save
        document.getElementById('btn-save')?.addEventListener('click', async function() {
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

                cash_amount: paymentType === 'cash' ? parseFloat(document.getElementById('pos_cash_amount').value) : null
            };

            // Validasi pembayaran cash
            if (paymentType === 'cash') {
                const cashAmount = parseFloat(document.getElementById('pos_cash_amount').value) || 0;
                const finalAmount = transactionData.final_amount;

                if (!cashAmount) {
                    showErrorModal('Masukkan jumlah uang tunai!');
                    return;
                }

                if (cashAmount < finalAmount) {
                    showErrorModal('Uang tunai kurang dari total pembayaran!');
                    return;
                }

                transactionData.cash_amount = cashAmount;
            }

            try {
                const response = await fetch('{{ route("pos.store") }}', {
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
                        window.open(`{{ url('pos/invoice') }}/${result.transaction_id}`, '_blank');
                        window.location.reload();
                    });
                } else {
                    showErrorModal(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Terjadi kesalahan saat menyimpan transaksi');
            }
        });

        document.getElementById('btn-pending').addEventListener('click', async function() {
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
                    // Only include cash_amount if payment type is cash and there's a value
                    ...(document.getElementById('pos_payment_type').value === 'cash' &&
                    document.getElementById('pos_cash_amount').value ?
                        {cash_amount: parseFloat(document.getElementById('pos_cash_amount').value)} :
                        {})
                };

                const response = await fetch('{{ route("pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showSuccessModal('Transaksi berhasil disimpan sebagai draft!', () => {
                        window.location.href = '{{ route("pos.index") }}';
                    });
                } else {
                    showErrorModal(result.message || 'Terjadi kesalahan saat menyimpan transaksi');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Terjadi kesalahan saat menyimpan transaksi');
            }
        });

        // Add this code to your existing JavaScript file

        // Show pending transactions modal
        document.getElementById('btn-show-pending').addEventListener('click', async function() {
            try {
                const response = await fetch('{{ route("transactions.index") }}?' + new URLSearchParams({
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
        });

        // Add keyboard navigation for the modal
        document.getElementById('pendingTransactionsModal').addEventListener('shown.bs.modal', function () {
            const modal = this;
            const links = modal.querySelectorAll('a.btn-primary');
            let currentIndex = -1;

            modal.addEventListener('keydown', function(e) {
                switch(e.key) {
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
        });

        document.getElementById('btn-clear-cart').addEventListener('click', async function() {
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
        });



        //================================================================================================


        // Add this after the payment type event listener
        document.getElementById('pos_cash_amount').addEventListener('input', function() {
            // Clear change field if no cash amount entered
            if (!this.value) {
                document.getElementById('pos_change').value = '';
                return;
            }

            // Get cash amount directly from input (it's already a number input)
            const cashAmount = parseFloat(this.value) || 0;
            console.log('Cash Amount:', cashAmount);

            // Get final amount and convert Indonesian format to standard number
            const finalAmountStr = document.getElementById('pos_final_amount').value;
            console.log('Final Amount String:', finalAmountStr);

            // Remove currency symbol, dots, and replace comma with dot
            const cleanAmount = finalAmountStr
                .replace(/[Rp\s]/g, '')  // Remove 'Rp' and spaces
                .replace(/\./g, '')      // Remove dots (thousand separators)
                .replace(/,/g, '.');     // Replace comma with dot for decimal

            const finalAmount = parseFloat(cleanAmount) || 0;
            console.log('Parsed Final Amount:', finalAmount);

            // Calculate change
            const change = cashAmount - finalAmount;
            console.log('Initial Change Calculation:', change);

            // Only show positive change, rounded to avoid floating point issues
            const displayChange = Math.max(0, Math.round(change * 100) / 100);
            console.log('Display Change (after rounding):', displayChange);

            // Format with proper currency handling
            document.getElementById('pos_change').value = formatCurrency(displayChange);
        });
    }


    // Initialize Select2
    $(document).ready(function() {
        $('#pos_search_product').select2({
            placeholder: 'Cari nama produk..',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("pos.search-product") }}',
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

            return $(`
        <div class="product-info">
            <span class="product-name">${product.product.name}</span>
            <span class="product-details">
                ${product.product.barcode || 'No Barcode'} -
                Stock: ${defaultUnit.stock} -
                ${formatCurrency(defaultUnit.selling_price)}
            </span>
        </div>
    `);
        }

        function formatProductSelection(product) {
            return product.text || 'Cari produk';
        }
    });

    // Keyboard navigation functions
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
            nextActive.scrollIntoView({block: 'nearest'});
        }
    }

    function handleUnitSelectionNavigation(e, modal) {
        const select = modal.querySelector('#unit_selection');

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (select.selectedIndex < select.options.length - 1) {
                    select.selectedIndex++;
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (select.selectedIndex > 0) {
                    select.selectedIndex--;
                }
                break;

            case 'Enter':
                e.preventDefault();
                modal.querySelector('#confirmUnitSelection').click();
                break;

            case 'Escape':
                e.preventDefault();
                modal.querySelector('.btn-close').click();
                break;
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

    // Cart handling functions
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

        if (defaultUnit.stock <= 0) {
            showErrorModal('Stok produk tidak tersedia!');
            return;
        }

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

    // UI Helper functions
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

        document.getElementById('successModal').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleSuccess();
            }
        });

        document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
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

        document.getElementById('errorModal').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleError();
            }
        });

        document.getElementById('errorModal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });

        modal.show();
        document.getElementById('errorOkButton').focus();
    }

    // Helper functions
    function updateCartTable() {
        const tbody = document.querySelector('#cart-table tbody');
        tbody.innerHTML = '';

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            const unit = item.available_units.find(u => u.unit_id === item.unit_id);

            const conversionInfo = parseFloat(unit.conversion_factor) > 1
                ? `(1 ${unit.unit_name} = ${unit.conversion_factor} ${unit.unit_name})`
                : '';

            tr.innerHTML = `
<td>
    ${item.product_name}
    <br>
    <small class="text-muted">${conversionInfo}</small>
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
<td>${formatCurrency(item.discount)}</td>
<td>${formatCurrency(item.subtotal)}</td>
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
        quantityInputs.forEach((input, index) => {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    const currentIndex = Array.from(quantityInputs).indexOf(input);
                    const nextIndex = e.key === 'ArrowDown'
                        ? (currentIndex + 1) % quantityInputs.length
                        : (currentIndex - 1 + quantityInputs.length) % quantityInputs.length;
                    quantityInputs[nextIndex].focus();
                    e.preventDefault();
                }
            });
        });
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

        document.getElementById('pos_subtotal').value = formatCurrency(subtotal);
        document.getElementById('pos_tax_amount').value = formatCurrency(taxAmount);
        document.getElementById('pos_discount_amount').value = formatCurrency(discountAmount);
        document.getElementById('pos_final_amount').value = formatCurrency(finalAmount);
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

    // Also add the getUnitPrice function that was referenced but not defined
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

    // Add CSS for keyboard navigation
    const style = document.createElement('style');
    style.textContent = `
.product-item {
    padding: 8px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}
.product-item:hover {
    background-color: #f8f9fa;
}
.product-item.active {
    background-color: #e9ecef;
}
#pos_product_list {
    position: absolute;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    z-index: 1000;
    display: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
`;
    document.head.appendChild(style);



    //================================================================================================

    async function updateTransactionSummary() {
        try {
            const response = await fetch('/pos/today-summary', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            // Update all values
            document.getElementById('today_total').textContent = formatCurrency(data.total_amount);
            document.getElementById('today_count').textContent = data.transaction_count;
            document.getElementById('cash_total').textContent = formatCurrency(data.cash_amount);
            document.getElementById('transfer_total').textContent = formatCurrency(data.transfer_amount);
            document.getElementById('average_transaction').textContent = formatCurrency(data.average_transaction);

            document.getElementById('today_date').textContent = new Date().toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });

            document.getElementById('last_update').textContent = data.last_updated;

            // Optional: Add tooltip with more details
            const tickerContent = document.querySelector('.ticker-content');
            tickerContent.title = `
            Transaksi Tunai: ${data.cash_transactions}
            Transaksi Transfer: ${data.transfer_transactions}
            Total Pajak: ${formatCurrency(data.total_tax)}
            Total Diskon: ${formatCurrency(data.total_discount)}
            Transaksi Terakhir: ${data.latest_transaction}
            ${data.peak_hour ? `Jam Tersibuk: ${data.peak_hour.hour}:00 (${data.peak_hour.count} transaksi)` : ''}
        `;
        } catch (error) {
            console.error('Error fetching transaction summary:', error);
        }
    }

    function adjustTickerAnimation() {
        const tickerContent = document.querySelector('.ticker-content');
        const contentWidth = tickerContent.scrollWidth;
        const containerWidth = document.querySelector('.transaction-ticker').offsetWidth;

        if (contentWidth > containerWidth) {
            const duration = Math.max(20, contentWidth / containerWidth * 10);
            tickerContent.style.animationDuration = `${duration}s`;
        } else {
            tickerContent.style.animation = 'none';
        }
    }
</script>

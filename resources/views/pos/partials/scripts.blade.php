<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('sneat/assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('sneat/assets/js/dashboards-analytics.js') }}"></script>

<script>
    let cart = [];
    let productDetails = {};
    let pendingTransactionId = null;

    // Check if there's cart data from a pending transaction
    const cartData = @json(session('cart_data'));
    if (cartData) {
        pendingTransactionId = cartData.pending_transaction_id;
        document.getElementById('pos_invoice_number').value = cartData.invoice_number;
        document.getElementById('pos_customer_id').value = cartData.customer_id;
        document.getElementById('pos_payment_type').value = cartData.payment_type;

        if (cartData.payment_type === 'transfer') {
            document.getElementById('pos_reference_number').value = cartData.reference_number;
            document.getElementById('pos_reference_number_container').style.display = 'block';
        }

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

    // Product search and handling
    async function getProduct(barcode) {
        try {
            const response = await fetch(`{{ route('pos.get-product') }}?barcode=${barcode}`);
            const data = await response.json();

            if (response.ok) {
                productDetails[data.id] = data;
                addToCart(data);
            } else {
                showErrorModal(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat mengambil data produk');
        }
    }

    // Cart handling functions
    function addToCart(product) {
        // Ubah pencarian default unit menggunakan is_default
        const defaultUnit = product.available_units.find(unit => unit.is_default === true);

        if (!defaultUnit) {
            showErrorModal('Produk tidak memiliki unit default!');
            return;
        }

        // Tambah pengecekan stock
        if (defaultUnit.stock <= 0) {
            showErrorModal('Stok produk tidak tersedia!');
            return;
        }

        const newItem = {
            product_id: product.id,
            product_name: product.name,
            unit_id: defaultUnit.unit_id, // Gunakan unit_id dari defaultUnit
            unit_name: defaultUnit.unit_name,
            available_units: product.available_units,
            quantity: 1,
            unit_price: parseFloat(defaultUnit.selling_price),
            tax_rate: product.tax ? parseFloat(product.tax.rate) : 0,
            discount: product.discount ? calculateDiscount(product, defaultUnit.selling_price) : 0
        };

        // Sisanya sama seperti sebelumnya
        showUnitSelectionModal(newItem, (selectedUnit) => {
            const selectedUnitId = parseInt(selectedUnit);
            const existingItemWithUnit = cart.find(item =>
                item.product_id === product.id &&
                item.unit_id === selectedUnitId
            );

            if (existingItemWithUnit) {
                existingItemWithUnit.quantity = parseFloat(existingItemWithUnit.quantity) + 1;
                calculateItemSubtotal(existingItemWithUnit);
            } else {
                newItem.unit_id = selectedUnitId;
                const unit = product.available_units.find(u => u.unit_id === selectedUnitId);
                newItem.unit_name = unit.unit_name;
                newItem.unit_price = parseFloat(unit.selling_price);
                calculateItemSubtotal(newItem);
                cart.push(newItem);
            }

            updateCartTable();
            calculateTotals();
        });
    }

    function showUnitSelectionModal(item, callback) {
        const modalHtml = `
<div class="modal fade" id="unitSelectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Unit untuk ${item.product_name}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
            <label for="unit_selection">Unit</label>
            <select class="form-select" id="unit_selection" name="unit_selection" autofocus>
                        ${item.available_units.map(unit => `
                        <option value="${unit.unit_id}" ${unit.unit_id === item.unit_id ? 'selected' : ''}>
                        ${unit.unit_name}
                        </option>
                        `).join('')}
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmUnitSelection">Pilih</button>
            </div>
        </div>
    </div>
</div>
`;

        const existingModal = document.getElementById('unitSelectionModal');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('unitSelectionModal'));

        document.getElementById('unitSelectionModal').addEventListener('keydown', function (e) {
            handleUnitSelectionNavigation(e, this);
        });

        modal.show();

        document.getElementById('confirmUnitSelection').addEventListener('click', function () {
            const selectedUnit = document.getElementById('unit_selection').value;
            modal.hide();
            callback(selectedUnit);
        });

        document.getElementById('unitSelectionModal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
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
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(amount);
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
        // Format the product data
        const formattedProduct = {
            id: product.id,
            name: product.name,
            barcode: product.barcode,
            available_units: product.available_units,
            tax: product.tax,
            discount: product.discount
        };

        // Store product details for later use
        productDetails[product.id] = formattedProduct;

        // Create cart item with all necessary properties
        const newItem = {
            product_id: product.id,
            product_name: product.name,
            available_units: product.available_units,
            tax_rate: product.tax ? parseFloat(product.tax.rate) : 0,
            discount: product.discount ? calculateDiscount(formattedProduct) : 0
        };

        // Show unit selection modal immediately after creating the item
        showUnitSelectionModal(newItem, (selectedUnitId) => {
            const selectedUnit = product.available_units.find(u => u.unit_id === parseInt(selectedUnitId));

            if (!selectedUnit) {
                showErrorModal('Unit tidak ditemukan!');
                return;
            }

            // Check stock
            if (selectedUnit.stock <= 0) {
                showErrorModal('Stok produk tidak tersedia!');
                return;
            }

            // Update item with selected unit details
            newItem.unit_id = parseInt(selectedUnitId);
            newItem.unit_name = selectedUnit.unit_name;
            newItem.unit_price = parseFloat(selectedUnit.selling_price);
            newItem.quantity = 1;

            // Check for existing item with same product and unit
            const existingItemIndex = cart.findIndex(item =>
                item.product_id === product.id &&
                item.unit_id === parseInt(selectedUnitId)
            );

            if (existingItemIndex !== -1) {
                cart[existingItemIndex].quantity += 1;
                calculateItemSubtotal(cart[existingItemIndex]);
            } else {
                calculateItemSubtotal(newItem);
                cart.push(newItem);
            }

            updateCartTable();
            calculateTotals();
        });
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

    // Barcode scanner handling
    document.getElementById('pos_barcode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            getProduct(this.value);
            this.value = '';
        }
    });

    // Payment type handling
    document.getElementById('pos_payment_type').addEventListener('change', function() {
        const refContainer = document.getElementById('pos_reference_number_container');
        refContainer.style.display = this.value === 'transfer' ? 'block' : 'none';
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
    document.getElementById('btn-save').addEventListener('click', async function () {
        if (cart.length === 0) {
            showErrorModal('Keranjang masih kosong!');
            return;
        }

        const paymentType = document.getElementById('pos_payment_type').value;
        const referenceNumber = document.getElementById('pos_reference_number').value;

        if (paymentType === 'transfer' && !referenceNumber) {
            showErrorModal('Nomor referensi harus diisi untuk pembayaran transfer!');
            return;
        }

        const data = {
            invoice_number: document.getElementById('pos_invoice_number').value,
            store_id: document.getElementById('pos_store_id').value,
            customer_id: document.getElementById('pos_customer_id').value,
            items: cart,
            payment_type: paymentType,
            reference_number: referenceNumber,
            total_amount: parseFloat(document.getElementById('pos_subtotal').value.replace(/[^0-9.-]+/g, "")),
            tax_amount: parseFloat(document.getElementById('pos_tax_amount').value.replace(/[^0-9.-]+/g, "")),
            discount_amount: parseFloat(document.getElementById('pos_discount_amount').value.replace(/[^0-9.-]+/g, "")),
            final_amount: parseFloat(document.getElementById('pos_final_amount').value.replace(/[^0-9.-]+/g, "")),
            pending_transaction_id: pendingTransactionId,
            status: 'success'
        };

        try {
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
                reference_number: document.getElementById('pos_reference_number').value,
                total_amount: parseFloat(document.getElementById('pos_subtotal').value.replace(/[^0-9.-]+/g, "")),
                tax_amount: parseFloat(document.getElementById('pos_tax_amount').value.replace(/[^0-9.-]+/g, "")),
                discount_amount: parseFloat(document.getElementById('pos_discount_amount').value.replace(/[^0-9.-]+/g, "")),
                final_amount: parseFloat(document.getElementById('pos_final_amount').value.replace(/[^0-9.-]+/g, "")),
                pending_transaction_id: pendingTransactionId,
                status: 'pending'
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
                showSuccessModal('Transaksi berhasil disimpan sebagai draft!', () => {
                    window.location.href = '{{ route('pos.index') }}';
                });
            } else {
                showErrorModal(result.message);
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
            document.getElementById('pos_reference_number').value = '';
            document.getElementById('pos_reference_number_container').style.display = 'none';

            // Update UI
            updateCartTable();
            calculateTotals();

            showSuccessModal('Keranjang berhasil dibersihkan!');
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat membersihkan keranjang');
        }
    });



    //======================



    // Add fullscreen functionality
    const fullscreenButton = document.getElementById('btn-fullscreen');
    const fullscreenIcon = fullscreenButton.querySelector('i');
    const sidebar = document.querySelector('.sidebar'); // Adjust selector based on your sidebar class
    const mainContent = document.querySelector('.main-content'); // Adjust selector based on your main content class

    function toggleFullscreenMode(isFullscreen) {
        if (isFullscreen) {
            // Enter fullscreen
            if (sidebar) {
                sidebar.style.display = 'none';
            }
            if (mainContent) {
                mainContent.style.marginLeft = '0';
                mainContent.style.width = '100%';
            }
            fullscreenIcon.classList.remove('fa-expand');
            fullscreenIcon.classList.add('fa-compress');
        } else {
            // Exit fullscreen
            if (sidebar) {
                sidebar.style.display = '';
            }
            if (mainContent) {
                mainContent.style.marginLeft = '';
                mainContent.style.width = '';
            }
            fullscreenIcon.classList.remove('fa-compress');
            fullscreenIcon.classList.add('fa-expand');
        }
    }

    fullscreenButton.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                showErrorModal(`Error attempting to enable fullscreen: ${err.message}`);
            });
            toggleFullscreenMode(true);
        } else {
            document.exitFullscreen().catch(err => {
                showErrorModal(`Error attempting to exit fullscreen: ${err.message}`);
            });
            toggleFullscreenMode(false);
        }
    });

    // Update icon and sidebar when fullscreen changes through other means (like Esc key)
    document.addEventListener('fullscreenchange', function() {
        toggleFullscreenMode(!!document.fullscreenElement);
    });

    style.textContent += `
.sidebar,
.main-content {
    transition: all 0.3s ease;
}
`;
    document.head.appendChild(style);
</script>

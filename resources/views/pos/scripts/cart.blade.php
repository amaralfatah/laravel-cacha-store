<script>
    // ===============================================================
    // Cart Management
    // ===============================================================
    let cart = [];
    let productDetails = {};

    /**
     * Add a product to the cart
     */
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

    /**
     * Update an item's quantity in cart
     */
    function updateQuantity(index, newQuantity) {
        const item = cart[index];
        const product = productDetails[item.product_id];

        item.quantity = parseFloat(newQuantity);
        item.unit_price = getUnitPrice(product, item.quantity, item.unit_id);

        calculateItemSubtotal(item);
        updateCartTable();
        calculateTotals();
    }

    /**
     * Update an item's unit in cart
     */
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

    /**
     * Remove an item from cart
     */
    function removeItem(index) {
        cart.splice(index, 1);
        updateCartTable();
        calculateTotals();
    }

    /**
     * Calculate a single item's subtotal
     */
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

    /**
     * Calculate discount for a product
     */
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

    /**
     * Get the unit price for a product quantity/unit combination
     */
    function getUnitPrice(product, quantity, unitId) {
        // Find the product unit
        const productUnit = product.available_units.find(unit => unit.unit_id === unitId);
        if (!productUnit) return 0;

        // Check for tiered pricing
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

    /**
     * Clear the entire cart
     */
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
</script>

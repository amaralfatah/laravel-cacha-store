<script>
    // ===============================================================
    // UI Update Functions
    // ===============================================================

    /**
     * Update the cart table with current items
     */
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

        updateEmptyCartMessage();
        enhanceTableRows();
    }

    /**
     * Update empty cart message visibility
     */
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

    /**
     * Add styling and hover effects to table rows
     */
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

    /**
     * Calculate and update totals in the UI
     */
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

    /**
     * Calculate change amount based on cash received
     */
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
</script>

<script>
    // ===============================================================
    // Select2 Initialization
    // ===============================================================
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
</script>

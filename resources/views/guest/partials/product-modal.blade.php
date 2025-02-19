<!-- resources/views/guest/partials/product-modal.blade.php -->
<div class="modal fade product-modal" id="productModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="product-image">
                            <div class="product-gallery">
                                <div class="product-gallery__large-image mb-md--30">
                                    <div class="product-gallery__wrapper">
                                        <div class="element-carousel main-slider image-popup" data-slick-options='{
                                                "slidesToShow": 1,
                                                "slidesToScroll": 1,
                                                "infinite": true,
                                                "arrows": false
                                            }'>
                                            <div class="item">
                                                <figure class="product-gallery__image zoom">
                                                    <img src="" alt="Product Image" id="modal-product-image" class="w-100">
                                                    <span class="product-badge sale d-none" id="modal-product-badge">Sale</span>
                                                </figure>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="modal-box product-summary">
                            <h3 class="product-title mb--20" id="modal-product-title"></h3>
                            <p class="product-short-description mb--20" id="modal-product-description"></p>
                            <div class="product-price-wrapper mb--25">
                                <span class="money" id="modal-product-price"></span>
                                <span class="price-separator d-none">-</span>
                                <span class="money old-price d-none" id="modal-product-old-price"></span>
                            </div>
                            <form action="#" class="variation-form mb--20">
                                <div class="product-size-variations d-flex align-items-center mb--15" id="modal-product-variants">
                                    <!-- Variants will be inserted dynamically -->
                                </div>
                            </form>
                            <div class="product-action d-flex flex-sm-row align-items-sm-center flex-column align-items-start mb--30">
                                <div class="quantity-wrapper d-flex align-items-center mr--30 mr-xs--0 mb-xs--30">
                                    <label class="quantity-label" for="modal-pro-qty">Quantity:</label>
                                    <div class="quantity">
                                        <input type="number" class="quantity-input" name="modal-pro-qty" id="modal-pro-qty" value="1" min="1">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-shape-square btn-size-sm" id="modal-add-to-cart">
                                    Add To Cart
                                </button>
                            </div>
                            <div class="product-footer-meta">
                                <p id="modal-product-category"><span>Category:</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('modal-scripts')
    <script>
        $(document).ready(function() {
            // Load product data into quick view modal
            $('.quick-view').on('click', function() {
                var productId = $(this).data('product-id');

                // AJAX call to get product details
                $.ajax({
                    url: '/api/products/' + productId,
                    method: 'GET',
                    success: function(response) {
                        // Populate modal with product data
                        $('#modal-product-title').text(response.name);
                        $('#modal-product-description').text(response.short_description);
                        $('#modal-product-image').attr('src', response.image);

                        // Set price
                        $('#modal-product-price').text('Rp ' + formatNumber(response.price));

                        // Show discount if available
                        if (response.discount_price) {
                            $('#modal-product-badge').removeClass('d-none');
                            $('.price-separator, #modal-product-old-price').removeClass('d-none');
                            $('#modal-product-old-price').text('Rp ' + formatNumber(response.original_price));
                        } else {
                            $('#modal-product-badge').addClass('d-none');
                            $('.price-separator, #modal-product-old-price').addClass('d-none');
                        }

                        // Set category
                        $('#modal-product-category').html('<span>Category:</span> <a href="/shop?category=' +
                            response.category.id + '">' + response.category.name + '</a>');

                        // Populate variants if available
                        if (response.variants && response.variants.length > 0) {
                            var variantsHtml = '<p class="variation-label">Size:</p><div class="product-size-variation variation-wrapper">';

                            response.variants.forEach(function(variant, index) {
                                variantsHtml += '<div class="variation">' +
                                    '<a class="product-size-variation-btn ' + (index === 0 ? 'selected' : '') + '" ' +
                                    'data-unit-id="' + variant.id + '" ' +
                                    'data-price="' + variant.price + '" ' +
                                    'data-bs-toggle="tooltip" data-bs-placement="top" title="' + variant.name + '">' +
                                    '<span class="product-size-variation-label">' + variant.code + '</span>' +
                                    '</a></div>';
                            });

                            variantsHtml += '</div>';
                            $('#modal-product-variants').html(variantsHtml);
                            $('#modal-product-variants').show();
                        } else {
                            $('#modal-product-variants').hide();
                        }

                        // Set max quantity based on stock
                        $('#modal-pro-qty').attr('max', response.stock || 10);

                        // Set product ID for add to cart button
                        $('#modal-add-to-cart').data('product-id', productId);
                        $('#modal-add-to-cart').data('unit-id', response.default_unit_id);
                    },
                    error: function() {
                        console.error('Failed to load product data');
                    }
                });
            });

            // Handle variant selection in modal
            $(document).on('click', '#modal-product-variants .product-size-variation-btn', function(e) {
                e.preventDefault();

                // Update selected class
                $('#modal-product-variants .product-size-variation-btn').removeClass('selected');
                $(this).addClass('selected');

                // Update price display
                var price = $(this).data('price');
                $('#modal-product-price').text('Rp ' + formatNumber(price));

                // Update unit ID for add to cart button
                $('#modal-add-to-cart').data('unit-id', $(this).data('unit-id'));
            });

            // Handle add to cart from modal
            $('#modal-add-to-cart').on('click', function() {
                var productId = $(this).data('product-id');
                var unitId = $(this).data('unit-id');
                var quantity = $('#modal-pro-qty').val();

                // Add to cart functionality
                // This would typically be an AJAX call to your cart controller
                console.log('Adding to cart from modal:', productId, unitId, quantity);

                // Show success message and close modal
                alert('Product added to cart!');
                $('#productModal').modal('hide');
            });

            // Helper function to format numbers with thousands separator
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
        });
    </script>
@endsection

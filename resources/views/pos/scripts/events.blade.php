<script>
    // ===============================================================
    // Event Handlers
    // ===============================================================

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Barcode scanner handling - replace the keypress event with input event
        const barcodeInput = document.getElementById('pos_barcode');

        // Add debounce to prevent multiple rapid-fire requests
        let barcodeTimer;
        const BARCODE_DELAY = 800; // Time in ms to wait after last character before submitting

        barcodeInput.addEventListener('input', function() {
            const barcode = this.value.trim();

            // Clear any pending timer
            clearTimeout(barcodeTimer);

            // Set a new timer for this input
            if (barcode.length >= 4) { // Only process if reasonable length for a barcode
                barcodeTimer = setTimeout(() => {
                    getProduct(barcode);
                    this.value = ''; // Clear the input after processing
                    // Refocus the input for the next scan
                    this.focus();
                }, BARCODE_DELAY);
            }
        });

        // If user presses Enter, process immediately
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(barcodeTimer); // Cancel any pending timer

                const barcode = this.value.trim();
                if (barcode.length > 0) {
                    getProduct(barcode);
                    this.value = '';
                }
            }
        });

        // Clear timer if user clicks elsewhere or tabs out
        barcodeInput.addEventListener('blur', function() {
            clearTimeout(barcodeTimer);
        });

        // Rest of your event bindings remain the same
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
            observer.observe(cartTableBody, {
                childList: true
            });

            const rowObserver = new MutationObserver(enhanceTableRows);
            rowObserver.observe(cartTableBody, {
                childList: true
            });
        }

        // Pending transactions modal keyboard navigation
        document.getElementById('pendingTransactionsModal').addEventListener('shown.bs.modal',
            setupModalKeyboardNavigation);

        // Cash amount input for calculating change
        document.getElementById('pos_cash_amount').addEventListener('input', calculateChange);

        document.getElementById('btn-camera')?.addEventListener('click', function() {
            openCameraModal();
        });
    }

    /**
     * Handle payment type change
     */
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

    /**
     * Handle product search input
     */
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

    /**
     * Handle keyboard navigation in product list
     */
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
            nextActive.scrollIntoView({
                block: 'nearest'
            });
        }
    }

    /**
     * BARU
     */

    /**
     * BARU
     */

    /**
     * BARU
     */

    // ================= STEP 2: Add after all existing functions =================
    // Tambahkan fungsi-fungsi ini di akhir file Anda, sebelum tag penutup

    /**
     * Open camera modal and start camera
     */
    function openCameraModal() {
        // Create modal if it doesn't exist
        if (!document.getElementById('cameraModal')) {
            createCameraModal();
        }

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
        modal.show();

        // Start camera after modal is shown
        document.getElementById('cameraModal').addEventListener('shown.bs.modal', function() {
            startCamera();
        }, {
            once: true
        });
    }

    /**
     * Create camera modal
     */
    function createCameraModal() {
        const modalHtml = `
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid #919b9c; border-radius: 0; box-shadow: 3px 3px 5px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(to bottom, #4f6acc 0%, #2a3c8e 100%); color: white; padding: 6px 10px; border-bottom: 1px solid #919b9c;">
                    <h5 class="modal-title" id="cameraModalLabel">
                        <i class='bx bx-scan me-1'></i> Scan Barcode
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #ece9d8; padding: 10px;">
                    <div class="text-center mb-3">
                        <p>Arahkan kamera ke barcode produk</p>
                    </div>

                    <div class="camera-wrapper">
                        <!-- Loading indicator -->
                        <div id="camera-loading" class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat kamera...</p>
                        </div>

                        <!-- Error message -->
                        <div id="camera-error" class="alert alert-danger text-center" style="display:none;">
                            <i class='bx bx-error-circle me-1'></i>
                            <span id="camera-error-message">Error message here</span>
                        </div>

                        <!-- Camera preview -->
                        <div id="camera-container" style="display:none;">
                            <video id="camera-preview" style="width: 100%; border: 2px solid #919b9c;" autoplay playsinline></video>
                            <canvas id="camera-canvas" style="display: none;"></canvas>
                        </div>
                    </div>

                    <!-- Camera controls -->
                    <div class="d-flex justify-content-between mt-3">
                        <select id="camera-select" class="form-select" style="max-width: 250px; display:none;">
                            <option value="">Pilih Kamera</option>
                        </select>
                        <div>
                            <button id="btn-take-photo" class="btn btn-primary" style="display:none;">
                                <i class='bx bx-camera me-1'></i> Ambil Foto
                            </button>
                        </div>
                    </div>

                    <!-- Detected barcode -->
                    <div id="barcode-result" class="alert alert-success mt-3" style="display:none;">
                        <strong>Barcode terdeteksi:</strong> <span id="barcode-value"></span>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ece9d8; border-top: 1px solid #919b9c; padding: 8px;">
                    <button type="button" id="btn-manual-input" class="btn btn-primary">
                        <i class='bx bx-keyboard me-1'></i> Input Manual
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Add event listeners
        document.getElementById('btn-manual-input').addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
            if (modal) {
                modal.hide();
            }

            // Focus on barcode input
            setTimeout(() => {
                document.getElementById('pos_barcode').focus();
            }, 300);
        });

        // Camera select change event
        document.getElementById('camera-select').addEventListener('change', function() {
            stopCamera();
            startCamera(this.value);
        });

        // Take photo button
        document.getElementById('btn-take-photo').addEventListener('click', captureBarcode);

        // Cleanup when modal is closed
        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function() {
            stopCamera();
        });
    }

    // Global variables
    let activeStream = null;

    /**
     * Start camera stream
     */
    async function startCamera(deviceId) {
        // Show loading, hide others
        document.getElementById('camera-loading').style.display = 'block';
        document.getElementById('camera-container').style.display = 'none';
        document.getElementById('camera-error').style.display = 'none';
        document.getElementById('barcode-result').style.display = 'none';
        document.getElementById('btn-take-photo').style.display = 'none';

        try {
            // Request camera permissions and get devices
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });

            // Stop the stream we just got (we'll start a new one with selected/preferred camera)
            stream.getTracks().forEach(track => track.stop());

            // Get list of cameras
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');

            // Update camera select dropdown
            updateCameraDropdown(videoDevices, deviceId);

            // Determine which camera to use
            let selectedDeviceId = deviceId;
            if (!selectedDeviceId && videoDevices.length > 0) {
                // Try to find back camera first
                const backCamera = videoDevices.find(device =>
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('rear') ||
                    device.label.toLowerCase().includes('belakang')
                );

                selectedDeviceId = backCamera ? backCamera.deviceId : videoDevices[0].deviceId;
            }

            // Set video constraints
            const constraints = {
                video: {
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    },
                    facingMode: "environment"
                }
            };

            // If we have a specific device ID, use it
            if (selectedDeviceId) {
                constraints.video.deviceId = {
                    exact: selectedDeviceId
                };
            }

            // Get stream with selected camera
            activeStream = await navigator.mediaDevices.getUserMedia(constraints);

            // Set video source
            const videoElement = document.getElementById('camera-preview');
            videoElement.srcObject = activeStream;

            // Add play event to hide loading when video starts
            videoElement.addEventListener('loadedmetadata', function() {
                document.getElementById('camera-loading').style.display = 'none';
                document.getElementById('camera-container').style.display = 'block';
                document.getElementById('btn-take-photo').style.display = 'inline-block';
            });

            console.log('Camera started successfully');
        } catch (error) {
            console.error('Error starting camera:', error);
            showCameraError(
                'Gagal mengakses kamera. Pastikan browser Anda mendukung akses kamera dan izin telah diberikan. Error: ' +
                error.message);
        }
    }

    /**
     * Update camera selection dropdown
     */
    function updateCameraDropdown(cameras, selectedId) {
        const cameraSelect = document.getElementById('camera-select');
        if (!cameraSelect) return;

        cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';

        if (cameras && cameras.length > 0) {
            cameras.forEach(camera => {
                const option = document.createElement('option');
                option.value = camera.deviceId;
                option.text = camera.label || `Kamera (${camera.deviceId.substr(0, 5)}...)`;
                option.selected = camera.deviceId === selectedId;
                cameraSelect.appendChild(option);
            });

            cameraSelect.style.display = cameras.length > 1 ? 'block' : 'none';
        } else {
            cameraSelect.style.display = 'none';
        }
    }

    /**
     * Stop camera stream
     */
    function stopCamera() {
        if (activeStream) {
            activeStream.getTracks().forEach(track => track.stop());
            activeStream = null;
        }

        const videoElement = document.getElementById('camera-preview');
        if (videoElement) {
            videoElement.srcObject = null;
        }
    }

    /**
     * Show camera error
     */
    function showCameraError(message) {
        document.getElementById('camera-loading').style.display = 'none';
        document.getElementById('camera-container').style.display = 'none';

        const errorElement = document.getElementById('camera-error');
        const errorMessageElement = document.getElementById('camera-error-message');

        if (errorElement && errorMessageElement) {
            errorMessageElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Capture image from camera and process for barcode
     */
    function captureBarcode() {
        const video = document.getElementById('camera-preview');
        const canvas = document.getElementById('camera-canvas');

        if (!video || !canvas || !activeStream) {
            console.error('Video or canvas not available');
            return;
        }

        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw video frame to canvas
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Get image data as base64
        const imageData = canvas.toDataURL('image/jpeg');

        // Process image for barcode using backend
        processImageForBarcode(imageData);
    }

    /**
     * Process image data to detect barcode
     */
    function processImageForBarcode(imageData) {
        // Show we're processing
        document.getElementById('btn-take-photo').disabled = true;
        document.getElementById('btn-take-photo').innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

        // Simulasi request ke backend
        setTimeout(() => {
            // Reset button
            document.getElementById('btn-take-photo').disabled = false;
            document.getElementById('btn-take-photo').innerHTML =
                '<i class="bx bx-camera me-1"></i> Ambil Foto';

            // Simulate barcode detection - in real implementation, this would come from your backend
            const mockBarcode = Math.floor(Math.random() * 10000000000000).toString().padStart(13, '0');
            processBarcodeResult(mockBarcode);

        }, 1500);
    }

    /**
     * Process detected barcode
     */
    function processBarcodeResult(barcode) {
        console.log('Barcode detected:', barcode);

        // Show result
        document.getElementById('barcode-value').textContent = barcode;
        document.getElementById('barcode-result').style.display = 'block';

        // Submit barcode to POS system after a delay
        setTimeout(() => {
            // Set barcode value in input field
            document.getElementById('pos_barcode').value = barcode;

            // Trigger Enter press to process barcode
            const enterEvent = new KeyboardEvent('keypress', {
                key: 'Enter',
                code: 'Enter',
                keyCode: 13,
                which: 13,
                bubbles: true
            });
            document.getElementById('pos_barcode').dispatchEvent(enterEvent);

            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
            if (modal) {
                modal.hide();
            }
        }, 1000);
    }
</script>

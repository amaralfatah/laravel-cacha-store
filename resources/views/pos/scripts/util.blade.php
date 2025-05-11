<script>
    // ===============================================================
    // Utility Functions
    // ===============================================================

    /**
     * Format a number as Indonesian currency (Rupiah)
     */
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

    /**
     * Show a success modal with optional callback
     */
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

    /**
     * Show an error modal
     */
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

/**
 * Modal Form Validation for Bootstrap 5
 * Handles form validation and error display for inventory modals
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Add Stock Modal Validation
    const addStockForm = document.getElementById('add_stock_form');
    if (addStockForm) {
        addStockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const quantityInput = document.getElementById('quantity');
            const quantityError = document.getElementById('quantityError');
            
            // Reset validation state
            quantityInput.classList.remove('is-invalid', 'is-valid');
            quantityError.style.display = 'none';
            
            // Validate quantity
            const quantity = parseInt(quantityInput.value);
            if (!quantity || quantity < 1) {
                quantityInput.classList.add('is-invalid');
                quantityError.textContent = 'La cantidad debe ser mayor o igual a 1';
                quantityError.style.display = 'block';
                return false;
            }
            
            quantityInput.classList.add('is-valid');
            
            // If validation passes, submit the form
            this.submit();
        });
        
        // Real-time validation
        const quantityInput = document.getElementById('quantity');
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                const quantityError = document.getElementById('quantityError');
                const quantity = parseInt(this.value);
                
                this.classList.remove('is-invalid', 'is-valid');
                quantityError.style.display = 'none';
                
                if (this.value && quantity >= 1) {
                    this.classList.add('is-valid');
                } else if (this.value) {
                    this.classList.add('is-invalid');
                    quantityError.textContent = 'La cantidad debe ser mayor o igual a 1';
                    quantityError.style.display = 'block';
                }
            });
        }
    }
    
    // Remove Stock Modal Validation
    const removeStockForm = document.getElementById('remove_stock_form');
    if (removeStockForm) {
        removeStockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const quantityInput = document.getElementById('quantity_remove');
            
            // Reset validation state
            quantityInput.classList.remove('is-invalid', 'is-valid');
            
            // Validate quantity
            const quantity = parseInt(quantityInput.value);
            if (!quantity || quantity < 1) {
                quantityInput.classList.add('is-invalid');
                return false;
            }
            
            quantityInput.classList.add('is-valid');
            
            // If validation passes, submit the form
            this.submit();
        });
    }
    
    // Clear form validation when modals are hidden
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                // Reset form
                form.reset();
                
                // Clear validation classes
                form.querySelectorAll('.is-invalid, .is-valid').forEach(input => {
                    input.classList.remove('is-invalid', 'is-valid');
                });
                
                // Hide error messages
                form.querySelectorAll('.invalid-feedback').forEach(error => {
                    error.style.display = 'none';
                });
            }
        });
    });
});

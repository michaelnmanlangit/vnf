document.addEventListener('DOMContentLoaded', function() {
    // Form change detection for edit page
    const inventoryForm = document.querySelector('.inventory-form');
    const updateButton = document.querySelector('button[onclick="showUpdateModal()"]');
    
    if (inventoryForm && updateButton) {
        // Store initial form state
        const initialFormState = new FormData(inventoryForm);
        const initialValues = {};
        
        initialFormState.forEach((value, key) => {
            initialValues[key] = value;
        });
        
        // Disable button initially
        updateButton.disabled = true;
        updateButton.style.opacity = '0.5';
        updateButton.style.cursor = 'not-allowed';
        
        // Add change listeners to all form inputs
        const formInputs = inventoryForm.querySelectorAll('input, select, textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('change', checkFormChanges);
            input.addEventListener('input', checkFormChanges);
        });
        
        function checkFormChanges() {
            const currentFormState = new FormData(inventoryForm);
            let hasChanges = false;
            
            // Check if any field has changed
            for (let [key, value] of currentFormState.entries()) {
                if (initialValues[key] !== value) {
                    hasChanges = true;
                    break;
                }
            }
            
            // Enable or disable button based on changes
            if (hasChanges) {
                updateButton.disabled = false;
                updateButton.style.opacity = '1';
                updateButton.style.cursor = 'pointer';
            } else {
                updateButton.disabled = true;
                updateButton.style.opacity = '0.5';
                updateButton.style.cursor = 'not-allowed';
            }
        }
    }
});
function hideConfirmModal() {
    const modals = document.querySelectorAll('.modal-overlay');
    
    modals.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.remove('show');
    });
}

/**
 * Confirm form submission
 */
function confirmSubmit() {
    const form = document.querySelector('.inventory-form');
    
    if (form) {
        form.submit();
    }
}

/**
 * Confirm update
 */
function confirmUpdate() {
    const form = document.querySelector('.inventory-form');
    
    if (form) {
        form.submit();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modals = document.querySelectorAll('.modal-overlay');
    
    modals.forEach(modal => {
        if (e.target === modal) {
            hideConfirmModal();
        }
    });
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideConfirmModal();
    }
});

/**
 * Date validation helpers
 */
function getMinDate() {
    const today = new Date();
    return today.toISOString().split('T')[0];
}

function getMaxExpirationDate() {
    const date = new Date();
    date.setFullYear(date.getFullYear() + 2); // 2 years max
    return date.toISOString().split('T')[0];
}

// Set minimum date for date_received to prevent future dates
const dateReceivedInput = document.querySelector('input[name="date_received"]');
if (dateReceivedInput) {
    dateReceivedInput.setAttribute('max', getMinDate());
}

// Set date constraints for expiration_date
const expirationInput = document.querySelector('input[name="expiration_date"]');
if (expirationInput) {
    expirationInput.setAttribute('min', getMinDate());
    // Optional: set max to 2 years from now
    // expirationInput.setAttribute('max', getMaxExpirationDate());
}

/**
 * Format number inputs for clarity
 */
const numberInputs = document.querySelectorAll('input[type="number"]');
numberInputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value && this.name === 'quantity') {
            this.value = parseFloat(this.value).toFixed(2);
        }
        if (this.value && this.name === 'temperature_requirement') {
            this.value = parseFloat(this.value).toFixed(1);
        }
    });
});

/**
 * Prevent form submit on Enter in text fields (but allow in textarea)
 */
const form = document.querySelector('.inventory-form');
if (form) {
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });
}

/**
 * Add success/error flash messages if present in DOM
 */
function initializeFlashMessages() {
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => msg.remove(), 300);
        }, 4000);
    });
}

initializeFlashMessages();

/**
 * Export for inline use in templates
 */
window.showConfirmModal = showConfirmModal;
window.hideConfirmModal = hideConfirmModal || function() {
    const modal = document.getElementById('createModal') || document.getElementById('updateModal');
    if (modal) modal.style.display = 'none';
};
window.confirmSubmit = confirmSubmit;
window.confirmUpdate = confirmUpdate;

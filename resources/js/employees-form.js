document.addEventListener('DOMContentLoaded', function() {
    // Image Preview Functionality
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Check file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file (JPG, PNG, GIF)');
                    this.value = '';
                    return;
                }

                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }

                // Display preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Clear previous content
                    imagePreview.innerHTML = '';
                    
                    // Create and add image
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    
                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
            
            // Check for form changes
            checkFormChanges();
        });
    }

    // Form change detection for edit page
    const employeeForm = document.querySelector('.employee-form');
    const updateButton = document.querySelector('button[onclick="showUpdateModal()"]');
    
    if (employeeForm && updateButton) {
        // Store initial form state
        const initialFormState = new FormData(employeeForm);
        const initialValues = {};
        
        initialFormState.forEach((value, key) => {
            initialValues[key] = value;
        });
        
        // Disable button initially
        updateButton.disabled = true;
        updateButton.style.opacity = '0.5';
        updateButton.style.cursor = 'not-allowed';
        
        // Add change listeners to all form inputs
        const formInputs = employeeForm.querySelectorAll('input, select, textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('change', checkFormChanges);
            input.addEventListener('input', checkFormChanges);
        });
        
        function checkFormChanges() {
            const currentFormState = new FormData(employeeForm);
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

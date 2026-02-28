window.showLogoutModal = function() {
    document.getElementById('logoutModal').classList.add('active');
};

window.hideLogoutModal = function() {
    document.getElementById('logoutModal').classList.remove('active');
};

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
        logoutModal.addEventListener('click', function(e) {
            if (e.target === this) {
                window.hideLogoutModal();
            }
        });
    }
});

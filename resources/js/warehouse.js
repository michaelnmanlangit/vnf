window.showLogoutModal = function() {
    document.getElementById('logoutModal').classList.add('active');
}

window.hideLogoutModal = function() {
    document.getElementById('logoutModal').classList.remove('active');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
        logoutModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutModal();
            }
        });
    }

    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Close menu when a link is clicked
        const menuItems = sidebar.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });
        });

        // Show/hide menu toggle based on screen size
        function toggleMenuVisibility() {
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'block';
            } else {
                menuToggle.style.display = 'none';
                sidebar.classList.remove('active');
            }
        }

        toggleMenuVisibility();
        window.addEventListener('resize', toggleMenuVisibility);

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }
});

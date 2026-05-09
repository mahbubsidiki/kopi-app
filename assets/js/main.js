// Kopi Nusantara Pasuruan — Main JS

// Hamburger menu toggle
function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    if (navLinks) {
        navLinks.classList.toggle('open');
    }
}

// Auto-hide alert after 4 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 400);
        }, 4000);
    });

    // Fade-up animation for cards
    const cards = document.querySelectorAll('.product-card, .card, .order-card');
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeUp 0.45s ease forwards';
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        cards.forEach(function (c) {
            c.style.opacity = '0';
            obs.observe(c);
        });
    }
});

// Confirm delete
function confirmDelete(msg) {
    return confirm(msg || 'Yakin ingin menghapus item ini?');
}

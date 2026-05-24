/**
 * Modern UI/UX Interactions
 * Tourism Academy LMS
 */

document.addEventListener('DOMContentLoaded', function() {

    // ===================================
    // 1. Smooth Page Transitions
    // ===================================
    function initPageTransitions() {
        // Add page transition class
        document.body.classList.add('page-transition');

        // Intercept link clicks for smooth transitions
        document.querySelectorAll('a:not([target="_blank"])').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Skip if it's a hash link or external
                if (!href || href.startsWith('#') || href.startsWith('http') || href.includes('logout')) {
                    return;
                }

                e.preventDefault();

                // Fade out
                document.body.style.opacity = '0';

                // Navigate after animation
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            });
        });
    }

    // ===================================
    // 2. Scroll Progress Bar
    // ===================================
    function initScrollProgress() {
        const progressBar = document.createElement('div');
        progressBar.className = 'scroll-progress';
        document.body.appendChild(progressBar);

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercentage = (scrollTop / scrollHeight) * 100;
            progressBar.style.width = scrollPercentage + '%';
        });
    }

    // ===================================
    // 3. Animated Stats Counter
    // ===================================
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);

        const updateCounter = () => {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };

        updateCounter();
    }

    function initStatsCounters() {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.dataset.count);
                    animateCounter(entry.target, target);
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stats-number').forEach(stat => {
            statsObserver.observe(stat);
        });
    }

    // ===================================
    // 4. Lazy Loading Images
    // ===================================
    function initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    // ===================================
    // 5. Animate Elements on Scroll
    // ===================================
    function initScrollAnimations() {
        const animateObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated', 'fadeInUp');
                    animateObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            animateObserver.observe(el);
        });
    }

    // ===================================
    // 6. Interactive Cards
    // ===================================
    function initInteractiveCards() {
        document.querySelectorAll('.modern-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    // ===================================
    // 7. Smooth Scroll to Anchors
    // ===================================
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ===================================
    // 8. Loading Spinner
    // ===================================
    function showLoading() {
        const spinner = document.createElement('div');
        spinner.id = 'page-loader';
        spinner.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
            </div>
        `;
        document.body.appendChild(spinner);
    }

    function hideLoading() {
        const spinner = document.getElementById('page-loader');
        if (spinner) {
            spinner.remove();
        }
    }

    // ===================================
    // 9. Toast Notifications
    // ===================================
    function showToast(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
        toast.style.transform = 'translateX(400px)';
        toast.innerHTML = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);

        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ===================================
    // 10. Back to Top Button
    // ===================================
    function initBackToTop() {
        const button = document.createElement('button');
        button.className = 'fixed bottom-8 right-8 bg-blue-600 text-white p-4 rounded-full shadow-lg opacity-0 transition-opacity duration-300 hover:bg-blue-700 z-40';
        button.innerHTML = '↑';
        button.style.display = 'none';
        document.body.appendChild(button);

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                button.style.display = 'block';
                setTimeout(() => button.style.opacity = '1', 10);
            } else {
                button.style.opacity = '0';
                setTimeout(() => button.style.display = 'none', 300);
            }
        });

        button.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===================================
    // 11. Form Validation Enhancement
    // ===================================
    function enhanceFormValidation() {
        document.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', function() {
                if (this.validity.valid) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                } else {
                    this.classList.remove('border-green-500');
                    this.classList.add('border-red-500');
                }
            });
        });
    }

    // ===================================
    // 12. Dynamic Search
    // ===================================
    function initDynamicSearch() {
        const searchInputs = document.querySelectorAll('[data-search]');

        searchInputs.forEach(input => {
            let debounceTimer;
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.value.toLowerCase();
                    const target = document.querySelector(this.dataset.search);

                    if (target) {
                        const items = target.querySelectorAll('[data-search-item]');
                        items.forEach(item => {
                            const text = item.textContent.toLowerCase();
                            item.style.display = text.includes(query) ? '' : 'none';
                        });
                    }
                }, 300);
            });
        });
    }

    // ===================================
    // Initialize All
    // ===================================
    try {
        initPageTransitions();
        initScrollProgress();
        initStatsCounters();
        initLazyLoading();
        initScrollAnimations();
        initInteractiveCards();
        initSmoothScroll();
        initBackToTop();
        enhanceFormValidation();
        initDynamicSearch();

        console.log('✅ Modern UI/UX features initialized successfully');
    } catch (error) {
        console.error('❌ Error initializing UI features:', error);
    }

    // Expose global functions
    window.showToast = showToast;
    window.showLoading = showLoading;
    window.hideLoading = hideLoading;
});

// ===================================
// Performance Monitoring
// ===================================
if (window.performance) {
    window.addEventListener('load', () => {
        const perfData = window.performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        console.log(`📊 Page Load Time: ${pageLoadTime}ms`);
    });
}

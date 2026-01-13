/**
 * KV Wood Works - Main JavaScript
 * Interactive features and animations
 */

document.addEventListener('DOMContentLoaded', function () {

    // =============================================
    // HEADER SCROLL EFFECT
    // =============================================
    const mainHeader = document.getElementById('mainHeader');

    function handleScroll() {
        if (window.scrollY > 50) {
            mainHeader?.classList.add('scrolled');
        } else {
            mainHeader?.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll);

    // =============================================
    // MOBILE MENU TOGGLE
    // =============================================
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');

    function toggleMobileMenu() {
        mobileMenuToggle?.classList.toggle('active');
        navMenu?.classList.toggle('active');
        mobileNavOverlay?.classList.toggle('active');
        document.body.style.overflow = navMenu?.classList.contains('active') ? 'hidden' : '';
    }

    mobileMenuToggle?.addEventListener('click', toggleMobileMenu);
    mobileNavOverlay?.addEventListener('click', toggleMobileMenu);

    // Mobile dropdown toggles
    const hasDropdowns = document.querySelectorAll('.has-dropdown > a');
    hasDropdowns.forEach(item => {
        item.addEventListener('click', function (e) {
            if (window.innerWidth <= 992) {
                e.preventDefault();
                this.parentElement.classList.toggle('active');
            }
        });
    });

    // =============================================
    // BACK TO TOP BUTTON
    // =============================================
    const backToTop = document.getElementById('backToTop');

    function toggleBackToTop() {
        if (window.scrollY > 300) {
            backToTop?.classList.add('visible');
        } else {
            backToTop?.classList.remove('visible');
        }
    }

    window.addEventListener('scroll', toggleBackToTop);

    backToTop?.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // =============================================
    // FAQ ACCORDION
    // =============================================
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question?.addEventListener('click', function () {
            // Close other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });

            // Toggle current item
            item.classList.toggle('active');
        });
    });

    // =============================================
    // FORM VALIDATION
    // =============================================
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                // Remove previous error
                field.classList.remove('error');
                const errorMsg = field.parentElement.querySelector('.error-message');
                if (errorMsg) errorMsg.remove();

                // Check if field is empty
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');

                    const error = document.createElement('span');
                    error.className = 'error-message';
                    error.textContent = 'This field is required';
                    error.style.color = '#e74c3c';
                    error.style.fontSize = '0.85rem';
                    error.style.marginTop = '5px';
                    error.style.display = 'block';
                    field.parentElement.appendChild(error);
                }

                // Email validation
                if (field.type === 'email' && field.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value)) {
                        isValid = false;
                        field.classList.add('error');

                        const error = document.createElement('span');
                        error.className = 'error-message';
                        error.textContent = 'Please enter a valid email';
                        error.style.color = '#e74c3c';
                        error.style.fontSize = '0.85rem';
                        error.style.marginTop = '5px';
                        error.style.display = 'block';
                        field.parentElement.appendChild(error);
                    }
                }

                // Phone validation
                if (field.type === 'tel' && field.value.trim()) {
                    const phoneRegex = /^[0-9]{10}$/;
                    if (!phoneRegex.test(field.value.replace(/\s/g, ''))) {
                        isValid = false;
                        field.classList.add('error');

                        const error = document.createElement('span');
                        error.className = 'error-message';
                        error.textContent = 'Please enter a valid 10-digit phone number';
                        error.style.color = '#e74c3c';
                        error.style.fontSize = '0.85rem';
                        error.style.marginTop = '5px';
                        error.style.display = 'block';
                        field.parentElement.appendChild(error);
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // =============================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // =============================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);

                if (target) {
                    const headerHeight = mainHeader?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - headerHeight - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // =============================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // =============================================
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    animatedElements.forEach(el => observer.observe(el));

    // =============================================
    // COUNTER ANIMATION
    // =============================================
    const counters = document.querySelectorAll('.counter');

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };

                updateCounter();
                counterObserver.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => counterObserver.observe(counter));

    // =============================================
    // IMAGE LAZY LOADING
    // =============================================
    const lazyImages = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    }, { rootMargin: '50px' });

    lazyImages.forEach(img => imageObserver.observe(img));

    // =============================================
    // CATEGORY FILTER (for project/gallery pages)
    // =============================================
    const filterButtons = document.querySelectorAll('.filter-btn');
    const filterItems = document.querySelectorAll('.filter-item');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');

            // Update active button
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter items
            filterItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = '';
                    item.classList.add('show');
                } else {
                    item.style.display = 'none';
                    item.classList.remove('show');
                }
            });
        });
    });

    // =============================================
    // SIMPLE IMAGE SLIDER/CAROUSEL
    // =============================================
    const sliders = document.querySelectorAll('.simple-slider');

    sliders.forEach(slider => {
        const slides = slider.querySelectorAll('.slide');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');
        const dotsContainer = slider.querySelector('.slider-dots');
        let currentSlide = 0;

        // Create dots
        if (dotsContainer) {
            slides.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.className = 'slider-dot' + (index === 0 ? ' active' : '');
                dot.addEventListener('click', () => goToSlide(index));
                dotsContainer.appendChild(dot);
            });
        }

        function goToSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');
            currentSlide = index;

            // Update dots
            const dots = dotsContainer?.querySelectorAll('.slider-dot');
            dots?.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            goToSlide((currentSlide + 1) % slides.length);
        }

        function prevSlide() {
            goToSlide((currentSlide - 1 + slides.length) % slides.length);
        }

        nextBtn?.addEventListener('click', nextSlide);
        prevBtn?.addEventListener('click', prevSlide);

        // Auto-slide
        setInterval(nextSlide, 5000);
    });

    // =============================================
    // FORM SUBMISSION WITH AJAX (optional)
    // =============================================
    window.submitFormAjax = function (form, successCallback) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        }

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (successCallback) successCallback(data);
                } else {
                    alert(data.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
    };

    // =============================================
    // SCROLL REVEAL ANIMATION ON LOAD
    // =============================================
    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 100);

});

// =============================================
// UTILITY FUNCTIONS
// =============================================

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

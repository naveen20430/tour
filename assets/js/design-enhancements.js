/**
 * Design Enhancements & Interactive Animations
 * Modern JavaScript improvements for better user experience
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scroll reveal animations
    function initScrollReveal() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, observerOptions);

        // Add scroll-reveal class to cards and sections
        const revealElements = document.querySelectorAll('.card, .section-space > .container > .row > div, .tour-content > div');
        revealElements.forEach((el, index) => {
            el.classList.add('scroll-reveal');
            el.style.transitionDelay = `${index * 0.1}s`;
            observer.observe(el);
        });
    }

    // Enhanced button interactions
    function enhanceButtons() {
        const buttons = document.querySelectorAll('.travhub-btn, .btn');
        
        buttons.forEach(button => {
            // Add ripple effect
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
            
            // Add hover sound effect (optional)
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    // Smooth scrolling for navigation links
    function initSmoothScroll() {
        const navLinks = document.querySelectorAll('a[href^="#"]');
        
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Enhanced card hover effects
    function enhanceCards() {
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) rotateX(5deg)';
                this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                
                // Add glow effect
                this.style.boxShadow = '0 20px 40px rgba(102, 126, 234, 0.3)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) rotateX(0)';
                this.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.1)';
            });
        });
    }

    // Loading skeleton effect for images
    function initImageLoading() {
        const images = document.querySelectorAll('img');
        
        images.forEach(img => {
            if (!img.complete) {
                img.classList.add('loading-skeleton');
                
                img.addEventListener('load', function() {
                    this.classList.remove('loading-skeleton');
                    this.classList.add('fade-in-up');
                });
                
                img.addEventListener('error', function() {
                    this.classList.remove('loading-skeleton');
                    this.style.background = '#f0f0f0';
                });
            }
        });
    }

    // Enhanced form interactions
    function enhanceForms() {
        const formGroups = document.querySelectorAll('.form-group, .mb-3');
        
        formGroups.forEach(group => {
            const input = group.querySelector('input, select, textarea');
            const label = group.querySelector('label');
            
            if (input && label) {
                // Floating label effect
                input.addEventListener('focus', function() {
                    label.style.transform = 'translateY(-25px) scale(0.8)';
                    label.style.color = '#667eea';
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        label.style.transform = 'translateY(0) scale(1)';
                        label.style.color = '#495057';
                    }
                });
                
                // Check if input has value on load
                if (input.value) {
                    label.style.transform = 'translateY(-25px) scale(0.8)';
                    label.style.color = '#667eea';
                }
            }
        });
        
        // Form validation feedback
        const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.validity.valid) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            });
        });
    }

    // Parallax effect for hero sections
    function initParallax() {
        const heroSections = document.querySelectorAll('.tour-hero, .hero-section');
        
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            
            heroSections.forEach(hero => {
                const rate = scrolled * -0.5;
                hero.style.transform = `translateY(${rate}px)`;
            });
        });
    }

    // Price counter animation
    function animateCounters() {
        const counters = document.querySelectorAll('.price-display, .counter');
        
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                    
                    if (target > 0) {
                        animateValue(counter, 0, target, 1000);
                    }
                    
                    counterObserver.unobserve(counter);
                }
            });
        });
        
        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    function animateValue(element, start, end, duration) {
        const startTimestamp = performance.now();
        const prefix = element.textContent.match(/[^\d]/g)?.join('') || '';
        
        const step = (timestamp) => {
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = Math.floor(progress * (end - start) + start);
            element.textContent = prefix + current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };
        
        requestAnimationFrame(step);
    }

    // Search enhancement
    function enhanceSearch() {
        const searchToggler = document.querySelector('.search-toggler');
        const searchPopup = document.querySelector('.search-popup');
        
        if (searchToggler && searchPopup) {
            searchToggler.addEventListener('click', function(e) {
                e.preventDefault();
                searchPopup.classList.add('search-popup--visible');
                
                // Focus on search input
                setTimeout(() => {
                    const searchInput = searchPopup.querySelector('input');
                    if (searchInput) searchInput.focus();
                }, 300);
            });
        }
    }

    // Progress bar for scroll
    function initScrollProgress() {
        const progressBar = document.createElement('div');
        progressBar.id = 'scroll-progress';
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 9999;
            transition: width 0.1s ease-out;
        `;
        document.body.appendChild(progressBar);
        
        window.addEventListener('scroll', function() {
            const scrolled = (window.pageYOffset / (document.body.scrollHeight - window.innerHeight)) * 100;
            progressBar.style.width = scrolled + '%';
        });
    }

    // Theme toggle (if needed)
    function initThemeToggle() {
        const themeToggle = document.querySelector('.theme-toggle');
        
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-theme');
                localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
            });
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
            }
        }
    }

    // Initialize all enhancements
    function init() {
        initScrollReveal();
        enhanceButtons();
        initSmoothScroll();
        enhanceCards();
        initImageLoading();
        enhanceForms();
        initParallax();
        animateCounters();
        enhanceSearch();
        initScrollProgress();
        initThemeToggle();
        
        // Add loaded class to body for CSS animations
        document.body.classList.add('page-loaded');
        
        console.log('🎨 Design enhancements loaded successfully!');
    }

    // Start initialization
    init();
});

// Add CSS for ripple effect
const rippleCSS = `
<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(2);
        opacity: 0;
    }
}

.page-loaded .scroll-reveal {
    opacity: 1;
    transform: translateY(0);
}

.search-popup--visible {
    opacity: 1 !important;
    visibility: visible !important;
    transform: scale(1) !important;
}
</style>
`;

// Inject ripple CSS
document.head.insertAdjacentHTML('beforeend', rippleCSS);
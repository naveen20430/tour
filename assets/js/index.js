/**
 * Home Page JavaScript
 * Extracted from index.php inline scripts
 */

(function() {
    'use strict';

    const BASE_URL = window.BASE_URL || '';

    /**
     * Phone Modal Functions
     */
    function showPhoneModal() {
        // Get form values - handle both selectpicker and regular selects
        const destination = document.querySelector('select[name="destination"]');
        const destinationText = destination ? (destination.options[destination.selectedIndex]?.text || '') : '';
        const destinationValue = destination ? destination.value : '';
        
        const country = document.querySelector('select[name="country"]');
        const countryText = country ? (country.options[country.selectedIndex]?.text || '') : '';
        const countryValue = country ? country.value : '';
        
        // Handle datepicker inputs
        const travelDateInput = document.querySelector('input[name="travel_date"]');
        const travelDate = travelDateInput ? travelDateInput.value : '';
        
        const returnDateInput = document.querySelector('input[name="return_date"]');
        const returnDate = returnDateInput ? returnDateInput.value : '';
        
        // Build search details HTML
        let detailsHTML = '';
        if (destinationValue) detailsHTML += `<div><strong>Destination:</strong> ${destinationText}</div>`;
        if (countryValue) detailsHTML += `<div><strong>Country:</strong> ${countryText}</div>`;
        if (travelDate) detailsHTML += `<div><strong>Travel Date:</strong> ${travelDate}</div>`;
        if (returnDate) detailsHTML += `<div><strong>Return Date:</strong> ${returnDate}</div>`;
        
        if (!detailsHTML) {
            detailsHTML = '<div style="color: #6c757d; font-style: italic;">No search filters selected</div>';
        }
        
        const searchDetailsEl = document.getElementById('searchDetails');
        if (searchDetailsEl) {
            searchDetailsEl.innerHTML = detailsHTML;
        }
        
        const modal = document.getElementById('phoneModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    function closePhoneModal() {
        const modal = document.getElementById('phoneModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        const phoneInput = document.getElementById('modalPhone');
        if (phoneInput) {
            phoneInput.value = '';
        }
    }

    function submitSearch(event) {
        event.preventDefault();
        
        const phoneInput = document.getElementById('modalPhone');
        if (!phoneInput) return false;
        
        const phone = phoneInput.value;
        const phonePattern = /^[0-9]{10}$/;
        
        if (!phonePattern.test(phone)) {
            alert('Please enter a valid 10-digit phone number');
            return false;
        }
        
        // Get form data
        const formData = new FormData();
        const destination = document.querySelector('select[name="destination"]');
        const country = document.querySelector('select[name="country"]');
        const travelDateInput = document.querySelector('input[name="travel_date"]');
        const returnDateInput = document.querySelector('input[name="return_date"]');
        
        if (destination) formData.append('destination', destination.value);
        if (country) formData.append('country', country.value);
        if (travelDateInput) formData.append('travel_date', travelDateInput.value);
        if (returnDateInput) formData.append('return_date', returnDateInput.value);
        formData.append('phone', phone);
        
        // Save to database
        fetch(BASE_URL + 'api/save-search-query.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.text().then(text => {
                console.log('Raw Response:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
                }
            });
        })
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                // Redirect to tours page with search parameters
                const params = new URLSearchParams();
                formData.forEach((value, key) => {
                    if (value && key !== 'phone') params.append(key, value);
                });
                window.location.href = BASE_URL + 'tours.php?' + params.toString();
            } else {
                console.error('API Error:', data);
                alert('Error: ' + (data.message || 'Failed to save search query. Please try again.'));
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
            alert('Network error. Please check your connection and try again.');
        });
        
        return false;
    }

    /**
     * Card Hover Effects
     */
    function initCardHoverEffects() {
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                // Add enhanced glow effect
                this.style.boxShadow = '0 25px 50px rgba(102, 126, 234, 0.15), 0 0 0 1px rgba(102, 126, 234, 0.1)';
                
                // Show subtle hover overlay
                const overlay = this.querySelector('div[style*="opacity: 0"]');
                if (overlay) {
                    overlay.style.opacity = '1';
                    overlay.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%)';
                }
                
                // Add slight scale effect
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                // Reset styles
                this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.1)';
                this.style.transform = 'translateY(0) scale(1)';
                
                // Hide hover overlay
                const overlay = this.querySelector('div[style*="opacity: 1"]');
                if (overlay && overlay.style.background.includes('rgba(102, 126, 234')) {
                    overlay.style.opacity = '0';
                    overlay.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%)';
                }
            });
        });
    }

    /**
     * Stats Counter Animation
     */
    function initCounterAnimation() {
        const counters = document.querySelectorAll('.gradient-text');
        const observerOptions = { threshold: 0.7 };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const text = counter.textContent;
                    if (text.includes('+')) {
                        const number = parseInt(text);
                        if (number > 0) {
                            animateCounter(counter, 0, number, 1500);
                        }
                    }
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => {
            if (counter.textContent.includes('+')) {
                observer.observe(counter);
            }
        });
        
        function animateCounter(element, start, end, duration) {
            const startTime = performance.now();
            const suffix = element.textContent.match(/\+|\w+/g)?.slice(1).join(' ') || '';
            
            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(progress * (end - start) + start);
                element.textContent = current + '+' + (suffix ? ' ' + suffix : '');
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }
            
            requestAnimationFrame(updateCounter);
        }
    }

    /**
     * Parallax Effect for Floating Elements
     */
    function initParallaxEffect() {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const floatingElements = document.querySelectorAll('[style*="animation: float"]');
            
            floatingElements.forEach((el, index) => {
                const speed = 0.5 + (index * 0.2);
                el.style.transform = `translateY(${scrolled * speed * -0.1}px)`;
            });
        });
    }

    /**
     * Modal Event Handlers
     */
    function initModalHandlers() {
        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('phoneModal');
            if (event.target == modal) {
                closePhoneModal();
            }
        };

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePhoneModal();
            }
        });
    }

    /**
     * Initialize all functionality when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        initCardHoverEffects();
        initCounterAnimation();
        initParallaxEffect();
        initModalHandlers();
    });

    // Expose functions to global scope for onclick handlers
    window.showPhoneModal = showPhoneModal;
    window.closePhoneModal = closePhoneModal;
    window.submitSearch = submitSearch;

})();


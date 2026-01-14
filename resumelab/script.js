/**
 * script.js
 * 
 * Frontend JavaScript for tracking user events and interactions
 * Handles scroll depth tracking, CTA clicks, and modal display
 */

// Track API endpoint
const TRACK_API = 'track.php';

// Track scroll thresholds (to avoid duplicate tracking)
let scrollThresholds = {
    scroll_25: false,
    scroll_50: false,
    scroll_75: false,
    scroll_100: false
};

// Track if user has interacted
let hasInteracted = false;

// Track if exit event has been sent
let exitEventSent = false;

/**
 * Track an event by sending POST request to track.php
 * @param {string} eventName - Name of the event to track
 */
function trackEvent(eventName) {
    fetch(TRACK_API, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ event: eventName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Event tracked:', eventName);
        }
    })
    .catch(error => {
        console.error('Error tracking event:', error);
    });
}

/**
 * Show modal with thank you message
 */
function showModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.classList.add('show');
    }
}

/**
 * Hide modal
 */
function hideModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.classList.remove('show');
    }
}

/**
 * Track scroll depth based on scroll position
 */
function trackScrollDepth() {
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;

    // Track 25% scroll
    if (scrollPercentage >= 25 && !scrollThresholds.scroll_25) {
        scrollThresholds.scroll_25 = true;
        trackEvent('scroll_25');
    }

    // Track 50% scroll
    if (scrollPercentage >= 50 && !scrollThresholds.scroll_50) {
        scrollThresholds.scroll_50 = true;
        trackEvent('scroll_50');
    }

    // Track 75% scroll
    if (scrollPercentage >= 75 && !scrollThresholds.scroll_75) {
        scrollThresholds.scroll_75 = true;
        trackEvent('scroll_75');
    }

    // Track 100% scroll
    if (scrollPercentage >= 100 && !scrollThresholds.scroll_100) {
        scrollThresholds.scroll_100 = true;
        trackEvent('scroll_100');
    }
}

/**
 * Track exit without interaction
 * This is called when user leaves without clicking any CTA
 */
function trackExitNoInteraction() {
    if (!hasInteracted && !exitEventSent) {
        exitEventSent = true;
        trackEvent('exit_no_interaction');
    }
}

// Track page view on load
document.addEventListener('DOMContentLoaded', function() {
    trackEvent('page_view');

    // Set up scroll tracking with throttling
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(trackScrollDepth, 100);
    });

    // Track exit without interaction when user leaves
    window.addEventListener('beforeunload', trackExitNoInteraction);

    // Track exit without interaction after 30 seconds if no interaction
    setTimeout(function() {
        if (!hasInteracted) {
            trackExitNoInteraction();
        }
    }, 30000);
});

// CTA Resume Click Handler
document.addEventListener('DOMContentLoaded', function() {
    const ctaResume = document.getElementById('cta-resume');
    if (ctaResume) {
        ctaResume.addEventListener('click', function() {
            hasInteracted = true;
            trackEvent('cta_resume_click');
            showModal();
        });
    }

    // CTA Waitlist Click Handler
    const ctaWaitlist = document.getElementById('cta-waitlist');
    if (ctaWaitlist) {
        ctaWaitlist.addEventListener('click', function() {
            hasInteracted = true;
            trackEvent('cta_waitlist_click');
            showModal();
        });
    }

    // Bottom CTA Click Handler
    const ctaBottom = document.getElementById('cta-bottom');
    if (ctaBottom) {
        ctaBottom.addEventListener('click', function() {
            hasInteracted = true;
            trackEvent('cta_resume_click');
            showModal();
        });
    }

    // Pricing Click Handlers
    const pricingButtons = document.querySelectorAll('.pricing-click');
    pricingButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            hasInteracted = true;
            trackEvent('pricing_click');
            showModal();
        });
    });

    // Modal Close Handlers
    const modalClose = document.querySelector('.modal-close');
    if (modalClose) {
        modalClose.addEventListener('click', hideModal);
    }

    const modalCloseBtn = document.getElementById('modal-close-btn');
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', hideModal);
    }

    // Close modal when clicking outside
    const modal = document.getElementById('modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal();
            }
        });
    }
});


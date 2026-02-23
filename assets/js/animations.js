/**
 * Reveil 2026 - Creative Animation Layer
 * Native high-performance alternative to React/Framer Motion frontend bloat.
 */

document.addEventListener("DOMContentLoaded", () => {

    /* =========================================================================
       1. PAGE LOAD — Instant, no fade delays
       ========================================================================= */
    document.body.classList.add('page-loaded');

    /* =========================================================================
       2. STAGGERED REVEALS (Intersection Observer API)
       ========================================================================= */
    // Target key FSE blocks: Columns (Pillars & Expert Cards), Media-Text blocks, and any pre-tagged text
    const revealElements = document.querySelectorAll('.wp-block-column, .wp-block-media-text, .fse-reveal-item');

    // Add base classes for CSS transition
    const isMobile = window.matchMedia('(max-width: 768px)').matches;

    revealElements.forEach((el, index) => {
        if (!el.classList.contains('fse-reveal-item')) {
            el.classList.add('fse-reveal-item');
        }
        if (isMobile) {
            el.classList.add('fse-fade-only');
            el.style.transitionDelay = '0s';
        } else {
            // Calculate a dynamic stagger delay based on DOM order
            el.style.transitionDelay = `${(index % 4) * 0.15}s`;
        }
    });

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                // Unobserve after revealing to prevent bouncing on scroll up
                observer.unobserve(entry.target);
            }
        });
    }, {
        root: null,
        rootMargin: '0px',
        threshold: 0.15 // Trigger when 15% visible
    });

    revealElements.forEach(el => revealObserver.observe(el));


    /* =========================================================================
       3. INTERACTIVE MAGNETIC HOVER (Event Listeners)
       ========================================================================= */
    const magneticButtons = document.querySelectorAll('.wp-block-button__link');

    magneticButtons.forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            // Calculate distance from center
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            // Apply magnetic pull (strength: 0.2)
            btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px) scale(1.05)`;
        });

        btn.addEventListener('mouseleave', () => {
            // Snap back to original position
            btn.style.transform = `translate(0px, 0px) scale(1)`;
        });
    });


    /* =========================================================================
       4. SUBTLE PARALLAX (Scroll Listener + requestAnimationFrame)
       ========================================================================= */
    const parallaxImages = document.querySelectorAll('.wp-block-image img');

    let lastKnownScrollPosition = 0;
    let ticking = false;

    function applyParallax(scrollPos) {
        parallaxImages.forEach(img => {
            const rect = img.getBoundingClientRect();
            // Only calculate if image is somewhat in viewport
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                // Move image slightly slower than scroll speed
                const moveY = (rect.top * 0.05);
                img.style.transform = `translate3d(0, ${moveY}px, 0)`;
            }
        });
    }

    window.addEventListener('scroll', () => {
        lastKnownScrollPosition = window.scrollY;

        if (!ticking) {
            window.requestAnimationFrame(() => {
                applyParallax(lastKnownScrollPosition);
                ticking = false;
            });
            ticking = true;
        }
    });

    /* =========================================================================
       5. HERO VIDEO FREEZE (Ending on Last Frame)
       ========================================================================= */
    const heroVideo = document.querySelector('.hero-video-bg');
    if (heroVideo) {
        heroVideo.addEventListener('ended', () => {
            heroVideo.pause();
        });
    }

    /* =========================================================================
       6. 3D HOVER TILT (Framer Motion Emulation)
       ========================================================================= */
    const cards3D = document.querySelectorAll('.card-3d');

    cards3D.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();

            // Calculate center of card
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            // Calculate distance from center (-1 to 1)
            const deltaX = (e.clientX - centerX) / (rect.width / 2);
            const deltaY = (e.clientY - centerY) / (rect.height / 2);

            // Calculate rotation (Max 10 degrees)
            const rotateX = deltaY * -10; // Invert Y for native tilt feel
            const rotateY = deltaX * 10;

            // Apply 3D Transform
            // We use a short inline transition of 0.1s during active movement so it's snappy but not jittery
            card.style.transition = 'transform 0.1s ease-out, box-shadow 0.1s ease-out';
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            // Restore smooth CSS transition from stylesheet for the snap-back
            card.style.transition = '';
            card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
        });
    });

});

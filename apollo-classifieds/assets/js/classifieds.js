/**
 * Apollo Classifieds — Frontend Interactions
 * 
 * CRITICAL FLOW:
 * 1. User clicks "Contact" button on any classified card
 * 2. Modal opens with legal disclaimer
 * 3. User MUST check "I'm AWARE!" checkbox
 * 4. Only then, "INICIAR CHAT" button becomes active
 * 5. Click redirects to apollo-chat with user_id + classified_id
 * 
 * @package Apollo\Classifieds
 */

(function($) {
    'use strict';

    // GSAP Scroll Reveal Animations
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        
        gsap.utils.toArray('.reveal-up').forEach(elem => {
            gsap.fromTo(elem, 
                { y: 50, opacity: 0 },
                { 
                    y: 0, 
                    opacity: 1, 
                    duration: 1, 
                    ease: "power3.out", 
                    scrollTrigger: { 
                        trigger: elem, 
                        start: "top 90%" 
                    } 
                }
            );
        });
    }

    // ==========================================
    // MODAL DISCLAIMER SYSTEM
    // ==========================================
    const modal = $('#apollo-classifieds-modal');
    const consentCheckbox = $('#modal-consent-check');
    const proceedButton = $('#btn-proceed-chat');
    const closeButtons = $('.btn-close-modal');

    // Open modal on any .btn-open-modal click
    $(document).on('click', '.btn-open-modal', function(e) {
        e.preventDefault();
        
        const userId = $(this).data('user-id');
        const classifiedId = $(this).data('classified-id');
        
        // Store data in proceed button
        proceedButton.data('target-user', userId);
        proceedButton.data('classified-id', classifiedId);
        
        // Reset modal state
        consentCheckbox.prop('checked', false);
        proceedButton.removeClass('active');
        
        // Open modal
        modal.addClass('open');
    });

    // Checkbox toggle — CRITICAL: only activate when checked
    consentCheckbox.on('change', function() {
        if ($(this).is(':checked')) {
            proceedButton.addClass('active');
        } else {
            proceedButton.removeClass('active');
        }
    });

    // Close modal
    closeButtons.on('click', function() {
        modal.removeClass('open');
    });

    // Close on backdrop click
    modal.on('click', function(e) {
        if (e.target === this) {
            $(this).removeClass('open');
        }
    });

    // ==========================================
    // PROCEED TO CHAT (ONLY IF ACTIVE)
    // ==========================================
    proceedButton.on('click', function() {
        if (!$(this).hasClass('active')) {
            return; // Do nothing if not active
        }

        const userId = $(this).data('target-user');
        const classifiedId = $(this).data('classified-id');

        // Change button state
        $(this).text('Conectando...');

        // AJAX: Create chat thread or redirect
        $.ajax({
            url: apolloClassifieds.ajaxUrl,
            type: 'POST',
            data: {
                action: 'apollo_classifieds_init_chat',
                nonce: apolloClassifieds.nonce,
                user_id: userId,
                classified_id: classifiedId
            },
            success: function(response) {
                if (response.success && response.data.chat_url) {
                    // Redirect to chat
                    window.location.href = response.data.chat_url;
                } else {
                    alert('Erro ao iniciar conversa. Tente novamente.');
                    proceedButton.text('INICIAR CHAT');
                }
            },
            error: function() {
                alert('Erro de conexão. Verifique sua internet.');
                proceedButton.text('INICIAR CHAT');
            }
        });
    });

    // ==========================================
    // FILTERS (OPTIONAL BEHAVIOR)
    // ==========================================
    $(document).on('click', '.filter-pill', function() {
        $('.filter-pill').removeClass('active');
        $(this).addClass('active');
        
        // TODO: Implement AJAX filtering by category/tag
    });

})(jQuery);

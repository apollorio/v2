<?php
/**
 * Profile Depoimentos Part
 *
 * @package Apollo\Users
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Variables available: $user, $user_id, $is_own_profile
?>

<!-- DEPOIMENTOS SECTION (Full Width) -->
<div id="depoimentos-anchor"></div>

<!-- Divisor Title -->
<div class="a-user-container" style="padding-bottom: 0;">
    <div class="a-user-section-divider">
        <span>Depoimentos</span>
    </div>
</div>

<div class="a-user-depoimentos-container" style="margin-top: 0;">

    <div class="a-user-depo-section">
        <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
        <i class="ri-double-quotes-l" style="font-size: 32px; color: var(--border); margin-bottom: 16px;"></i>
        <blockquote class="a-user-depo-quote">
            "Rafael has an incredible ear for detail. Every event he touches turns to gold. The community he's built in Rio is a testament to his dedication."
        </blockquote>
        <div class="a-user-depo-user">
            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="User" class="a-user-depo-avatar">
            <div>
                <div class="a-user-depo-name">Clara Mendez</div>
                <div class="a-user-depo-role">Event Producer</div>
            </div>
        </div>
    </div>

     <div class="a-user-depo-section">
        <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
        <i class="ri-double-quotes-l" style="font-size: 32px; color: var(--border); margin-bottom: 16px;"></i>
        <blockquote class="a-user-depo-quote">
            "Trusted seller. The gear was exactly as described and he even gave me some production tips when I picked it up."
        </blockquote>
        <div class="a-user-depo-user">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="a-user-depo-avatar">
            <div>
                <div class="a-user-depo-name">Lucas Silva</div>
                <div class="a-user-depo-role">DJ / Producer</div>
            </div>
        </div>
    </div>

</div>

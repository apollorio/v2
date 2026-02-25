<?php
/**
 * Profile Feed Part
 *
 * @package Apollo\Users
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Variables available: $user, $user_id, $is_own_profile
?>

<!-- FILTERS -->
<nav class="a-user-filter-bar">
    <button class="a-user-btn-reset a-user-filter-btn active">Events</button>
    <button class="a-user-btn-reset a-user-filter-btn">Posts</button>
    <button class="a-user-btn-reset a-user-filter-btn">Classifieds Adverts</button>
    <button class="a-user-btn-reset a-user-filter-btn" onclick="document.getElementById('depoimentos-anchor').scrollIntoView({behavior:'smooth'})">Depoimentos</button>
</nav>

<!-- FEED GRID [ 1% | 48.5% | 1% | 48.5% | 1% ] -->
<section class="a-user-feed-grid">

    <!-- ROW 1 -->
    <!-- POST 1 (Left) -->
    <article class="a-user-card a-user-col-left">
        <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
        <!-- Hover Overlay -->
        <div class="a-user-card-overlay">
            <div class="a-user-overlay-content">
                <i class="ri-brain-ai-3-line"></i> 36 wows &nbsp; <i class="ri-chat-4-line"></i> 3 comentários
            </div>
        </div>

        <img src="https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?w=800&auto=format&fit=crop" class="a-user-card-img" alt="Event">
        <div class="a-user-card-body">
            <span class="a-user-card-meta">Upcoming Event • 24 Feb</span>
            <h3 class="a-user-card-title">Sunset Theory: Vol. 4</h3>
            <p class="a-user-card-text">Join us for an open-air session at the Museum of Tomorrow. Deep cuts and organic textures until the sun goes down.</p>
        </div>
    </article>

    <!-- POST 2 (Right) -->
    <article class="a-user-card a-user-col-right" style="background: var(--gray-3); border-color: var(--gray-3);">
        <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
        <!-- Hover Overlay -->
        <div class="a-user-card-overlay">
            <div class="a-user-overlay-content">
                <i class="ri-brain-ai-3-line"></i> 12 wows &nbsp; <i class="ri-chat-4-line"></i> 8 comentários
            </div>
        </div>

        <div class="a-user-player-box" style="background: transparent; border-color: rgba(0,0,0,0.05);">
            <i class="ri-play-circle-fill a-user-play-icon"></i>
            <div class="a-user-progress">
                <div class="a-user-progress-bar"></div>
            </div>
            <span style="font-family:var(--ff-mono); font-size:11px;">12:40</span>
        </div>
        <div class="a-user-card-body">
            <span class="a-user-card-meta">SoundCloud Premiere</span>
            <h3 class="a-user-card-title">Midnight Tapes (Live Recording)</h3>
            <p class="a-user-card-text">Recorded live at D-Edge. Raw energy and unreleased tracks.</p>
        </div>
    </article>

    <!-- ROW 2 -->
    <!-- POST 3 (Left) -->
    <article class="a-user-card a-user-col-left">
        <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
        <!-- Hover Overlay -->
        <div class="a-user-card-overlay">
            <div class="a-user-overlay-content">
                <i class="ri-brain-ai-3-line"></i> 128 wows &nbsp; <i class="ri-chat-4-line"></i> 42 comentários
            </div>
        </div>

        <div class="a-user-card-body">
            <span class="a-user-card-meta">Status Update</span>
            <p class="a-user-card-text" style="font-size: 18px; font-style: italic; color: var(--black-1);">
                "The new sound system at Capslock is absolutely mind-bending. Can't wait to test some new productions there next month."
            </p>
            <div style="margin-top:16px; display:flex; gap:16px; color:var(--muted); font-size:14px;">
                <span><i class="ri-brain-ai-3-line"></i> 128</span>
                <span><i class="ri-chat-3-line"></i> 42</span>
            </div>
        </div>
    </article>

    <!-- POST 4 (Right) -->
    <article class="a-user-card a-user-col-right" style="background: rgba(244, 95, 0, 0.1); border-color: rgba(244, 95, 0, 0.1);">
        <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
        <!-- Hover Overlay -->
        <div class="a-user-card-overlay">
            <div class="a-user-overlay-content">
                <i class="ri-brain-ai-3-line"></i> 5 wows &nbsp; <i class="ri-chat-4-line"></i> 2 comentários
            </div>
        </div>

        <div class="a-user-card-body" style="background: transparent;">
            <div style="display:flex; justify-content:space-between;">
                <span class="a-user-card-meta" style="color:var(--black-1);">Classified Advert</span>
                <i class="ri-price-tag-3-line" style="font-size:22px;color:var(--primary);"></i> <!-- RARE ORANGE -->
            </div>
            <h3 class="a-user-card-title">For Sale: Pioneer DJM-900</h3>
            <p class="a-user-card-text" style="color: var(--black-1);">Mint condition. Used only in home studio. Original box and cables included.</p>
            <button class="a-user-btn-reset" style="margin-top:16px; font-family:var(--ff-mono); font-size:12px; text-decoration:underline; color:var(--black-1);">Message </button>
        </div>
    </article>

    <!-- ROW 3 -->
    <!-- POST 5 (Left) -->
    <article class="a-user-card a-user-col-left" style="background: var(--black-1); border-color: var(--black-1);">
        <div class="a-user-delete-btn edit-only" style="border-color: #333;"><i class="ri-close-line"></i></div>
        <!-- Hover Overlay -->
        <div class="a-user-card-overlay">
            <div class="a-user-overlay-content">
                <i class="ri-brain-ai-3-line"></i> 89 wows &nbsp; <i class="ri-chat-4-line"></i> 15 comentários
            </div>
        </div>

        <div class="a-user-player-box" style="background: transparent; color:white; border-color: rgba(255,255,255,0.1);">
            <i class="ri-spotify-fill" style="font-size:32px;"></i>
            <div class="a-user-progress" style="background:rgba(255,255,255,0.3);">
                <div class="a-user-progress-bar" style="background:white; width:60%;"></div>
            </div>
        </div>
        <div class="a-user-card-body">
            <span class="a-user-card-meta" style="color: var(--white-1);">Spotify Playlist</span>
            <h3 class="a-user-card-title" style="color: var(--white-1);">Rio Underground Essentials</h3>
            <p class="a-user-card-text" style="color: var(--gray-5);">Curated selection of the finest local producers.</p>
        </div>
    </article>

    <!-- POST 6 (Right) -->
    <article class="a-user-card a-user-col-right">
         <div class="a-user-delete-btn edit-only"><i class="ri-close-line"></i></div>
         <!-- Hover Overlay -->
         <div class="a-user-card-overlay">
            <div class="a-user-overlay-content">
                <i class="ri-brain-ai-3-line"></i> 22 wows &nbsp; <i class="ri-chat-4-line"></i> 1 comentário
            </div>
         </div>

         <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800&auto=format&fit=crop" class="a-user-card-img" alt="Venue">
         <div class="a-user-card-body">
            <span class="a-user-card-meta">Location Scout</span>
            <h3 class="a-user-card-title">Hidden Warehouse Found</h3>
            <p class="a-user-card-text">Perfect acoustics for the next secret rave. Stay tuned for coordinates.</p>
        </div>
    </article>

</section>

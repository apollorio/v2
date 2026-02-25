<?php

/**
 * New Home — Radio Widget
 *
 * Synchronized SoundCloud playback engine. All users hear the same
 * track at the same position (time-sync via 3-hour block JSON).
 *
 * @package Apollo\Templates
 * @since   6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<aside class="nh-radio is-paused" id="nhRadio" aria-label="<?php esc_attr_e( 'Rádio Apollo', 'apollo-templates' ); ?>">
	<button class="nh-radio-btn" id="nhRadioBtn" aria-label="<?php esc_attr_e( 'Tocar rádio', 'apollo-templates' ); ?>">
		<svg id="nhRadioIcon" viewBox="0 0 24 24" aria-hidden="true">
			<polygon points="5 3 19 12 5 21 5 3"></polygon>
		</svg>
	</button>
	<div class="nh-radio-wave" aria-hidden="true">
		<span></span><span></span><span></span><span></span><span></span>
	</div>
	<div class="nh-radio-info" aria-live="polite">
		<span class="nh-radio-track">Apollo Radio</span>
		<span class="nh-radio-artist">loading…</span>
	</div>
	<span class="nh-radio-time" id="nhRadioTime" aria-hidden="true"></span>

	<!-- Hidden SoundCloud Iframe Engine -->
	<iframe id="sc-widget"
		width="100%"
		height="166"
		scrolling="no"
		frameborder="no"
		allow="autoplay"
		src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/293&auto_play=false&hide_related=true&visual=false"
		style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;z-index:-1;"></iframe>
</aside>

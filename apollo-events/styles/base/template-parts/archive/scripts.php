<?php

/**
 * Template Part: Events Archive — Scripts
 *
 * Client-side filtering engine (sound, date, search, category),
 * GSAP page-loader curtain, hero entrance, card stagger, toolbar scroll.
 *
 * Expects: $events (array) — passed from parent for JSON hydration.
 *
 * @package Apollo\Event
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<script>
(function() {
	'use strict';

	/* ───────────────────────────────────────────
		0. GSAP — Page Loader Curtain
		─────────────────────────────────────────── */
	const loader = document.querySelector('.page-loader');
	if (loader) {
		gsap.to(loader, {
			scaleY: 0,
			transformOrigin: 'top center',
			duration: 0.7,
			ease: 'power3.inOut',
			delay: 0.15,
			onComplete() {
				loader.remove();
			}
		});
	}

	/* ───────────────────────────────────────────
		1. GSAP — Hero Entrance
		─────────────────────────────────────────── */
	const heroTl = gsap.timeline({
		delay: 0.35,
		defaults: {
			ease: 'power3.out'
		}
	});
	heroTl
		.to('.ev-hero__breadcrumb', {
			opacity: 1,
			y: 0,
			duration: 0.5
		})
		.to('.ev-hero__title', {
			opacity: 1,
			y: 0,
			duration: 0.65
		}, '-=0.3')
		.to('.ev-hero__sub', {
			opacity: 1,
			y: 0,
			duration: 0.55
		}, '-=0.35')
		.to('.ev-hero__stat', {
			opacity: 1,
			y: 0,
			duration: 0.5
		}, '-=0.25');

	/* ───────────────────────────────────────────
		2. GSAP — Card Stagger on View
		─────────────────────────────────────────── */
	function revealCards() {
		const cards = document.querySelectorAll('.ev-card:not(.hidden):not(.visible)');
		if (!cards.length) return;

		gsap.to(cards, {
			opacity: 1,
			y: 0,
			duration: 0.55,
			stagger: 0.06,
			ease: 'power3.out',
			onStart() {
				cards.forEach(c => c.classList.add('visible'));
			}
		});
	}

	// Initial reveal after loader
	setTimeout(revealCards, 500);

	/* ───────────────────────────────────────────
		3. Filter State
		─────────────────────────────────────────── */
	let activeSound = 'all';
	let activeDate = 'all';
	let activeCat = 'all';
	let searchTerm = '';

	const cards = () => document.querySelectorAll('.ev-card');
	const counter = document.getElementById('ev-counter');
	const emptyLive = document.getElementById('ev-empty-live');
	const emptyStatic = document.getElementById('ev-empty');
	const filtersInfo = document.getElementById('ev-active-filters');
	const filtersText = document.getElementById('ev-active-text');

	const today = document.querySelector('.ev-main')?.dataset.today || '';
	const todayDate = today ? new Date(today + 'T00:00:00') : new Date();
	const currentWeek = getISOWeek(todayDate);
	const currentMonth = todayDate.getMonth();
	const currentYear = todayDate.getFullYear();

	function getISOWeek(d) {
		const date = new Date(d.getTime());
		date.setHours(0, 0, 0, 0);
		date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
		const week1 = new Date(date.getFullYear(), 0, 4);
		return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
	}

	/* ───────────────────────────────────────────
		4. Apply Filters
		─────────────────────────────────────────── */
	function applyFilters() {
		let visible = 0;

		cards().forEach(card => {
			let show = true;

			// Sound filter
			if (activeSound !== 'all') {
				const cardSounds = (card.dataset.sounds || '').split(',');
				if (!cardSounds.includes(activeSound)) show = false;
			}

			// Date filter
			if (show && activeDate !== 'all') {
				const cardDate = card.dataset.date || '';
				if (activeDate === 'today') {
					if (cardDate !== today) show = false;
				} else if (activeDate === 'week') {
					const cardWeek = parseInt(card.dataset.week, 10);
					if (cardWeek !== currentWeek) show = false;
				} else if (activeDate === 'month') {
					if (cardDate) {
						const d = new Date(cardDate + 'T00:00:00');
						if (d.getMonth() !== currentMonth || d.getFullYear() !== currentYear) show = false;
					} else {
						show = false;
					}
				}
			}

			// Category filter
			if (show && activeCat !== 'all') {
				const cardCats = (card.dataset.cats || '').split(',');
				if (!cardCats.includes(activeCat)) show = false;
			}

			// Search filter
			if (show && searchTerm) {
				const haystack = card.dataset.search || '';
				if (!haystack.includes(searchTerm)) show = false;
			}

			card.classList.toggle('hidden', !show);
			if (show) visible++;
		});

		// Counter
		if (counter) counter.textContent = visible;

		// Empty state
		const anyFilters = activeSound !== 'all' || activeDate !== 'all' || activeCat !== 'all' || searchTerm;
		if (emptyLive) emptyLive.classList.toggle('visible', visible === 0 && anyFilters);
		if (emptyStatic) emptyStatic.style.display = visible === 0 && !anyFilters ? 'flex' : 'none';

		// Active filters indicator
		if (filtersInfo && filtersText) {
			if (anyFilters) {
				const parts = [];
				if (activeSound !== 'all') parts.push(activeSound);
				if (activeDate !== 'all') parts.push(activeDate);
				if (activeCat !== 'all') parts.push(activeCat);
				if (searchTerm) parts.push('"' + searchTerm + '"');
				filtersText.textContent = visible + ' resultado' + (visible !== 1 ? 's' : '') + ' — ' + parts.join(
					' · ');
				filtersInfo.classList.add('visible');
			} else {
				filtersInfo.classList.remove('visible');
			}
		}

		// Re-reveal visible cards
		document.querySelectorAll('.ev-card:not(.hidden)').forEach(c => {
			if (!c.classList.contains('visible')) {
				c.classList.add('visible');
				gsap.fromTo(c, {
					opacity: 0,
					y: 24
				}, {
					opacity: 1,
					y: 0,
					duration: 0.45,
					ease: 'power3.out'
				});
			}
		});
	}

	/* ───────────────────────────────────────────
		5. Sound Pills
		─────────────────────────────────────────── */
	document.querySelectorAll('#ev-sound-pills .ev-pill').forEach(pill => {
		pill.addEventListener('click', () => {
			document.querySelectorAll('#ev-sound-pills .ev-pill').forEach(p => p.classList.remove(
				'active'));
			pill.classList.add('active');
			activeSound = pill.dataset.sound;
			applyFilters();
		});
	});

	/* ───────────────────────────────────────────
		6. Date Pills
		─────────────────────────────────────────── */
	document.querySelectorAll('#ev-date-pills .ev-pill').forEach(pill => {
		pill.addEventListener('click', () => {
			document.querySelectorAll('#ev-date-pills .ev-pill').forEach(p => p.classList.remove(
				'active'));
			pill.classList.add('active');
			activeDate = pill.dataset.date;
			applyFilters();
		});
	});

	/* ───────────────────────────────────────────
		7. Search
		─────────────────────────────────────────── */
	const searchInput = document.getElementById('ev-search');
	let searchTimeout;
	if (searchInput) {
		searchInput.addEventListener('input', () => {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				searchTerm = searchInput.value.trim().toLowerCase();
				applyFilters();
			}, 200);
		});
	}

	/* ───────────────────────────────────────────
		8. Category Filter
		─────────────────────────────────────────── */
	const catFilter = document.getElementById('ev-category-filter');
	if (catFilter) {
		catFilter.addEventListener('change', () => {
			activeCat = catFilter.value;
			applyFilters();
		});
	}

	/* ───────────────────────────────────────────
		9. Clear All
		─────────────────────────────────────────── */
	function clearAll() {
		activeSound = 'all';
		activeDate = 'all';
		activeCat = 'all';
		searchTerm = '';

		document.querySelectorAll('#ev-sound-pills .ev-pill').forEach((p, i) => p.classList.toggle('active', i ===
			0));
		document.querySelectorAll('#ev-date-pills .ev-pill').forEach((p, i) => p.classList.toggle('active', i ===
			0));
		if (searchInput) searchInput.value = '';
		if (catFilter) catFilter.value = 'all';
		applyFilters();
	}

	document.getElementById('ev-clear-all')?.addEventListener('click', clearAll);
	document.querySelector('.ev-empty__clear')?.addEventListener('click', clearAll);

	/* ───────────────────────────────────────────
		10. Toolbar Scroll Shadow
		─────────────────────────────────────────── */
	const toolbar = document.getElementById('ev-toolbar');
	if (toolbar) {
		let ticking = false;
		window.addEventListener('scroll', () => {
			if (!ticking) {
				requestAnimationFrame(() => {
					toolbar.classList.toggle('scrolled', window.scrollY > 200);
					ticking = false;
				});
				ticking = true;
			}
		}, {
			passive: true
		});
	}

})();
</script>

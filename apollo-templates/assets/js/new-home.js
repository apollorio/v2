/* ═══════════════════════════════════════════════════════════════════
   APOLLO::RIO — new_home.js  v4.1.0
   Page-level interactions for /home panel layout.
   Depends on: Apollo CDN (GSAP 3.14.2, RemixIcon), page-layout.js
   ═══════════════════════════════════════════════════════════════════
   PART MAPPING (PHP modular reference):
     Radio engine      → apollo-home/templates/partials/radio-widget.php
     Navbar scroll     → nh-navbar (persistent-ui.php)
     Menu FAB + sheet  → nh-menu-fab + nh-menu-sheet (persistent-ui.php)
     Profile dropdown  → nhProfileSheet (persistent-ui.php)
     GSAP hero         → panel-home.php hero section
   ═══════════════════════════════════════════════════════════════════ */

;(function () {
  'use strict';

  /* ─── CACHE DOM ─────────────────────────────────────────────────── */
  var navbar       = document.getElementById('nhNav');
  var homePanel    = document.querySelector('[data-panel="home"]');

  // Radio
  var radioWidget  = document.getElementById('nhRadio');
  var radioBtn     = document.getElementById('nhRadioBtn');
  var radioIcon    = document.getElementById('nhRadioIcon');

  // Menu FAB + upward sheet
  var menuFab      = document.getElementById('nhMenuFab');
  var menuSheet    = document.getElementById('nhMenuSheet');

  // Profile dropdown
  var profileBtn   = document.getElementById('nhProfileBtn');
  var profileSheet = document.getElementById('nhProfileDropdown');

  // Apps dropdown
  var appsBtn      = document.getElementById('nhAppsBtn');
  var appsSheet    = document.getElementById('nhAppsDropdown');


  /* ════════════════════════════════════════════════════════════════════
     APOLLO RADIO — Synchronized Playback Engine v4.0
     Modernized: Official SC Widget API with robust sync logic.
     Sync: Per-second position enforce (no drift), pause jumps to live on resume.
     ════════════════════════════════════════════════════════════════════ */

  const SVG_PAUSE  = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
  const SVG_PLAY   = '<polygon points="5 3 19 12 5 21 5 3"></polygon>';
  const RADIO_BASE = 'radio/';

  let isPlaying = false;
  let currentPlaylist = [];
  let totalDuration = 0;
  let syncInterval = null;
  let scWidget = null;
  let scApiLoaded = false;
  let currentTrackUrl = null;

  // Load SC Widget API
  function loadSCApi() {
    if (scApiLoaded) return Promise.resolve();
    if (window.SC && window.SC.Widget) {
      scApiLoaded = true;
      return Promise.resolve();
    }
    return new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = 'https://w.soundcloud.com/player/api.js';
      s.onload = () => { scApiLoaded = true; resolve(); };
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  // Calculate current live position (seconds since midnight % total)
  function getCurrentLivePosition() {
    if (totalDuration === 0) return 0;
    const now = new Date();
    const midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const secondsSinceMidnight = (now - midnight) / 1000;
    return secondsSinceMidnight % totalDuration;
  }

  // Find current track and offset from position
  function findTrackAndOffset(position) {
    let elapsed = 0;
    for (let i = 0; i < currentPlaylist.length; i++) {
      const track = currentPlaylist[i];
      if (position < elapsed + track.time) {
        return { index: i, offset: position - elapsed, track };
      }
      elapsed += track.time;
    }
    return { index: 0, offset: 0, track: currentPlaylist[0] };
  }

  function fmtTime(s) {
    return Math.floor(s / 60) + ':' + String(Math.floor(s % 60)).padStart(2, '0');
  }

  function updateUI(title, dj) {
    const t = document.querySelector('.nh-radio-track');
    const a = document.querySelector('.nh-radio-artist');
    if (t) t.textContent = title || '—';
    if (a) a.textContent = dj || '';
  }

  // Load and play at position
  async function loadAndPlayTrack(track, offset) {
    try {
      await loadSCApi();
      const iframe = document.getElementById('sc-widget');
      if (!iframe) return;

      if (!scWidget) {
        scWidget = SC.Widget(iframe);
      }

      updateUI(track.title, track.dj);

      if (currentTrackUrl !== track.url) {
        currentTrackUrl = track.url;
        scWidget.load(track.url, {
          auto_play: true,
          hide_related: true,
          show_comments: false,
          show_user: false,
          show_reposts: false,
          visual: false,
          callback: () => {
            scWidget.seekTo(offset * 1000);
            scWidget.play();
          }
        });
      } else {
        scWidget.seekTo(offset * 1000);
        scWidget.play();
      }
    } catch (e) {
      console.error('[Apollo Radio] Track load failed:', e);
    }
  }

  // Enforce sync every second (anti-drift)
  function startSyncCheck() {
    if (syncInterval) clearInterval(syncInterval);
    syncInterval = setInterval(() => {
      if (!isPlaying || !scWidget) return;
      const livePos = getCurrentLivePosition();
      const { track, offset: liveOffset } = findTrackAndOffset(livePos);
      
      // Update time display
      const timeEl = document.getElementById('nhRadioTime');
      if (timeEl) timeEl.textContent = fmtTime(liveOffset);

      scWidget.getPosition((currentPosMs) => {
        const currentPos = currentPosMs / 1000;
        if (currentTrackUrl !== track.url || Math.abs(currentPos - liveOffset) > 3) {
          loadAndPlayTrack(track, liveOffset);
        }
      });
    }, 1000);
  }

  // Init radio
  async function initRadio() {
    const now = new Date();
    const hour = Math.floor(now.getHours() / 3) * 3;
    const pad = String(hour).padStart(2, '0');
    
    try {
      const res = await fetch(`${RADIO_BASE}radio.${pad}.json`);
      if (!res.ok) throw new Error('Playlist fetch failed');
      currentPlaylist = await res.json();
      totalDuration = currentPlaylist.reduce((sum, t) => sum + t.time, 0);
      
      // Preload info
      const livePos = getCurrentLivePosition();
      const { track } = findTrackAndOffset(livePos);
      if (track) updateUI(track.title, track.dj);
      
    } catch (e) {
      console.error('[Apollo Radio] Init failed:', e);
      updateUI('Offline', '—');
      return;
    }

    if (radioBtn) {
      radioBtn.addEventListener('click', () => {
        isPlaying = !isPlaying;
        if (radioWidget) {
          radioWidget.classList.toggle('is-paused', !isPlaying);
        }
        if (radioIcon) {
          radioIcon.innerHTML = isPlaying ? SVG_PAUSE : SVG_PLAY;
        }
        radioBtn.setAttribute('aria-label', isPlaying ? 'Pausar rádio' : 'Tocar rádio');

        if (isPlaying) {
          const pos = getCurrentLivePosition();
          const { track, offset } = findTrackAndOffset(pos);
          loadAndPlayTrack(track, offset);
          startSyncCheck();
        } else {
          if (scWidget) scWidget.pause();
          if (syncInterval) clearInterval(syncInterval);
        }
      });
    }
  }

  initRadio();


  /* ─── NAVBAR SCROLL (panel-aware) ──────────────────────────────────
     body is locked by page-layout.js — listen on [data-panel="home"]
     which is the actual scrolling surface.                            */
  if (navbar && homePanel) {
    var ticking = false;
    homePanel.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(function () {
          navbar.classList.toggle('scrolled', homePanel.scrollTop > 72);
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }


  /* ─── MENU FAB — open/close upward sheet ───────────────────────────
     Single circle button bottom-right.
     Click → toggles .is-open on both FAB and sheet.
     Sheet slides from bottom to top via CSS transform.
     Clicking outside closes both.                                      */
  function openMenuSheet() {
    menuFab.classList.add('is-open');
    menuSheet.classList.add('is-open');
    menuFab.setAttribute('aria-expanded', 'true');
    // Close profile if open
    closeProfileSheet();
  }
  function closeMenuSheet() {
    menuFab.classList.remove('is-open');
    menuSheet.classList.remove('is-open');
    menuFab.setAttribute('aria-expanded', 'false');
  }
  function isMenuOpen() {
    return menuSheet.classList.contains('is-open');
  }

  if (menuFab && menuSheet) {
    menuFab.addEventListener('click', function (e) {
      e.stopPropagation();
      isMenuOpen() ? closeMenuSheet() : openMenuSheet();
    });
  }


  /* ─── PROFILE DROPDOWN (navbar top-right) ───────────────────────── */
  function openProfileSheet() {
    if (!profileSheet) return;
    profileSheet.classList.add('is-open');
    profileBtn.setAttribute('aria-expanded', 'true');
    closeMenuSheet();
    closeAppsSheet();
  }
  function closeProfileSheet() {
    if (!profileSheet) return;
    profileSheet.classList.remove('is-open');
    if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
  }
  function isProfileOpen() {
    return profileSheet && profileSheet.classList.contains('is-open');
  }

  if (profileBtn && profileSheet) {
    profileBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      isProfileOpen() ? closeProfileSheet() : openProfileSheet();
    });
    profileSheet.addEventListener('click', function (e) { e.stopPropagation(); });
  }


  /* ─── APPS DROPDOWN (navbar) ─────────────────────────────────────── */
  function openAppsSheet() {
    if (!appsSheet) return;
    appsSheet.classList.add('is-open');
    appsBtn.setAttribute('aria-expanded', 'true');
    closeMenuSheet();
    closeProfileSheet();
  }
  function closeAppsSheet() {
    if (!appsSheet) return;
    appsSheet.classList.remove('is-open');
    if (appsBtn) appsBtn.setAttribute('aria-expanded', 'false');
  }
  function isAppsOpen() {
    return appsSheet && appsSheet.classList.contains('is-open');
  }

  if (appsBtn && appsSheet) {
    appsBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      isAppsOpen() ? closeAppsSheet() : openAppsSheet();
    });
    appsSheet.addEventListener('click', function (e) { e.stopPropagation(); });
  }


  /* ─── CLICK OUTSIDE — close all sheets ─────────────────────────── */
  document.addEventListener('click', function (e) {
    if (menuFab && menuSheet && !menuFab.contains(e.target) && !menuSheet.contains(e.target)) {
      closeMenuSheet();
    }
    if (profileBtn && profileSheet && !profileBtn.contains(e.target) && !profileSheet.contains(e.target)) {
      closeProfileSheet();
    }
    if (appsBtn && appsSheet && !appsBtn.contains(e.target) && !appsSheet.contains(e.target)) {
      closeAppsSheet();
    }
  });

  /* ESC key closes any open sheet */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeMenuSheet();
      closeProfileSheet();
      closeAppsSheet();
    }
  });


  /* ─── AJAX LOGIN (guest profile dropdown) ───────────────────────── */
  var loginForm = document.getElementById('nhLoginForm');
  var loginBtn  = document.getElementById('nhLoginSubmit');

  if (loginForm && loginBtn) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var user = document.getElementById('nh-login-user');
      var pass = document.getElementById('nh-login-pass');
      if (!user || !pass || !user.value.trim() || !pass.value) return;

      loginBtn.classList.add('loading');
      loginBtn.disabled = true;

      var formData = new FormData(loginForm);
      formData.append('action', 'apollo_navbar_login');

      fetch(
        (typeof window.apolloNavbar !== 'undefined' ? window.apolloNavbar.ajaxUrl : '/wp-admin/admin-ajax.php'),
        { method: 'POST', body: formData, credentials: 'same-origin' }
      )
      .then(function (r) { return r.json(); })
      .then(function (data) {
        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;
        if (data.success) {
          if (data.data && data.data.redirect) {
            window.location.href = data.data.redirect;
          } else {
            window.location.reload();
          }
        } else {
          showLoginError(data.data ? data.data.message : 'Erro ao fazer login');
        }
      })
      .catch(function (err) {
        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;
        showLoginError(err.message || 'Erro de conexão');
      });
    });
  }

  function showLoginError(msg) {
    if (!loginForm) return;
    var el = loginForm.querySelector('.nh-login-error');
    if (!el) {
      el = document.createElement('div');
      el.className = 'nh-login-error';
      el.style.cssText = 'color:#ef4444;font-size:.78rem;text-align:center;margin-top:8px;';
      loginForm.appendChild(el);
    }
    el.textContent = msg;
    setTimeout(function () { if (el) el.remove(); }, 5000);
  }


  /* ─── LIVE POLLING — badge updates (chat + notif) ───────────────── */
  (function () {
    var nav = document.getElementById('nhNav');
    if (!nav || nav.getAttribute('data-auth') !== 'logged') return;
    if (!window.apolloNavbar || !window.apolloNavbar.restUrl) return;

    var pollInterval = 8000;
    var lastPollTime = '';

    function poll() {
      var url = window.apolloNavbar.restUrl + 'chat/poll';
      if (lastPollTime) url += '?since=' + encodeURIComponent(lastPollTime);

      fetch(url, {
        headers: { 'X-WP-Nonce': window.apolloNavbar.nonce },
        credentials: 'same-origin'
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        lastPollTime = data.timestamp || '';

        var nBadge = document.getElementById('nhNotifBadge');
        if (nBadge) nBadge.dataset.notif = (data.unread_notifs > 0) ? 'true' : 'false';

        var cBadge = document.getElementById('nhChatBadge');
        if (cBadge) cBadge.dataset.notif = (data.unread_messages > 0) ? 'true' : 'false';
      })
      .catch(function () { /* silent */ });
    }

    setTimeout(function () {
      poll();
      setInterval(poll, pollInterval);
    }, 3000);
  })();


  /* ─── GSAP HERO EFFECTS ─────────────────────────────────────────── */
  function waitForGsap(cb, n) {
    n = n || 0;
    if (typeof gsap !== 'undefined') return cb();
    if (n < 100) return setTimeout(function () { waitForGsap(cb, n + 1); }, 50);
  }

  waitForGsap(function () {

    /* Cancel CSS fallback animation — GSAP takes over from here */
    if (homePanel) {
      homePanel.querySelectorAll('.ai').forEach(function (el) {
        el.style.animation = 'none';
      });
    }

    /* Hero .ai entrance — force visible immediately then animate.
       Set opacity:1 first as safety, then animate in. */
    var heroEl = document.getElementById('nhHero');
    if (heroEl) {
      var heroAiItems = heroEl.querySelectorAll('.ai');
      if (heroAiItems.length) {
        /* Safety: make sure they're visible even if gsap animation hiccups */
        heroAiItems.forEach(function(el) { el.style.opacity = '1'; el.style.transform = 'none'; });
        gsap.fromTo(heroAiItems,
          { y: 18, opacity: 0 },
          { y: 0, opacity: 1, duration: 0.7, ease: 'power3.out', stagger: 0.12, delay: 0.3, overwrite: 'auto' }
        );
      }
    }

    /* Hero video parallax — scroller is the panel, not window */
    var heroVid = document.getElementById('nhHeroVid');
    if (heroVid && homePanel && typeof ScrollTrigger !== 'undefined') {
      gsap.registerPlugin(ScrollTrigger);

      gsap.to(heroVid, {
        scale: 1.12,
        ease: 'none',
        scrollTrigger: {
          trigger: '.nh-hero',
          scroller: homePanel,
          start: 'top top',
          end: 'bottom top',
          scrub: 1.5,
        }
      });
    }

    /* Menu FAB entrance — springy pop from bottom */
    if (menuFab) {
      gsap.fromTo(menuFab,
        { y: 30, scale: 0.5, opacity: 0 },
        { y: 0, scale: 1, opacity: 1, duration: 0.7, delay: 1.6, ease: 'back.out(2.2)', overwrite: 'auto', clearProps: 'transform' }
      );
    }

    /* Radio entrance — slide from left */
    if (radioWidget) {
      gsap.from(radioWidget, {
        x: -40,
        opacity: 0,
        duration: 0.9,
        delay: 2.0,
        ease: 'power3.out',
        overwrite: 'auto',
        clearProps: 'transform',
      });
    }

    /* Scroll-triggered reveal for all .ai elements in each section */
    if (typeof ScrollTrigger !== 'undefined' && homePanel) {
      var sections = homePanel.querySelectorAll('.section');
      sections.forEach(function (sec) {
        var items = sec.querySelectorAll('.ai');
        if (!items.length) return;
        ScrollTrigger.create({
          trigger: sec,
          scroller: homePanel,
          start: 'top 86%',
          once: true,
          onEnter: function () {
            gsap.to(items, {
              y: 0,
              opacity: 1,
              duration: 0.52,
              ease: 'power3.out',
              stagger: 0.055,
              overwrite: 'auto',
            });
          }
        });
      });
    }

  });


  /* ─── MONTH DROPDOWN (September start, +2 months, Portal) ────────
     Trigger → click → reveals row of month options + portal link.
     Current = September, then +1 (October), +2 (November), + Portal.  */
  var monthTrigger = document.getElementById('nhMonthTrigger');
  var monthMenu    = document.getElementById('nhMonthMenu');
  var monthText    = monthTrigger ? monthTrigger.querySelector('.nh-month-text') : null;

  if (monthTrigger && monthMenu && monthText) {
    var allMonths = ['January','February','March','April','May','June',
                     'July','August','September','October','November','December'];
    var startIdx = new Date().getMonth(); // Dynamic: always starts at current month

    var curMonth  = allMonths[startIdx];
    var nextMonth = allMonths[(startIdx + 1) % 12];
    var plusTwo   = allMonths[(startIdx + 2) % 12];

    var monthOptions = [
      { text: curMonth,  type: 'month' },
      { text: nextMonth, type: 'month' },
      { text: plusTwo,   type: 'month' },
      { text: '<i class="ri-calendar-2-line"></i> Ver todos', type: 'link', url: '/portal' },
      { text: '<i class="ri-calendar-schedule-line"></i> Inluir evento', type: 'link', url: '/add-evento' }
    ];

    // Set trigger text
    monthText.textContent = curMonth;

    // Populate row
    monthMenu.innerHTML = monthOptions.map(function (opt) {
      var isActive = (opt.text === curMonth) ? ' active' : '';
      var isPortal = (opt.type === 'link') ? ' nh-portal-link' : '';
      var href = opt.url || '#';
      return '<li><a href="' + href + '" class="' + isActive + isPortal + '" data-type="' + opt.type + '">' + opt.text + '</a></li>';
    }).join('');

    // Toggle visibility
    monthTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      monthMenu.classList.toggle('is-visible');

      // GSAP stagger if available
      if (typeof gsap !== 'undefined' && monthMenu.classList.contains('is-visible')) {
        gsap.from('#nhMonthMenu li', {
          x: 10,
          opacity: 0,
          stagger: 0.05,
          ease: 'power2.out'
        });
      }
    });

    // Option click
    monthMenu.addEventListener('click', function (e) {
      var target = e.target.closest('a');
      if (!target) return;

      if (target.dataset.type === 'month') {
        e.preventDefault();
        monthText.textContent = target.textContent.trim();
        monthMenu.querySelectorAll('a').forEach(function (a) { a.classList.remove('active'); });
        target.classList.add('active');
        monthMenu.classList.remove('is-visible');
      }
      // Portal link follows href naturally
    });

    // Close on outside click
    document.addEventListener('click', function () {
      monthMenu.classList.remove('is-visible');
    });
  }

})();

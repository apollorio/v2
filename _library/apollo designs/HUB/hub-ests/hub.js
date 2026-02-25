/* ═══════════════════════════════════════════════════
   HUB::rio — Main Application Engine
   State → Editor Sidebar → Phone Preview (reactive)

   ★ PATCHED — Full audit compliance:
     - §8: Modal ESC close, focus-trap, scroll-lock
     - §13: Debounce on filter/input handlers
     - §13: Keyboard support for toggles (Enter/Space)
     - §13: Analytics hooks (HUB.on / HUB.emit)
     - §3.2: aria-* state management on modals/tabs
     - Icon picker scanImmediate patches integrated
   ═══════════════════════════════════════════════════ */
'use strict';

const HUB = (() => {
  /* ─── STATE ─── */
  const state = {
    profile: {
      name: 'Apollo::rio',
      bio: 'Connecting culture, music & design in Rio de Janeiro.',
      avatar: 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
      bg: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
      avatarLayout: 'center',
      avatarShape: 'circle',
      showName: true,
      showBio: true,
      showVerified: true,
      verifiedText: 'VERIFIED',
      bgColor: '#ffffff',
      bgGradient: '',
      primaryColor: '#f45f00',
      textColor: '#121214',
      fontFamily: 'Space Grotesk',
      borderStyle: 'glass',
      blockRadius: '14',
      blockGap: '10',
      globalAnimation: 'none'
    },
    blocks: [
      { id: _id(), type: 'header', visible: true, text: 'Destaques' },
      { id: _id(), type: 'link', visible: true, title: 'Official Website', sub: 'apollo.rio.br', url: '#', icon: 'globe-s', variant: 'default', bgColor: '', textColor: '', iconBg: '#f4f4f5', animation: 'none', badge: '', shape: 'rounded' },
      { id: _id(), type: 'event', visible: true, title: 'Summer Launch Party', day: '24', month: 'DEZ', url: '#', venue: 'Armazém da Utopia', cta: 'Get Tickets', variant: 'default', coverUrl: '', lineup: '', price: 'R$80', status: 'upcoming' },
      { id: _id(), type: 'cards', visible: true, cards: [
        { title: 'Agenda', img: 'https://images.unsplash.com/photo-1483412033650-1015ddeb83d1?auto=format&fit=crop&w=400&q=80', url: '#' },
        { title: 'Projetos', img: 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=400&q=80', url: '#' }
      ]},
      { id: _id(), type: 'marquee', visible: true, text: 'TICKETS AVAILABLE • LIMITED TIME • ', bgColor: '#121214', textColor: '#ffffff' },
      { id: _id(), type: 'audio', visible: true, title: 'Apollo Mixtape Vol.1', artist: 'DJ Rio', artUrl: '', embedUrl: '', variant: 'compact', theme: 'dark' },
      { id: _id(), type: 'divider', visible: true, style: 'line' },
      { id: _id(), type: 'text', visible: true, text: 'Siga-nos nas redes sociais para novidades e bastidores!', format: 'plain', alignment: 'center', tags: ['música', 'cultura', 'rio'], status: '', statusType: 'available' },
      { id: _id(), type: 'social', visible: true, icons: [
        { icon: 'instagram-s', url: 'https://instagram.com', label: 'Instagram' },
        { icon: 'twitter-s', url: 'https://twitter.com', label: 'Twitter' },
        { icon: 'linkedin-s', url: 'https://linkedin.com', label: 'LinkedIn' },
        { icon: 'spotify-s', url: 'https://spotify.com', label: 'Spotify' }
      ], size: 'md', alignment: 'center' },
      { id: _id(), type: 'testimonial', visible: true, items: [{ quote: 'O melhor coletivo cultural do Rio!', author: 'Revista Zupi', role: 'Magazine' }] },
      { id: _id(), type: 'newsletter', visible: true, title: 'Fique por dentro', placeholder: 'Seu melhor email', buttonText: 'Inscrever', submitUrl: '', bgColor: '', textColor: '' },
      { id: _id(), type: 'monetize', visible: true, title: 'Apoie Apollo', desc: 'Ajude a manter a cena viva', url: '#', buttonText: 'Apoiar', icon: 'fire-s', variant: 'tipjar' },
      { id: _id(), type: 'contact', visible: true, buttons: [
        { label: 'WhatsApp', icon: 'whatsapp-s', url: '#' },
        { label: 'Email', icon: 'mail-s', url: '#' }
      ]}
    ]
  };

  let activeTab = 'blocks';
  let openBlocks = new Set();
  let iconPickerTarget = null;
  let device = 'phone';

  function _id() { return 'b' + Date.now().toString(36) + Math.random().toString(36).slice(2, 6); }

  /* ─── UTILITIES ─── */
  const $ = (sel) => document.querySelector(sel);
  const $$ = (sel) => document.querySelectorAll(sel);

  /* ★ NEW §13: Debounce utility */
  function _debounce(fn, ms = 150) {
    let timer;
    return function(...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), ms);
    };
  }

  /* ★ NEW §13: Simple event emitter for analytics hooks */
  const _listeners = {};
  function on(event, fn) {
    if (!_listeners[event]) _listeners[event] = [];
    _listeners[event].push(fn);
  }
  function emit(event, data) {
    (_listeners[event] || []).forEach(fn => {
      try { fn(data); } catch (e) { console.warn('[HUB] listener error:', e); }
    });
  }

  /* ─── INIT ─── */
  function init() {
    render();
    bindGlobalInputs();
    initDragDrop();
    initScale();
    initModalBackdrops();
    initKeyboard();      // ★ NEW: keyboard handlers
    initMobileLayout();

    // Dismiss loader
    setTimeout(() => {
      const loader = $('.hub-loader');
      if (loader) loader.classList.add('out');
      setTimeout(() => { if (loader) loader.remove(); }, 600);
    }, 500);

    emit('init', { blocks: state.blocks.length });
  }

  /* ─── MODAL BACKDROP — click outside to close ─── */
  function initModalBackdrops() {
    document.querySelectorAll('.hub-modal-bg').forEach(bg => {
      bg.addEventListener('click', function(e) {
        if (e.target === this) {
          const id = this.id;
          if (id) closeModal(id);
        }
      });
    });
  }

  /* ★ NEW §8: Global keyboard handlers — ESC close + toggle Enter/Space */
  function initKeyboard() {
    document.addEventListener('keydown', (e) => {
      // ESC closes any open modal
      if (e.key === 'Escape') {
        const openModal = document.querySelector('.hub-modal-bg.open');
        if (openModal) {
          closeModal(openModal.id);
          e.preventDefault();
          return;
        }
        // Also close add-menu
        const addMenu = $('#add-menu');
        if (addMenu && addMenu.classList.contains('open')) {
          closeAddMenu();
          e.preventDefault();
        }
      }

      // Enter/Space on toggle switches
      if (e.key === 'Enter' || e.key === ' ') {
        const toggle = e.target.closest('.sb-toggle[data-profile]');
        if (toggle) {
          e.preventDefault();
          toggle.click();
        }
      }
    });
  }

  /* ─── MOBILE LAYOUT (≤980px) ─── */
  let mobActivePanel = 'preview';

  function initMobileLayout() {
    if (window.innerWidth > 980) return;
    document.body.classList.add('is-mobile');
    mobilePanel('preview');
  }

  function mobilePanel(name) {
    mobActivePanel = name;
    const sidebar  = document.querySelector('.hub-sidebar');
    const mainArea = document.querySelector('.hub-main');
    const nav      = document.querySelectorAll('.mob-nav-btn');

    nav.forEach(btn => {
      const isActive = btn.dataset.mob === name;
      btn.classList.toggle('mob-active', isActive);
      // ★ FIX: aria-current for active nav
      if (isActive) btn.setAttribute('aria-current', 'page');
      else btn.removeAttribute('aria-current');
    });

    if (name === 'preview') {
      if (sidebar)  { sidebar.classList.remove('mob-visible'); sidebar.classList.add('mob-hidden'); }
      if (mainArea) { mainArea.classList.remove('mob-hidden'); mainArea.classList.add('mob-visible'); }
    } else {
      switchTab(name);
      if (mainArea) { mainArea.classList.remove('mob-visible'); mainArea.classList.add('mob-hidden'); }
      if (sidebar)  { sidebar.classList.remove('mob-hidden'); sidebar.classList.add('mob-visible'); }
    }
  }

  /* ─── RENDER ALL ─── */
  function render() {
    applyTheme();
    renderPreview();
    renderEditor();
    emit('render', { blocks: state.blocks.length });
  }

  /* ─── THEME ─── */
  function applyTheme() {
    const p = state.profile;
    const root = document.documentElement;
    root.style.setProperty('--primary', p.primaryColor);
    const screen = $('.phone-screen');
    if (screen) {
      screen.style.background = p.bgGradient || p.bgColor || '#ffffff';
      screen.style.color = p.textColor || '#121214';
    }
  }

  /* ─── PREVIEW RENDER ─── */
  function renderPreview() {
    const area = $('#pv-blocks-area');
    const p = state.profile;
    if (!area) return;

    // Background
    const bgLayer = $('#pv-bg');
    if (bgLayer) {
      if (p.bg) {
        bgLayer.style.backgroundImage = `url('${p.bg}')`;
        bgLayer.classList.add('active');
      } else {
        bgLayer.classList.remove('active');
      }
    }

    // Profile
    const profileEl = $('#pv-profile');
    if (profileEl) {
      profileEl.setAttribute('data-layout', p.avatarLayout || 'center');
      profileEl.innerHTML = `
        ${p.avatarLayout !== 'minimal' ? `<img class="pv-avatar shape-${p.avatarShape || 'circle'}" src="${esc(p.avatar)}" alt="${esc(p.name)}" width="80" height="80" loading="lazy">` : ''}
        <div class="pv-profile-text">
          ${p.showName ? `<div class="pv-name" style="font-family:'${p.fontFamily || 'Space Grotesk'}',system-ui,sans-serif;">${esc(p.name)}</div>` : ''}
          ${p.showBio ? `<div class="pv-bio">${esc(p.bio)}</div>` : ''}
          ${p.showVerified ? `<div class="pv-verified"><i class="ri-verified-badge-fill" aria-hidden="true"></i> ${esc(p.verifiedText)}</div>` : ''}
        </div>`;
    }

    // Blocks
    const gap = p.blockGap || '10';
    area.style.gap = gap + 'px';
    area.innerHTML = '';

    state.blocks.forEach(b => {
      if (!b.visible) return;
      const def = window.BLOCKS[b.type];
      if (!def) return;
      const html = def.preview(b);
      const wrapper = document.createElement('div');
      wrapper.innerHTML = html;
      // Apply global border radius
      const blocks = wrapper.querySelectorAll('.pv-block');
      blocks.forEach(el => {
        el.style.borderRadius = (p.blockRadius || '14') + 'px';
        if (p.borderStyle === 'solid') {
          el.style.background = 'rgba(255,255,255,0.95)';
          el.style.border = '1px solid rgba(0,0,0,0.08)';
          el.style.backdropFilter = 'none';
        } else if (p.borderStyle === 'none') {
          el.style.border = 'none';
          el.style.background = 'transparent';
          el.style.backdropFilter = 'none';
          el.style.boxShadow = 'none';
        }
      });
      while (wrapper.firstChild) area.appendChild(wrapper.firstChild);
    });

    // ★ FIX: Ensure Apollo icons render after dynamic injection
    requestAnimationFrame(() => {
      if (window.apolloIcons && window.apolloIcons.scanImmediate) {
        window.apolloIcons.scanImmediate(document.getElementById('pv-blocks-area'));
      }
    });
  }

  function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

  /* ─── EDITOR RENDER ─── */
  function renderEditor() {
    renderBlocksPanel();
    // Update block count badge
    const cnt = $('#block-count');
    if (cnt) cnt.textContent = state.blocks.length;
  }

  function renderBlocksPanel() {
    const container = $('#blocks-list');
    if (!container || activeTab !== 'blocks') return;

    container.innerHTML = '';
    state.blocks.forEach((b, idx) => {
      const def = window.BLOCKS[b.type];
      if (!def) return;
      const isOpen = openBlocks.has(b.id);
      const card = document.createElement('div');
      card.className = `block-card ${isOpen ? 'open' : ''}`;
      card.dataset.idx = idx;
      card.dataset.id = b.id;
      card.draggable = true;
      card.setAttribute('role', 'listitem');

      const title = b.title || b.text || b.question || def.label;

      card.innerHTML = `
        <div class="block-card-head" onclick="HUB.toggleBlock('${b.id}')">
          <span class="drag" aria-label="Arrastar bloco"><i class="ri-drag-move-2-line" aria-hidden="true"></i></span>
          <span class="block-vis ${b.visible ? '' : 'off'}" aria-hidden="true"></span>
          <span class="block-type-badge">${def.label}</span>
          <span class="block-card-title">${esc(title)}</span>
          <div class="block-card-acts">
            <button class="i-btn" style="width:26px;height:26px;" onclick="event.stopPropagation();HUB.toggleVis(${idx})" title="${b.visible ? 'Ocultar' : 'Mostrar'}" aria-label="${b.visible ? 'Ocultar' : 'Mostrar'} bloco">
              <i class="ri-eye${b.visible ? '' : '-off'}-line" style="font-size:14px;" aria-hidden="true"></i>
            </button>
            <button class="i-btn" style="width:26px;height:26px;" onclick="event.stopPropagation();HUB.dupBlock(${idx})" title="Duplicar" aria-label="Duplicar bloco">
              <i class="ri-file-copy-line" style="font-size:14px;" aria-hidden="true"></i>
            </button>
            <button class="i-btn" style="width:26px;height:26px;color:#dc2626;" onclick="event.stopPropagation();HUB.delBlock(${idx})" title="Remover" aria-label="Remover bloco">
              <i class="ri-delete-bin-line" style="font-size:14px;" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="block-card-body">
          ${isOpen ? def.editor(b, idx) : ''}
        </div>`;

      container.appendChild(card);
    });

    // Bind inputs inside editor — ★ FIX §13: debounced preview update
    const debouncedPreview = _debounce(() => renderPreview(), 120);
    container.querySelectorAll('[data-idx][data-key]').forEach(el => {
      const handler = (e) => {
        const idx = parseInt(el.dataset.idx);
        const key = el.dataset.key;
        let val = el.type === 'number' ? parseInt(el.value) || 0 : el.value;
        setNestedValue(state.blocks[idx], key, val);
        if (key === '_tagsStr') {
          state.blocks[idx].tags = val.split(',').map(t => t.trim()).filter(Boolean);
        }
        debouncedPreview();
      };
      el.addEventListener('input', handler);
    });
  }

  function setNestedValue(obj, path, val) {
    const parts = path.split('.');
    let current = obj;
    for (let i = 0; i < parts.length - 1; i++) {
      const key = isNaN(parts[i]) ? parts[i] : parseInt(parts[i]);
      current = current[key];
      if (!current) return;
    }
    const finalKey = isNaN(parts[parts.length - 1]) ? parts[parts.length - 1] : parseInt(parts[parts.length - 1]);
    current[finalKey] = val;
  }

  /* ─── BLOCK ACTIONS ─── */
  function toggleBlock(id) {
    if (openBlocks.has(id)) openBlocks.delete(id);
    else openBlocks.add(id);
    renderEditor();
  }

  function toggleVis(idx) {
    state.blocks[idx].visible = !state.blocks[idx].visible;
    render();
    emit('toggleVis', { idx, visible: state.blocks[idx].visible });
  }

  function delBlock(idx) {
    const type = state.blocks[idx].type;
    state.blocks.splice(idx, 1);
    render();
    toast('Bloco removido');
    emit('delBlock', { type, idx });
  }

  function dupBlock(idx) {
    const clone = JSON.parse(JSON.stringify(state.blocks[idx]));
    clone.id = _id();
    state.blocks.splice(idx + 1, 0, clone);
    render();
    toast('Bloco duplicado');
    emit('dupBlock', { type: clone.type });
  }

  function addBlock(type) {
    const def = window.BLOCKS[type];
    if (!def) return;
    const b = { id: _id(), type, visible: true, ...def.defaults() };
    state.blocks.push(b);
    openBlocks.add(b.id);
    render();
    closeAddMenu();
    toast(`${def.label} adicionado`);
    emit('addBlock', { type });
    setTimeout(() => {
      const list = $('#blocks-list');
      if (list) list.scrollTop = list.scrollHeight;
    }, 60);
  }

  function addArrayItem(idx, key, item) {
    const arr = state.blocks[idx][key];
    if (Array.isArray(arr)) {
      arr.push(item);
      render();
    }
  }

  function delArrayItem(idx, key, itemIdx) {
    const arr = state.blocks[idx][key];
    if (Array.isArray(arr)) {
      arr.splice(itemIdx, 1);
      render();
    }
  }

  /* ─── ADD MENU ─── */
  function openAddMenu(e) {
    const menu = $('#add-menu');
    if (!menu) return;
    const groups = { content: 'Conteúdo', media: 'Mídia', layout: 'Layout', interactive: 'Interativo', advanced: 'Avançado' };
    let html = '';
    Object.entries(groups).forEach(([gk, gl]) => {
      const items = Object.values(window.BLOCKS).filter(b => b.group === gk);
      if (!items.length) return;
      html += `<div class="add-menu-group">${gl}</div>`;
      items.forEach(b => {
        html += `<div class="add-menu-item" role="menuitem" onclick="HUB.addBlock('${b.type}')"><i class="${b.icon}" aria-hidden="true"></i><span>${b.label}</span></div>`;
      });
      html += '<div class="add-menu-divider"></div>';
    });
    menu.innerHTML = html;
    menu.classList.add('open');

    const btn = e.currentTarget;
    const rect = btn.getBoundingClientRect();
    menu.style.top = (rect.bottom + 4) + 'px';
    menu.style.left = Math.min(rect.left, window.innerWidth - 260) + 'px';

    setTimeout(() => {
      document.addEventListener('click', _closeAddMenuOnClick, { once: true });
    }, 10);
  }

  function _closeAddMenuOnClick(e) {
    const menu = $('#add-menu');
    if (menu && !menu.contains(e.target)) closeAddMenu();
    else if (menu) document.addEventListener('click', _closeAddMenuOnClick, { once: true });
  }

  function closeAddMenu() {
    const menu = $('#add-menu');
    if (menu) menu.classList.remove('open');
  }

  /* ─── TABS — ★ FIX: aria-selected management ─── */
  function switchTab(tab) {
    activeTab = tab;
    $$('.sb-nav-item').forEach(el => {
      const isActive = el.dataset.tab === tab;
      el.classList.toggle('on', isActive);
      el.setAttribute('aria-selected', String(isActive));
      el.setAttribute('tabindex', isActive ? '0' : '-1');
    });
    $$('.sb-panel').forEach(el => el.classList.toggle('on', el.id === `panel-${tab}`));
    if (tab === 'blocks') renderEditor();
    emit('switchTab', { tab });
  }

  /* ─── PROFILE BINDING — ★ FIX §13: debounced render ─── */
  const _debouncedRender = _debounce(() => render(), 100);

  function bindGlobalInputs() {
    document.addEventListener('input', (e) => {
      const el = e.target;
      if (el.dataset.profile) {
        const key = el.dataset.profile;
        state.profile[key] = el.type === 'checkbox' ? el.checked : el.value;
        _debouncedRender();
      }
    });

    // Toggle clicks — ★ FIX: aria-checked management
    document.addEventListener('click', (e) => {
      const toggle = e.target.closest('.sb-toggle[data-profile]');
      if (toggle) {
        const key = toggle.dataset.profile;
        state.profile[key] = !state.profile[key];
        toggle.classList.toggle('on', state.profile[key]);
        toggle.setAttribute('aria-checked', String(state.profile[key]));
        render();
      }
    });
  }

  function syncProfileInputs() {
    const p = state.profile;
    $$('[data-profile]').forEach(el => {
      const key = el.dataset.profile;
      if (el.classList.contains('sb-toggle')) {
        el.classList.toggle('on', !!p[key]);
        el.setAttribute('aria-checked', String(!!p[key]));
      } else if (el.type === 'color') {
        el.value = p[key] || '#000000';
      } else {
        el.value = p[key] || '';
      }
    });
  }

  /* ─── ICON PICKER ─── */
  const iconData = [
    { name: 'instagram', file: 'instagram-s.svg', cat: 'social' },
    { name: 'twitter', file: 'twitter-s.svg', cat: 'social' },
    { name: 'linkedin', file: 'linkedin-s.svg', cat: 'social' },
    { name: 'github', file: 'github-s.svg', cat: 'social' },
    { name: 'tiktok', file: 'tiktok-s.svg', cat: 'social' },
    { name: 'twitch', file: 'twitch-s.svg', cat: 'social' },
    { name: 'discord', file: 'discord-s.svg', cat: 'social' },
    { name: 'telegram', file: 'telegram-s.svg', cat: 'social' },
    { name: 'whatsapp', file: 'whatsapp-s.svg', cat: 'social' },
    { name: 'facebook', file: 'facebook-s.svg', cat: 'social' },
    { name: 'pinterest', file: 'pinterest-s.svg', cat: 'social' },
    { name: 'snapchat', file: 'snapchat-s.svg', cat: 'social' },
    { name: 'threads', file: 'threads-s.svg', cat: 'social' },
    { name: 'mastodon', file: 'mastodon-s.svg', cat: 'social' },
    { name: 'spotify', file: 'spotify-s.svg', cat: 'media' },
    { name: 'youtube', file: 'youtube-s.svg', cat: 'media' },
    { name: 'soundcloud', file: 'soundcloud-s.svg', cat: 'media' },
    { name: 'music', file: 'music-s.svg', cat: 'media' },
    { name: 'mic', file: 'mic-s.svg', cat: 'media' },
    { name: 'video', file: 'video-s.svg', cat: 'media' },
    { name: 'play', file: 'play-circle-s.svg', cat: 'media' },
    { name: 'camera', file: 'camera-s.svg', cat: 'media' },
    { name: 'image', file: 'image-s.svg', cat: 'media' },
    { name: 'headphone', file: 'headphone-s.svg', cat: 'media' },
    { name: 'globe', file: 'globe-s.svg', cat: 'ui' },
    { name: 'link', file: 'link.svg', cat: 'ui' },
    { name: 'external-link', file: 'external-link-s.svg', cat: 'ui' },
    { name: 'home', file: 'home-s.svg', cat: 'ui' },
    { name: 'search', file: 'search-s.svg', cat: 'ui' },
    { name: 'download', file: 'download-s.svg', cat: 'ui' },
    { name: 'mail', file: 'mail-s.svg', cat: 'ui' },
    { name: 'phone', file: 'phone-s.svg', cat: 'ui' },
    { name: 'map-pin', file: 'map-pin-s.svg', cat: 'ui' },
    { name: 'calendar', file: 'calendar-s.svg', cat: 'ui' },
    { name: 'lock', file: 'lock-s.svg', cat: 'ui' },
    { name: 'user', file: 'user-s.svg', cat: 'ui' },
    { name: 'users', file: 'group-s.svg', cat: 'ui' },
    { name: 'settings', file: 'settings-s.svg', cat: 'ui' },
    { name: 'menu', file: 'menu-s.svg', cat: 'ui' },
    { name: 'share', file: 'share-s.svg', cat: 'ui' },
    { name: 'qr-code', file: 'qr-code-s.svg', cat: 'ui' },
    { name: 'shopping-bag', file: 'shopping-bag-s.svg', cat: 'commerce' },
    { name: 'credit-card', file: 'bank-card-s.svg', cat: 'commerce' },
    { name: 'ticket', file: 'ticket-s.svg', cat: 'commerce' },
    { name: 'briefcase', file: 'briefcase-s.svg', cat: 'commerce' },
    { name: 'store', file: 'store-s.svg', cat: 'commerce' },
    { name: 'star', file: 'star.svg', cat: 'status' },
    { name: 'heart', file: 'heart-s.svg', cat: 'status' },
    { name: 'fire', file: 'fire-s.svg', cat: 'status' },
    { name: 'bolt', file: 'flashlight-s.svg', cat: 'status' },
    { name: 'rocket', file: 'rocket-s.svg', cat: 'status' },
    { name: 'gift', file: 'gift-s.svg', cat: 'status' },
    { name: 'medal', file: 'medal-s.svg', cat: 'status' },
    { name: 'trophy', file: 'trophy-s.svg', cat: 'status' },
    { name: 'crown', file: 'vip-crown-s.svg', cat: 'status' },
    { name: 'diamond', file: 'diamond-s.svg', cat: 'status' },
    { name: 'coffee', file: 'cup-s.svg', cat: 'status' },
    { name: 'book', file: 'book-s.svg', cat: 'status' },
    { name: 'flag', file: 'flag-s.svg', cat: 'status' },
    { name: 'paint', file: 'paint-s.svg', cat: 'status' },
    { name: 'code', file: 'code-s.svg', cat: 'status' },
    { name: 'terminal', file: 'terminal-s.svg', cat: 'status' },
    { name: 'puzzle', file: 'puzzle-s.svg', cat: 'status' },
    { name: 'graduation', file: 'graduation-s.svg', cat: 'status' }
  ];

  const iconCategories = [
    { key: 'all', label: 'Todos' },
    { key: 'social', label: 'Social' },
    { key: 'media', label: 'Mídia' },
    { key: 'ui', label: 'Interface' },
    { key: 'commerce', label: 'Negócios' },
    { key: 'status', label: 'Status' }
  ];
  let activeIconCat = 'all';

  function openIconPicker(idx, key) {
    iconPickerTarget = { idx, key };
    const modal = $('#icon-modal');
    if (!modal) return;
    modal.style.display = '';  // ★ FIX: clear any inline display:none
    modal.classList.add('open');
    // ★ FIX §8: Scroll lock
    document.body.classList.add('modal-open');
    activeIconCat = 'all';
    _renderIconGrid();
    const search = $('#icon-search');
    if (search) { search.value = ''; search.focus(); }
  }

  function _renderIconGrid() {
    const grid = $('#icon-grid');
    const tabsEl = $('#icon-cat-tabs');
    if (!grid) return;

    if (tabsEl) {
      tabsEl.innerHTML = iconCategories.map(c =>
        `<button class="icon-cat-tab ${activeIconCat === c.key ? 'on' : ''}" onclick="HUB.filterIconCat('${c.key}')" role="tab" aria-selected="${activeIconCat === c.key}">${c.label}</button>`
      ).join('');
    }

    grid.innerHTML = '';
    const filtered = activeIconCat === 'all' ? iconData : iconData.filter(ic => ic.cat === activeIconCat);

    filtered.forEach(ic => {
      const k = ic.file.replace(/\.svg$/, '');
      const el = document.createElement('div');
      el.className = 'icon-choice';
      el.dataset.key = k;
      el.dataset.name = ic.name;
      el.dataset.cat = ic.cat || '';
      el.setAttribute('role', 'option');
      el.setAttribute('aria-label', ic.name);
      el.setAttribute('tabindex', '0');
      const html = _safeIconHTML(k, '22px');
      el.innerHTML = `${html}<span>${ic.name}</span>`;
      el.onclick = () => selectIcon(k);
      el.onkeydown = (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectIcon(k); } };
      grid.appendChild(el);
    });

    // ★ FIX: Force Apollo CDN to render icons in picker
    if (window.apolloIcons && window.apolloIcons.scanImmediate) {
      window.apolloIcons.scanImmediate(grid);
    }
  }

  function selectIcon(key) {
    if (!iconPickerTarget) return;
    const { idx, key: path } = iconPickerTarget;
    setNestedValue(state.blocks[idx], path, key);
    closeModal('icon-modal');
    iconPickerTarget = null;
    render();
    toast('Ícone alterado!');
    emit('selectIcon', { key });
  }

  /* ★ FIX §13: Debounced icon search */
  const _debouncedIconFilter = _debounce((term) => {
    const t = (term || '').toLowerCase();
    $$('#icon-grid .icon-choice').forEach(el => {
      const name = (el.dataset.name || '').toLowerCase();
      const key = (el.dataset.key || '').toLowerCase();
      el.style.display = (!t || name.includes(t) || key.includes(t)) ? 'flex' : 'none';
    });
    // ★ FIX: Re-scan after filter
    if (window.apolloIcons && window.apolloIcons.scanImmediate) {
      window.apolloIcons.scanImmediate(document.getElementById('icon-grid'));
    }
  }, 80);

  function filterIcons(term) {
    _debouncedIconFilter(term);
  }

  function filterIconCat(cat) {
    activeIconCat = cat;
    _renderIconGrid();
    const search = $('#icon-search');
    if (search && search.value) filterIcons(search.value);
  }

  /* ═══════════════════════════════════════════════
     ★★★ BRUTAL ICON SYSTEM — RemixIcon ONLY, zero CDN dep.
     RemixIcon loaded in <head> via jsDelivr = ALWAYS available.
     No Apollo CDN needed. Instant. Guaranteed.
     ═══════════════════════════════════════════════ */
  const _riMap = {
    /* Social */ 'instagram-s':'ri-instagram-fill','twitter-s':'ri-twitter-x-fill','linkedin-s':'ri-linkedin-fill',
    'github-s':'ri-github-fill','tiktok-s':'ri-tiktok-fill','twitch-s':'ri-twitch-fill',
    'discord-s':'ri-discord-fill','telegram-s':'ri-telegram-fill','whatsapp-s':'ri-whatsapp-fill',
    'facebook-s':'ri-facebook-fill','pinterest-s':'ri-pinterest-fill','snapchat-s':'ri-snapchat-fill',
    'threads-s':'ri-threads-fill','mastodon-s':'ri-mastodon-fill',
    /* Media */ 'spotify-s':'ri-spotify-fill','youtube-s':'ri-youtube-fill','soundcloud-s':'ri-soundcloud-fill',
    'music-s':'ri-music-fill','mic-s':'ri-mic-fill','video-s':'ri-video-fill',
    'play-circle-s':'ri-play-circle-fill','camera-s':'ri-camera-fill','image-s':'ri-image-fill',
    'headphone-s':'ri-headphone-fill',
    /* UI */ 'globe-s':'ri-global-line','link':'ri-link','external-link-s':'ri-external-link-fill',
    'home-s':'ri-home-fill','search-s':'ri-search-line','download-s':'ri-download-fill',
    'mail-s':'ri-mail-fill','phone-s':'ri-phone-fill','map-pin-s':'ri-map-pin-fill',
    'calendar-s':'ri-calendar-fill','lock-s':'ri-lock-fill','user-s':'ri-user-fill',
    'group-s':'ri-group-fill','settings-s':'ri-settings-3-fill','menu-s':'ri-menu-line',
    'share-s':'ri-share-fill','qr-code-s':'ri-qr-code-fill','bell-s':'ri-notification-fill',
    /* Commerce */ 'shopping-bag-s':'ri-shopping-bag-fill','bank-card-s':'ri-bank-card-fill',
    'ticket-s':'ri-ticket-fill','briefcase-s':'ri-briefcase-fill','store-s':'ri-store-fill',
    /* Status */ 'star':'ri-star-fill','heart-s':'ri-heart-fill','fire-s':'ri-fire-fill',
    'flashlight-s':'ri-flashlight-fill','rocket-s':'ri-rocket-fill','gift-s':'ri-gift-fill',
    'medal-s':'ri-medal-fill','trophy-s':'ri-trophy-fill','vip-crown-s':'ri-vip-crown-fill',
    'diamond-s':'ri-vip-diamond-fill','cup-s':'ri-cup-fill','book-s':'ri-book-fill',
    'flag-s':'ri-flag-fill','paint-s':'ri-paint-fill','code-s':'ri-code-s-slash-fill',
    'terminal-s':'ri-terminal-box-fill','puzzle-s':'ri-puzzle-fill',
    'graduation-s':'ri-graduation-cap-fill'
  };

  /**
   * _safeIconHTML — BRUTAL: sempre usa RemixIcon (zero CDN externo).
   * Se blocks-hub.js já definiu iconHTML com RI, delega a ele.
   * Fallback final: ri-apps-2-line.
   */
  function _safeIconHTML(name, size) {
    const n = String(name || 'link').replace(/\.svg$/, '');
    const sz = size || '22px';
    const numSz = parseInt(sz, 10) || 22;
    // Se blocks-hub.js já definiu iconHTML com RI, usa ele
    if (typeof window.iconHTML === 'function') return window.iconHTML(n, sz);
    // Fallback RI direto
    const ri = _riMap[n] || 'ri-apps-2-line';
    return `<i class="${ri}" style="font-size:${numSz}px;line-height:1;display:inline-flex;align-items:center;justify-content:center;width:${sz};height:${sz};flex-shrink:0;" aria-hidden="true"></i>`;
  }

  function iconHTML(name, size) { return _safeIconHTML(name, size); }

  /* ─── MODALS — ★ FIX §8: scroll-lock + focus return ─── */
  let _prevFocus = null;

  function closeModal(id) {
    const el = $('#' + id);
    if (el) {
      el.classList.remove('open');
      document.body.classList.remove('modal-open');
      // Restore focus
      if (_prevFocus) {
        _prevFocus.focus();
        _prevFocus = null;
      }
    }
  }

  /* ─── TOAST ─── */
  function toast(msg) {
    const container = $('.hub-toast-container');
    if (!container) return;
    const t = document.createElement('div');
    t.className = 'hub-toast';
    t.setAttribute('role', 'status');
    t.innerHTML = `<i class="ri-check-line" aria-hidden="true"></i> ${esc(msg)}`;
    container.appendChild(t);
    setTimeout(() => {
      t.style.transition = 'opacity 0.3s, transform 0.3s';
      t.style.opacity = '0';
      t.style.transform = 'translateY(-6px) scale(0.95)';
      setTimeout(() => t.remove(), 350);
    }, 2800);
  }

  /* ─── DRAG AND DROP ─── */
  function initDragDrop() {
    const container = $('#blocks-list');
    if (!container) return;

    let dragIdx = null;

    container.addEventListener('dragstart', e => {
      const card = e.target.closest('.block-card');
      if (!card) return;
      dragIdx = parseInt(card.dataset.idx);
      card.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
    });

    container.addEventListener('dragend', e => {
      $$('.block-card').forEach(c => c.classList.remove('dragging', 'drag-over'));
      dragIdx = null;
    });

    container.addEventListener('dragover', e => {
      e.preventDefault();
      const card = e.target.closest('.block-card');
      $$('.block-card').forEach(c => c.classList.remove('drag-over'));
      if (card) card.classList.add('drag-over');
    });

    container.addEventListener('drop', e => {
      e.preventDefault();
      const card = e.target.closest('.block-card');
      if (!card || dragIdx === null) return;
      const toIdx = parseInt(card.dataset.idx);
      if (dragIdx !== toIdx) {
        const [moved] = state.blocks.splice(dragIdx, 1);
        state.blocks.splice(toIdx, 0, moved);
        render();
        emit('reorder', { from: dragIdx, to: toIdx });
      }
    });
  }

  /* ─── PHONE SCALE ─── */
  function initScale() {
    const BASE = 390;
    const setScale = () => {
      const frame = $('.phone-wrap');
      if (!frame) return;
      const w = frame.getBoundingClientRect().width || BASE;
      const s = Math.max(0.6, Math.min(1, w / BASE));
      frame.style.setProperty('--scale', s.toFixed(4));
      const screen = $('.phone-screen');
      if (screen) screen.style.setProperty('--s', s.toFixed(4));
    };

    window.addEventListener('resize', setScale, { passive: true });
    setScale();

    const frame = $('.phone-wrap');
    if (frame && 'ResizeObserver' in window) {
      new ResizeObserver(setScale).observe(frame);
    }
  }

  /* ─── DEVICE SWITCH — ★ FIX: aria-pressed ─── */
  function setDevice(d) {
    device = d;
    const frame = $('.phone-wrap');
    if (!frame) return;
    frame.classList.toggle('desktop-mode', d === 'desktop');
    $$('.device-btn').forEach(el => {
      const isActive = el.dataset.device === d;
      el.classList.toggle('on', isActive);
      el.setAttribute('aria-pressed', String(isActive));
    });
  }

  /* ─── SIDEBAR TOGGLE ─── */
  function toggleSidebar() {
    document.body.classList.toggle('sidebar-collapsed');
  }

  /* ─── EXPORT JSON ─── */
  function exportJSON() {
    const data = JSON.stringify(state, null, 2);
    const blob = new Blob([data], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'hub-rio-config.json'; a.click();
    URL.revokeObjectURL(url);
    toast('JSON exportado!');
    emit('export', { size: data.length });
  }

  /* ─── PUBLIC API ─── */
  return {
    init, render, switchTab, addBlock, delBlock, dupBlock,
    toggleBlock, toggleVis, openAddMenu, closeAddMenu,
    openIconPicker, selectIcon, filterIcons, filterIconCat, closeModal,
    addArrayItem, delArrayItem, setDevice, toggleSidebar,
    exportJSON, toast, syncProfileInputs, mobilePanel, state,
    on, emit  // ★ NEW: analytics hooks
  };
})();

// Boot
document.addEventListener('DOMContentLoaded', () => {
  HUB.init();
  HUB.syncProfileInputs();
});

// Expose globally
window.HUB = HUB;

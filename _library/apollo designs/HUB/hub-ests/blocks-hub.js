/* ═══════════════════════════════════════════════════
   HUB::rio — Block Registry (window.BLOCKS)
   Every block type used by hub.js is defined here.
   Loaded BEFORE hub.js.
   
   Each block provides:
     type      — unique key
     label     — display name
     icon      — RemixIcon class for add-menu
     group     — content | media | layout | interactive | advanced
     defaults()   — returns fresh default data
     preview(b)   — returns HTML string for phone preview
     editor(b,i)  — returns HTML string for sidebar editor
   ═══════════════════════════════════════════════════ */
'use strict';

/* ══════════════════════════════════════════════════════════════
   ★★★ BRUTAL ICON SYSTEM — RemixIcon ONLY, zero CDN dependency
   RemixIcon is loaded in <head> via jsDelivr, ALWAYS available.
   No Apollo CDN SVG masks needed. Instant. Guaranteed.
   ══════════════════════════════════════════════════════════════ */
const _RI_MAP = {
  /* Social & Messaging */
  'instagram-s':'ri-instagram-fill','twitter-s':'ri-twitter-x-fill','linkedin-s':'ri-linkedin-fill',
  'github-s':'ri-github-fill','tiktok-s':'ri-tiktok-fill','twitch-s':'ri-twitch-fill',
  'discord-s':'ri-discord-fill','telegram-s':'ri-telegram-fill','whatsapp-s':'ri-whatsapp-fill',
  'facebook-s':'ri-facebook-fill','pinterest-s':'ri-pinterest-fill','snapchat-s':'ri-snapchat-fill',
  'threads-s':'ri-threads-fill','mastodon-s':'ri-mastodon-fill',
  /* Media & Music */
  'spotify-s':'ri-spotify-fill','youtube-s':'ri-youtube-fill','soundcloud-s':'ri-soundcloud-fill',
  'music-s':'ri-music-fill','mic-s':'ri-mic-fill','video-s':'ri-video-fill',
  'play-circle-s':'ri-play-circle-fill','camera-s':'ri-camera-fill','image-s':'ri-image-fill',
  'headphone-s':'ri-headphone-fill',
  /* Navigation & UI */
  'globe-s':'ri-global-line','link':'ri-link','external-link-s':'ri-external-link-fill',
  'home-s':'ri-home-fill','search-s':'ri-search-line','download-s':'ri-download-fill',
  'mail-s':'ri-mail-fill','phone-s':'ri-phone-fill','map-pin-s':'ri-map-pin-fill',
  'calendar-s':'ri-calendar-fill','lock-s':'ri-lock-fill','user-s':'ri-user-fill',
  'group-s':'ri-group-fill','settings-s':'ri-settings-3-fill','menu-s':'ri-menu-line',
  'share-s':'ri-share-fill','qr-code-s':'ri-qr-code-fill','arrow-right-s':'ri-arrow-right-s-line',
  'close-s':'ri-close-line','check-s':'ri-check-line','info-s':'ri-information-fill',
  'warning-s':'ri-error-warning-fill','bell-s':'ri-notification-fill',
  /* Commerce & Business */
  'shopping-bag-s':'ri-shopping-bag-fill','bank-card-s':'ri-bank-card-fill',
  'ticket-s':'ri-ticket-fill','briefcase-s':'ri-briefcase-fill','store-s':'ri-store-fill',
  /* Status & Fun */
  'star':'ri-star-fill','heart-s':'ri-heart-fill','fire-s':'ri-fire-fill',
  'flashlight-s':'ri-flashlight-fill','rocket-s':'ri-rocket-fill','gift-s':'ri-gift-fill',
  'medal-s':'ri-medal-fill','trophy-s':'ri-trophy-fill','vip-crown-s':'ri-vip-crown-fill',
  'diamond-s':'ri-vip-diamond-fill','cup-s':'ri-cup-fill','book-s':'ri-book-fill',
  'flag-s':'ri-flag-fill','paint-s':'ri-paint-fill','code-s':'ri-code-s-slash-fill',
  'terminal-s':'ri-terminal-box-fill','puzzle-s':'ri-puzzle-fill',
  'graduation-s':'ri-graduation-cap-fill',
  /* Extra handy ones */
  'music':'ri-music-line','calendar':'ri-calendar-line','mic':'ri-mic-line',
  'link-s':'ri-link','send-s':'ri-send-plane-fill','chat-s':'ri-chat-1-fill',
  'location-s':'ri-map-pin-fill','clock-s':'ri-time-line','tag-s':'ri-price-tag-fill',
  'eye-s':'ri-eye-fill','edit-s':'ri-pencil-fill','add-s':'ri-add-circle-fill',
};

/**
 * window.iconHTML — BRUTAL: sempre usa RemixIcon (zero CDN externo).
 * Fallback final: ri-apps-2-line (ícone grade).
 */
window.iconHTML = function(name, size) {
  var n = String(name || 'link').replace(/\.svg$/, '');
  var sz = size || '22px';
  var numSz = parseInt(sz, 10) || 22;
  var ri = _RI_MAP[n] || 'ri-apps-2-line';
  return '<i class="' + ri + '" style="font-size:' + numSz + 'px;line-height:1;display:inline-flex;align-items:center;justify-content:center;width:' + sz + ';height:' + sz + ';flex-shrink:0;" aria-hidden="true"></i>';
};

/* ─── Escape helper ─── */
function _esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

/* ─── Editor field helpers ─── */
function _input(idx, key, val, placeholder, label, type) {
  type = type || 'text';
  return `
    <div class="sb-field">
      ${label ? `<label class="sb-label">${label}</label>` : ''}
      <input class="sb-input" type="${type}" data-idx="${idx}" data-key="${key}" value="${_esc(val || '')}" placeholder="${_esc(placeholder || '')}">
    </div>`;
}

function _textarea(idx, key, val, placeholder, label) {
  return `
    <div class="sb-field">
      ${label ? `<label class="sb-label">${label}</label>` : ''}
      <textarea class="sb-textarea" data-idx="${idx}" data-key="${key}" placeholder="${_esc(placeholder || '')}">${_esc(val || '')}</textarea>
    </div>`;
}

function _select(idx, key, val, options, label) {
  const opts = options.map(o => {
    const k = typeof o === 'string' ? o : o.value;
    const l = typeof o === 'string' ? o : o.label;
    return `<option value="${k}" ${val === k ? 'selected' : ''}>${l}</option>`;
  }).join('');
  return `
    <div class="sb-field">
      ${label ? `<label class="sb-label">${label}</label>` : ''}
      <select class="sb-select" data-idx="${idx}" data-key="${key}">${opts}</select>
    </div>`;
}

function _iconBtn(idx, key, currentIcon) {
  var ic = currentIcon ? window.iconHTML(currentIcon, '18px') : '<i class="ri-apps-2-line" style="font-size:18px;opacity:0.4;" aria-hidden="true"></i>';
  var label = currentIcon ? currentIcon.replace(/-s$/, '') : 'escolher';
  return `
    <button class="sb-btn sb-btn-ghost" onclick="HUB.openIconPicker(${idx},'${key}')" style="gap:8px;min-height:34px;" aria-label="Escolher ícone">
      <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:6px;background:var(--sf,rgba(0,0,0,0.05));">${ic}</span>
      <span style="font-size:10px;font-family:var(--mono,monospace);opacity:0.7;">${_esc(label)}</span>
      <i class="ri-arrow-down-s-line" style="font-size:12px;opacity:0.4;margin-left:auto;" aria-hidden="true"></i>
    </button>`;
}


/* ═══════════════════════════════════════════════════
   BLOCK REGISTRY
   ═══════════════════════════════════════════════════ */
window.BLOCKS = {};

/* ── 1. HEADER / SECTION TITLE ── */
window.BLOCKS.header = {
  type: 'header', label: 'Título', icon: 'ri-heading', group: 'layout',
  defaults: () => ({ text: 'Seção', icon: '' }),
  preview: (b) => {
    var ic = b.icon ? `<span style="margin-right:6px;opacity:0.7;">${window.iconHTML(b.icon,'16px')}</span>` : '';
    return `<div class="pv-block pv-section-header" style="display:flex;align-items:center;gap:4px;">${ic}${_esc(b.text)}</div>`;
  },
  editor: (b, i) => `
    ${_input(i, 'text', b.text, 'Título da seção', 'Texto')}
    <div class="sb-field">
      <label class="sb-label"><i class="ri-apps-2-line" aria-hidden="true"></i> Ícone (opcional)</label>
      ${_iconBtn(i, 'icon', b.icon)}
    </div>`
};

/* ── 2. LINK / CTA ── */
window.BLOCKS.link = {
  type: 'link', label: 'Link / CTA', icon: 'ri-link', group: 'content',
  defaults: () => ({ title: 'Novo Link', sub: '', url: '#', icon: 'globe-s', variant: 'default', bgColor: '', textColor: '', iconBg: '#f4f4f5', animation: 'none', badge: '', shape: 'rounded' }),
  preview: (b) => {
    const ic = window.iconHTML(b.icon || 'globe-s', '20px');
    const varCls = b.variant && b.variant !== 'default' ? ` variant-${b.variant}` : '';
    const badgeHTML = b.badge ? `<span style="font-family:var(--mono,'Space Mono',monospace);font-size:0.55rem;font-weight:700;text-transform:uppercase;background:var(--primary-l,rgba(244,95,0,0.07));color:var(--primary,#f45f00);padding:2px 6px;border-radius:100px;margin-left:auto;flex-shrink:0;">${_esc(b.badge)}</span>` : '';
    return `
      <a class="pv-block" href="${_esc(b.url)}" target="_blank" rel="noopener" style="${b.bgColor ? 'background:' + b.bgColor + ';' : ''}${b.textColor ? 'color:' + b.textColor + ';' : ''}">
        <div class="pv-link-inner${varCls}">
          <div class="pv-link-icon" style="${b.iconBg ? 'background:' + b.iconBg : ''}">${ic}</div>
          <div class="pv-link-text">
            <div class="pv-link-title">${_esc(b.title)}</div>
            ${b.sub ? `<div class="pv-link-sub">${_esc(b.sub)}</div>` : ''}
          </div>
          ${badgeHTML}
          <span class="pv-link-arrow"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
        </div>
      </a>`;
  },
  editor: (b, i) => `
    ${_input(i, 'title', b.title, 'Título do link', 'Título')}
    ${_input(i, 'sub', b.sub, 'Subtítulo', 'Subtítulo')}
    ${_input(i, 'url', b.url, 'https://...', 'URL')}
    <div class="sb-field">
      <label class="sb-label">Ícone</label>
      ${_iconBtn(i, 'icon', b.icon)}
    </div>
    ${_select(i, 'variant', b.variant, [
      { value: 'default', label: 'Padrão' },
      { value: 'grid', label: 'Grid' },
      { value: 'minimal', label: 'Minimal' }
    ], 'Variante')}
    <div class="sb-cols">
      ${_input(i, 'bgColor', b.bgColor, '#hex', 'Fundo')}
      ${_input(i, 'textColor', b.textColor, '#hex', 'Texto')}
    </div>
    ${_input(i, 'iconBg', b.iconBg, '#f4f4f5', 'Fundo do Ícone')}
    ${_input(i, 'badge', b.badge, 'Ex: NEW', 'Badge')}
    ${_select(i, 'shape', b.shape, ['rounded', 'pill', 'square'], 'Formato')}`
};

/* ── 3. EVENT ── */
window.BLOCKS.event = {
  type: 'event', label: 'Evento', icon: 'ri-calendar-event-line', group: 'content',
    defaults: () => ({ title: 'Novo Evento', day: '01', month: 'JAN', url: '#', venue: '', cta: 'Ver mais', variant: 'default', coverUrl: '', lineup: '', price: '', status: 'upcoming', icon: 'calendar-s' }),
  preview: (b) => {
    const varCls = b.variant && b.variant !== 'default' ? ` variant-${b.variant}` : '';
    const ic = window.iconHTML(b.icon || 'calendar-s', '18px');
    return `
      <div class="pv-block pv-event${varCls}">
        <div class="pv-event-date">
          <span class="pv-event-day">${_esc(b.day)}</span>
          <span class="pv-event-month">${_esc(b.month)}</span>
        </div>
        <div class="pv-event-info">
          <div class="pv-event-title">${_esc(b.title)}</div>
          ${b.venue ? `<div class="pv-event-venue"><i class="ri-map-pin-fill" style="font-size:10px;opacity:0.5;"></i> ${_esc(b.venue)}</div>` : ''}
          ${b.cta ? `<div class="pv-event-cta">${ic} ${_esc(b.cta)}</div>` : ''}
        </div>
        <div class="pv-event-action"><i class="ri-arrow-right-line" aria-hidden="true"></i></div>
      </div>`;
  },
  editor: (b, i) => `
    ${_input(i, 'title', b.title, 'Nome do evento', 'Título')}
    <div class="sb-cols">
      ${_input(i, 'day', b.day, '01', 'Dia')}
      ${_input(i, 'month', b.month, 'JAN', 'Mês')}
    </div>
    ${_input(i, 'venue', b.venue, 'Local', 'Local')}
    ${_input(i, 'url', b.url, 'https://...', 'URL')}
    ${_input(i, 'cta', b.cta, 'Get Tickets', 'Texto CTA')}
    <div class="sb-field">
      <label class="sb-label"><i class="ri-apps-2-line" aria-hidden="true"></i> Ícone do Evento</label>
      ${_iconBtn(i, 'icon', b.icon || 'calendar-s')}
    </div>
    ${_select(i, 'variant', b.variant, [
      { value: 'default', label: 'Padrão' },
      { value: 'countdown', label: 'Countdown' },
      { value: 'ticket', label: 'Ticket' },
      { value: 'neon', label: 'Neon' }
    ], 'Variante')}
    ${_input(i, 'price', b.price, 'R$80', 'Preço')}
    ${_select(i, 'status', b.status, ['upcoming', 'soldout', 'cancelled'], 'Status')}`
};

/* ── 4. CARDS GRID ── */
window.BLOCKS.cards = {
  type: 'cards', label: 'Cards Grid', icon: 'ri-layout-grid-line', group: 'content',
  defaults: () => ({ cards: [{ title: 'Card 1', img: '', url: '#', icon: '' }] }),
  preview: (b) => {
    const items = (b.cards || []).map(c => `
      <a class="pv-cards-item" href="${_esc(c.url)}" target="_blank" rel="noopener">
        ${c.img ? `<img src="${_esc(c.img)}" alt="${_esc(c.title)}" loading="lazy">` : (c.icon ? `<div style="display:flex;align-items:center;justify-content:center;height:60px;font-size:28px;">${window.iconHTML(c.icon,'28px')}</div>` : '')}
        <div class="pv-cards-overlay"><span class="pv-cards-title">${_esc(c.title)}</span></div>
      </a>`).join('');
    return `<div class="pv-block pv-cards-grid">${items}</div>`;
  },
  editor: (b, i) => {
    let html = '<div class="sb-sec" style="margin-top:0;">Cards</div>';
    (b.cards || []).forEach((c, ci) => {
      html += `
        <div style="background:var(--sf);border-radius:var(--r-sm);padding:10px;margin-bottom:8px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-family:var(--mono);font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ghost);">Card ${ci + 1}</span>
            <button class="i-btn" style="width:22px;height:22px;color:#dc2626;" onclick="HUB.delArrayItem(${i},'cards',${ci})" aria-label="Remover card"><i class="ri-close-line" style="font-size:12px;" aria-hidden="true"></i></button>
          </div>
          ${_input(i, 'cards.' + ci + '.title', c.title, 'Título', 'Título')}
          ${_input(i, 'cards.' + ci + '.img', c.img, 'URL da imagem (ou deixe vazio)', 'Imagem URL')}
          ${_input(i, 'cards.' + ci + '.url', c.url, 'Link', 'URL')}
          <div class="sb-field">
            <label class="sb-label">Ícone (sem imagem)</label>
            ${_iconBtn(i, 'cards.' + ci + '.icon', c.icon)}
          </div>
        </div>`;
    });
    html += `<button class="sb-btn-add" onclick="HUB.addArrayItem(${i},'cards',{title:'Card',img:'',url:'#',icon:''})" aria-label="Adicionar card"><i class="ri-add-line" aria-hidden="true"></i> Adicionar Card</button>`;
    return html;
  }
};

/* ── 5. MARQUEE ── */
window.BLOCKS.marquee = {
  type: 'marquee', label: 'Marquee', icon: 'ri-text-wrap', group: 'layout',
  defaults: () => ({ text: 'YOUR TEXT HERE • ', bgColor: '#121214', textColor: '#ffffff', icon: 'star' }),
  preview: (b) => {
    var ic = b.icon ? `<span style="margin-right:8px;display:inline-flex;align-items:center;">${window.iconHTML(b.icon,'14px')}</span>` : '';
    var content = _esc(b.text);
    return `
    <div class="pv-block pv-marquee" style="${b.bgColor ? 'background:' + b.bgColor + ';' : ''}${b.textColor ? 'color:' + b.textColor + ';' : ''}">
      <div class="pv-marquee-track"><span class="pv-marquee-content">${ic}${content}${ic}${content}</span></div>
    </div>`;
  },
  editor: (b, i) => `
    ${_input(i, 'text', b.text, 'Texto do marquee', 'Texto')}
    <div class="sb-field">
      <label class="sb-label"><i class="ri-apps-2-line" aria-hidden="true"></i> Ícone Separador</label>
      ${_iconBtn(i, 'icon', b.icon)}
    </div>
    <div class="sb-cols">
      ${_input(i, 'bgColor', b.bgColor, '#121214', 'Fundo')}
      ${_input(i, 'textColor', b.textColor, '#ffffff', 'Texto')}
    </div>`
};

/* ── 6. AUDIO ── */
window.BLOCKS.audio = {
  type: 'audio', label: 'Áudio', icon: 'ri-music-2-line', group: 'media',
  defaults: () => ({ title: 'Untitled Track', artist: 'Artist', artUrl: '', embedUrl: '', variant: 'compact', theme: 'dark' }),
  preview: (b) => {
    if (b.embedUrl) {
      return `<div class="pv-block pv-audio"><div class="pv-audio-embed"><iframe src="${_esc(b.embedUrl)}" allow="autoplay; encrypted-media" loading="lazy" title="${_esc(b.title)}"></iframe></div></div>`;
    }
    return `
      <div class="pv-block pv-audio">
        <div class="pv-audio-compact">
          <div class="pv-audio-art">${b.artUrl ? `<img src="${_esc(b.artUrl)}" alt="${_esc(b.title)}" loading="lazy">` : ''}</div>
          <div class="pv-audio-info">
            <div class="pv-audio-title">${_esc(b.title)}</div>
            <div class="pv-audio-artist">${_esc(b.artist)}</div>
          </div>
          <div class="pv-audio-play"><i class="ri-play-fill" aria-hidden="true"></i></div>
        </div>
        <div class="pv-audio-wave">${Array(16).fill(0).map(function(_, x) { return '<div class="pv-audio-wave-bar" style="height:' + (20 + Math.random() * 80) + '%;animation-delay:' + (x * 0.05) + 's;"></div>'; }).join('')}</div>
      </div>`;
  },
  editor: (b, i) => `
    ${_input(i, 'title', b.title, 'Nome da faixa', 'Título')}
    ${_input(i, 'artist', b.artist, 'Artista', 'Artista')}
    ${_input(i, 'artUrl', b.artUrl, 'URL da capa', 'Capa (URL)')}
    ${_input(i, 'embedUrl', b.embedUrl, 'Spotify/SoundCloud embed URL', 'Embed URL')}
    <span class="sb-help">Cole a URL de embed do Spotify ou SoundCloud. Se vazio, mostra player visual.</span>
    ${_select(i, 'variant', b.variant, ['compact', 'full', 'minimal'], 'Variante')}
    ${_select(i, 'theme', b.theme, ['dark', 'light'], 'Tema')}`
};

/* ── 7. DIVIDER ── */
window.BLOCKS.divider = {
  type: 'divider', label: 'Divisor', icon: 'ri-separator', group: 'layout',
  defaults: () => ({ style: 'line', icon: '' }),
  preview: (b) => {
    if (b.style === 'space') return '<div class="pv-block pv-divider" style="padding:0.8rem 0;"></div>';
    if (b.icon) {
      return `<div class="pv-block pv-divider" style="display:flex;align-items:center;gap:10px;"><div class="pv-divider-line" style="flex:1;"></div><span style="opacity:0.5;">${window.iconHTML(b.icon,'16px')}</span><div class="pv-divider-line" style="flex:1;"></div></div>`;
    }
    if (b.style === 'wave') return '<div class="pv-block pv-divider"><div class="pv-divider-line wave"></div></div>';
    return '<div class="pv-block pv-divider"><div class="pv-divider-line"></div></div>';
  },
  editor: (b, i) => `
    ${_select(i, 'style', b.style, [
      { value: 'line', label: 'Linha' },
      { value: 'wave', label: 'Onda' },
      { value: 'space', label: 'Espaço' }
    ], 'Estilo')}
    <div class="sb-field">
      <label class="sb-label"><i class="ri-apps-2-line" aria-hidden="true"></i> Ícone Central (opcional)</label>
      ${_iconBtn(i, 'icon', b.icon)}
    </div>`
};

/* ── 8. TEXT / BIO ── */
window.BLOCKS.text = {
  type: 'text', label: 'Texto', icon: 'ri-text', group: 'content',
  defaults: () => ({ text: 'Escreva aqui...', format: 'plain', alignment: 'center', tags: [], status: '', statusType: 'available', icon: '' }),
  preview: (b) => {
    var content = _esc(b.text);
    if (b.format === 'markdown') content = content.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    var align = b.alignment || 'center';
    var iconHTML = b.icon ? '<div style="text-align:' + align + ';margin-bottom:8px;font-size:24px;">' + window.iconHTML(b.icon, '24px') + '</div>' : '';
    var tagsHTML = '';
    if (b.tags && b.tags.length) {
      tagsHTML = '<div class="pv-tags">' + b.tags.map(function(t) { return '<span class="pv-tag">' + _esc(t) + '</span>'; }).join('') + '</div>';
    }
    var statusHTML = '';
    if (b.status) {
      statusHTML = '<div style="text-align:' + align + ';margin-top:6px;"><span class="pv-status ' + (b.statusType || 'available') + '">' + _esc(b.status) + '</span></div>';
    }
    return '<div class="pv-block pv-text" style="text-align:' + align + ';">' + iconHTML + '<p>' + content + '</p>' + statusHTML + '</div>' + tagsHTML;
  },
  editor: (b, i) => `
    ${_textarea(i, 'text', b.text, 'Seu texto aqui...', 'Texto')}
    <div class="sb-field">
      <label class="sb-label"><i class="ri-apps-2-line" aria-hidden="true"></i> Ícone de Destaque (opcional)</label>
      ${_iconBtn(i, 'icon', b.icon)}
    </div>
    ${_select(i, 'alignment', b.alignment, [
      { value: 'left', label: 'Esquerda' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Direita' }
    ], 'Alinhamento')}
    ${_input(i, '_tagsStr', (b.tags || []).join(', '), 'tag1, tag2, tag3', 'Tags (separadas por vírgula)')}
    <div class="sb-cols">
      ${_input(i, 'status', b.status, 'Ex: Disponível', 'Status')}
      ${_select(i, 'statusType', b.statusType, ['available', 'busy'], 'Tipo')}
    </div>`
};

/* ── 9. SOCIAL ICONS ── */
window.BLOCKS.social = {
  type: 'social', label: 'Sociais', icon: 'ri-share-line', group: 'content',
  defaults: () => ({ icons: [{ icon: 'instagram-s', url: '', label: 'Instagram' }], size: 'md', alignment: 'center' }),
  preview: (b) => {
    var align = b.alignment || 'center';
    var items = (b.icons || []).map(function(s) {
      return '<a class="pv-social-item" href="' + _esc(s.url) + '" target="_blank" rel="noopener" title="' + _esc(s.label) + '">' + window.iconHTML(s.icon || 'link', '20px') + '</a>';
    }).join('');
    return '<div class="pv-block pv-social" style="justify-content:' + align + ';">' + items + '</div>';
  },
  editor: (b, i) => {
    var html = '';
    (b.icons || []).forEach(function(s, si) {
      html += `
        <div style="background:var(--sf);border-radius:var(--r-sm);padding:10px;margin-bottom:8px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-family:var(--mono);font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ghost);">${_esc(s.label || 'Social')}</span>
            <button class="i-btn" style="width:22px;height:22px;color:#dc2626;" onclick="HUB.delArrayItem(${i},'icons',${si})" aria-label="Remover social"><i class="ri-close-line" style="font-size:12px;" aria-hidden="true"></i></button>
          </div>
          <div class="sb-field">
            <label class="sb-label">Ícone</label>
            ${_iconBtn(i, 'icons.' + si + '.icon', s.icon)}
          </div>
          ${_input(i, 'icons.' + si + '.url', s.url, 'https://...', 'URL')}
          ${_input(i, 'icons.' + si + '.label', s.label, 'Nome', 'Label')}
        </div>`;
    });
    html += '<button class="sb-btn-add" onclick="HUB.addArrayItem(' + i + ',\'icons\',{icon:\'link\',url:\'\',label:\'Link\'})" aria-label="Adicionar social"><i class="ri-add-line" aria-hidden="true"></i> Adicionar Social</button>';
    html += _select(i, 'alignment', b.alignment, [
      { value: 'flex-start', label: 'Esquerda' },
      { value: 'center', label: 'Centro' },
      { value: 'flex-end', label: 'Direita' }
    ], 'Alinhamento');
    return html;
  }
};

/* ── 10. TESTIMONIAL ── */
window.BLOCKS.testimonial = {
  type: 'testimonial', label: 'Depoimento', icon: 'ri-chat-quote-line', group: 'content',
  defaults: () => ({ items: [{ quote: 'Excelente!', author: 'Nome', role: 'Cargo' }] }),
  preview: (b) => {
    var first = (b.items || [])[0] || { quote: '', author: '', role: '' };
    return `
      <div class="pv-block pv-testimonial">
        <div class="pv-testimonial-quote">${_esc(first.quote)}</div>
        <div class="pv-testimonial-author">${_esc(first.author)}</div>
        <div class="pv-testimonial-role">${_esc(first.role)}</div>
      </div>`;
  },
  editor: (b, i) => {
    var html = '';
    (b.items || []).forEach(function(item, ti) {
      html += `
        <div style="background:var(--sf);border-radius:var(--r-sm);padding:10px;margin-bottom:8px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-family:var(--mono);font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ghost);">Depoimento ${ti + 1}</span>
            <button class="i-btn" style="width:22px;height:22px;color:#dc2626;" onclick="HUB.delArrayItem(${i},'items',${ti})" aria-label="Remover depoimento"><i class="ri-close-line" style="font-size:12px;" aria-hidden="true"></i></button>
          </div>
          ${_textarea(i, 'items.' + ti + '.quote', item.quote, 'Citação', '')}
          <div class="sb-cols">
            ${_input(i, 'items.' + ti + '.author', item.author, 'Autor', '')}
            ${_input(i, 'items.' + ti + '.role', item.role, 'Cargo', '')}
          </div>
        </div>`;
    });
    html += '<button class="sb-btn-add" onclick="HUB.addArrayItem(' + i + ',\'items\',{quote:\'\',author:\'\',role:\'\'})" aria-label="Adicionar depoimento"><i class="ri-add-line" aria-hidden="true"></i> Adicionar</button>';
    return html;
  }
};

/* ── 11. NEWSLETTER ── */
window.BLOCKS.newsletter = {
  type: 'newsletter', label: 'Newsletter', icon: 'ri-mail-send-line', group: 'interactive',
    defaults: () => ({ title: 'Fique por dentro', placeholder: 'Seu email', buttonText: 'Inscrever', submitUrl: '', bgColor: '', textColor: '', icon: 'mail-s' }),
  preview: (b) => `
    <div class="pv-block pv-newsletter" style="${b.bgColor ? 'background:' + b.bgColor + ';' : ''}${b.textColor ? 'color:' + b.textColor + ';' : ''}">
      <div class="pv-newsletter-title" style="display:flex;align-items:center;gap:6px;">${b.icon ? window.iconHTML(b.icon,'16px') : ''} ${_esc(b.title)}</div>
      <div class="pv-newsletter-form">
        <input type="email" placeholder="${_esc(b.placeholder)}" readonly>
        <button type="button">${_esc(b.buttonText)}</button>
      </div>
    </div>`,  
  editor: (b, i) => `
    ${_input(i, 'title', b.title, 'Título', 'Título')}
    <div class="sb-field">
      <label class="sb-label"><i class="ri-apps-2-line" aria-hidden="true"></i> Ícone</label>
      ${_iconBtn(i, 'icon', b.icon || 'mail-s')}
    </div>
    ${_input(i, 'placeholder', b.placeholder, 'Placeholder', 'Placeholder')}
    ${_input(i, 'buttonText', b.buttonText, 'Texto do botão', 'Botão')}
    ${_input(i, 'submitUrl', b.submitUrl, 'URL do formulário', 'URL de envio')}
    <span class="sb-help">URL que receberá o POST com o email.</span>
    <div class="sb-cols">
      ${_input(i, 'bgColor', b.bgColor, '#hex', 'Fundo')}
      ${_input(i, 'textColor', b.textColor, '#hex', 'Texto')}
    </div>`
};

/* ── 12. MONETIZE ── */
window.BLOCKS.monetize = {
  type: 'monetize', label: 'Monetização', icon: 'ri-hand-coin-line', group: 'interactive',
  defaults: () => ({ title: 'Apoie', desc: '', url: '#', buttonText: 'Apoiar', icon: 'fire-s', variant: 'tipjar' }),
  preview: (b) => {
    var ic = window.iconHTML(b.icon || 'fire-s', '20px');
    return `
      <div class="pv-block pv-monetize">
        <div class="pv-monetize-icon">${ic}</div>
        <div class="pv-monetize-info">
          <div class="pv-monetize-title">${_esc(b.title)}</div>
          ${b.desc ? '<div class="pv-monetize-desc">' + _esc(b.desc) + '</div>' : ''}
        </div>
        <button class="pv-monetize-btn" type="button">${_esc(b.buttonText)}</button>
      </div>`;
  },
  editor: (b, i) => `
    ${_input(i, 'title', b.title, 'Título', 'Título')}
    ${_input(i, 'desc', b.desc, 'Descrição curta', 'Descrição')}
    ${_input(i, 'url', b.url, 'https://...', 'URL')}
    ${_input(i, 'buttonText', b.buttonText, 'Apoiar', 'Texto do Botão')}
    <div class="sb-field">
      <label class="sb-label">Ícone</label>
      ${_iconBtn(i, 'icon', b.icon)}
    </div>
    ${_select(i, 'variant', b.variant, ['tipjar', 'membership', 'donate'], 'Variante')}`
};

/* ── 13. CONTACT ── */
window.BLOCKS.contact = {
  type: 'contact', label: 'Contato', icon: 'ri-contacts-line', group: 'content',
  defaults: () => ({ buttons: [{ label: 'WhatsApp', icon: 'whatsapp-s', url: '#' }] }),
  preview: (b) => {
    var btns = (b.buttons || []).map(function(c) {
      var ic = window.iconHTML(c.icon || 'link', '14px');
      return '<a class="pv-contact-btn" href="' + _esc(c.url) + '" target="_blank" rel="noopener">' + ic + ' ' + _esc(c.label) + '</a>';
    }).join('');
    return '<div class="pv-block pv-contact-row">' + btns + '</div>';
  },
  editor: (b, i) => {
    var html = '';
    (b.buttons || []).forEach(function(c, ci) {
      html += `
        <div style="background:var(--sf);border-radius:var(--r-sm);padding:10px;margin-bottom:8px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-family:var(--mono);font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ghost);">${_esc(c.label || 'Botão')}</span>
            <button class="i-btn" style="width:22px;height:22px;color:#dc2626;" onclick="HUB.delArrayItem(${i},'buttons',${ci})" aria-label="Remover botão"><i class="ri-close-line" style="font-size:12px;" aria-hidden="true"></i></button>
          </div>
          ${_input(i, 'buttons.' + ci + '.label', c.label, 'Label', '')}
          ${_input(i, 'buttons.' + ci + '.url', c.url, 'https://...', '')}
          <div class="sb-field">
            <label class="sb-label">Ícone</label>
            ${_iconBtn(i, 'buttons.' + ci + '.icon', c.icon)}
          </div>
        </div>`;
    });
    html += '<button class="sb-btn-add" onclick="HUB.addArrayItem(' + i + ',\'buttons\',{label:\'Novo\',icon:\'link\',url:\'#\'})" aria-label="Adicionar botão de contato"><i class="ri-add-line" aria-hidden="true"></i> Adicionar</button>';
    return html;
  }
};

console.log('[HUB] blocks-hub.js loaded — ' + Object.keys(window.BLOCKS).length + ' block types registered');

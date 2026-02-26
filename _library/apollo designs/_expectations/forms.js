(function() {
  'use strict';
  const cssForms = `
  :root {
  --ff-main: "Space Grotesk", system-ui, sans-serif;
  --ff-mono: "Space Mono", monospace;
  --primary: #f45f00;
  --black-1: #121214;
  --white-1: #ffffff;
  --border: rgba(0,0,0,0.15);
  --muted: rgba(19,21,23,0.31);
  --txt: rgba(19,21,23,0.77);
  --txt-head: rgba(19,21,23,0.9);
  --ease: cubic-bezier(0.16, 1, 0.3, 1);
  --radius: 18px;
}

/* ══════════════════════════════════════
   RESET
   ══════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: var(--ff-main);
  font-size: 15px;
  color: var(--txt);
  background: var(--white-1);
  -webkit-font-smoothing: antialiased;
  min-height: 100vh;
}

/* ══════════════════════════════════════
   DEMO PAGE
   ══════════════════════════════════════ */
.demo-page {
  max-width: 480px;
  margin: 0 auto;
  padding: 60px 40px 100px;
}
.demo-label {
  font-family: var(--ff-mono);
  font-size: 10px;
  color: var(--primary);
  letter-spacing: 0.2em;
  text-transform: uppercase;
  margin-bottom: 8px;
}
.demo-title {
  font-size: 32px;
  font-weight: 300;
  color: var(--black-1);
  margin: 0 0 50px;
  line-height: 1.1;
}
.demo-divider {
  border: none;
  border-top: 1px solid var(--border);
  margin: 50px 0;
}
.type-badge {
  display: inline-block;
  font-family: var(--ff-mono);
  font-size: 9px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--primary);
  border: 1px solid var(--primary);
  padding: 3px 8px;
  margin-bottom: 24px;
}

/* ══════════════════════════════════════
   SHARED: .input-group + .apollo-input
   (Your existing form base)
   ══════════════════════════════════════ */
.input-group {
  position: relative;
  margin-bottom: 35px;
}
.apollo-label {
  position: absolute;
  top: 12px;
  left: 0;
  font-family: var(--ff-mono);
  font-size: 12px;
  color: var(--muted);
  pointer-events: none;
  transition: all 0.4s var(--ease);
  text-transform: uppercase;
}
.apollo-input {
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--border);
  padding: 12px 0;
  font-family: var(--ff-main);
  font-size: 16px;
  color: var(--black-1);
  border-radius: 0;
  outline: none;
  transition: border-color 0.4s var(--ease);
}
.apollo-input:focus {
  border-bottom-color: var(--primary);
}
.apollo-input:focus ~ .apollo-label,
.apollo-input:not(:placeholder-shown) ~ .apollo-label {
  top: -10px;
  font-size: 10px;
  color: var(--primary);
}


/* ══════════════════════════════════════════════════
   TYPE 1 — FORM INLINE
   Custom dropdown, border-bottom, icon badges,
   two-line options, animated underline, stagger
   ══════════════════════════════════════════════════ */
.as1 { position: relative; }

/* Trigger */
.as1-trigger {
  display: flex;
  align-items: center;
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--border);
  padding: 12px 28px 12px 0;
  font-family: var(--ff-main);
  font-size: 16px;
  color: var(--black-1);
  cursor: pointer;
  outline: none;
  text-align: left;
  position: relative;
}

/* Animated underline from center */
.as1-trigger::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 50%;
  width: 0;
  height: 2px;
  background: var(--primary);
  transition: width 0.4s var(--ease), left 0.4s var(--ease);
}
.as1.is-open .as1-trigger::after {
  left: 0;
  width: 100%;
}

/* Placeholder state */
.as1-trigger.is-placeholder { color: transparent; }
.as1-trigger.is-placeholder ~ .apollo-label {
  top: 12px;
  font-size: 12px;
  color: var(--muted);
}

/* Label float when open or has value */
.as1.is-open .apollo-label,
.as1.has-value .apollo-label {
  top: -10px;
  font-size: 10px;
  color: var(--primary);
}

/* Value display inside trigger */
.as1-val { flex: 1; display: flex; align-items: center; gap: 10px; }

.as1-val-icon {
  width: 22px;
  height: 22px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.3s var(--ease);
  overflow: hidden;
}
.as1-val-icon svg { width: 12px; height: 12px; color: #fff; }
.as1-trigger.is-placeholder .as1-val-icon { width: 0; opacity: 0; }

.as1-val-text { font-weight: 400; }

/* Chevron */
.as1-arrow {
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  color: var(--muted);
  transition: transform 0.4s var(--ease), color 0.3s;
  pointer-events: none;
}
.as1.is-open .as1-arrow {
  transform: translateY(-50%) rotate(180deg);
  color: var(--primary);
}

/* Dropdown */
.as1-drop {
  position: absolute;
  top: calc(100% + 2px);
  left: -16px;
  right: -16px;
  z-index: 100;
  
  border-top: none;

  padding: 8px 0;
  max-height: 300px;
  overflow-y: auto;
  /* Hidden by default */
  opacity: 0;
  visibility: hidden;
  transform: translateY(-4px);
  transition: opacity 0.25s var(--ease), transform 0.3s var(--ease), visibility 0s 0.3s;
}
.as1.is-open .as1-drop {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
  transition: opacity 0.25s var(--ease), transform 0.3s var(--ease), visibility 0s 0s;
}

/* Options — expand from height 0 */
.as1-opt {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 16px;
  height: 0;
  overflow: hidden;
  opacity: 0;
  cursor: pointer;
  transition: height 0.35s var(--ease), opacity 0.3s, background 0.15s;
}
.as1.is-open .as1-opt {
  padding: 1px 8px;
  background-color: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(5px) saturate(200%);
  -webkit-backdrop-filter: blur(5px) saturate(200%);
  border: 1px solid rgba(255, 255, 255, 0.10) !important;
  border-radius: var(--radius);
  corner-shape: squircle;
  height: 52px;
  opacity: 1;
}
.as1.is-open .as1-opt:nth-child(1) { transition-delay: 0.02s; }
.as1.is-open .as1-opt:nth-child(2) { transition-delay: 0.06s; }
.as1.is-open .as1-opt:nth-child(3) { transition-delay: 0.10s; }
.as1.is-open .as1-opt:nth-child(4) { transition-delay: 0.14s; }

.as1.is-open .as1-opt:hover { background: rgba(244,95,0,0.04); }
  
.as1.is-open .as1-opt.is-selected { background: rgba(244,95,0,0.099); }
  
.as1-opt.is-selected .as1-opt-check { opacity: 1; transform: scale(1); }

/* Icon badge */
.as1-opt-icon {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.25s var(--ease), box-shadow 0.25s;
}
.as1-opt:hover .as1-opt-icon {
  transform: scale(1.1);
  box-shadow: 0 3px 10px rgba(0,0,0,0.12);
}
.as1-opt-icon svg { width: 14px; height: 14px; color: #fff; }

/* Text */
.as1-opt-text { flex: 1; display: flex; flex-direction: column; gap: 1px; }
.as1-opt-name { font-weight: 500; font-size: 14px; color: var(--black-1); line-height: 1.2; }
.as1-opt-hint { font-size: 11px; font-family: var(--ff-mono); color: var(--muted); line-height: 1.2; }

/* Checkmark */
.as1-opt-check {
  width: 16px;
  height: 16px;
  color: var(--primary);
  opacity: 0;
  transform: scale(0.5);
  transition: opacity 0.25s, transform 0.3s var(--ease);
  flex-shrink: 0;
}


/* ══════════════════════════════════════════════════
   TYPE 2 — PALETTE WRITABLE
   Border-bottom, type-to-filter on trigger,
   color swatches, no search inside dropdown
   ══════════════════════════════════════════════════ */
.as2 { position: relative; }

.as2-input {
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--border);
  padding: 12px 40px 12px 0;
  font-family: var(--ff-main);
  font-size: 16px;
  color: var(--black-1);
  border-radius: 0;
  outline: none;
  transition: border-color 0.4s var(--ease);
}
.as2-input::placeholder { color: transparent; }

/* Animated underline */
.as2-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background: var(--primary);
  transition: width 0.4s var(--ease), left 0.4s var(--ease);
  pointer-events: none;
}
.as2-input:focus ~ .as2-line,
.as2.is-open .as2-line {
  left: 0;
  width: 100%;
}
.as2-input:focus,
.as2.is-open .as2-input {
  border-bottom-color: transparent;
}

/* Label float */
.as2-input:focus ~ .apollo-label,
.as2-input:not(:placeholder-shown) ~ .apollo-label,
.as2.has-value .apollo-label {
  top: -10px;
  font-size: 10px;
  color: var(--primary);
}

/* Active swatch (pops in after selection) */
.as2-swatch {
  position: absolute;
  left: 0;
  bottom: 14px;
  width: 16px;
  height: 16px;
  border-radius: 4px;
  pointer-events: none;
  opacity: 0;
  transform: scale(0.4) rotate(-10deg);
  transition: all 0.35s var(--ease);
}
.as2.has-value .as2-swatch {
  opacity: 1;
  transform: scale(1) rotate(0);
}
.as2.has-value .as2-input { padding-left: 28px; }

/* Arrow */
.as2-arrow {
  position: absolute;
  right: 0;
  bottom: 12px;
  width: 18px;
  height: 18px;
  color: var(--muted);
  pointer-events: none;
  transition: color 0.3s, transform 0.4s var(--ease);
}
.as2.is-open .as2-arrow {
  transform: rotate(180deg);
  color: var(--primary);
}

/* Dropdown */
.as2-drop {
  position: absolute;
  top: calc(100% + 2px);
  left: -12px;
  right: -12px;
  z-index: 100;
  max-height: 240px;
  overflow-y: auto;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-4px);
  transition: opacity 0.25s var(--ease), transform 0.3s var(--ease), visibility 0s 0.3s;
}
.as2.is-open .as2-drop {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
  transition: opacity 0.25s var(--ease), transform 0.3s var(--ease), visibility 0s 0s;
}

/* Options */
.as2-opt {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  cursor: pointer;
  font-size: 14px;
  color: var(--txt);
  transition: background 0.15s, transform 0.2s var(--ease);
  padding: 8px 8px;
  background-color: rgba(255, 255, 255, 0.35);
  backdrop-filter: blur(5px) saturate(200%);
  -webkit-backdrop-filter: blur(5px) saturate(200%);
  border: 1px solid rgba(255, 255, 255, 0.10) !important;
  border-radius: var(--radius);
  corner-shape: squircle;
}
.as2-opt:hover { background: rgba(244,95,0,0.04) }
.as2-opt:active { transform: scale(0.985); }
.as2-opt.is-selected { background: rgba(244,95,0,0.099); }
.as2-opt[hidden] { display: none; }

.as2-opt-swatch {
  width: 20px;
  height: 20px;
  border-radius: 5px;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1), inset 0 0 0 1px rgba(255,255,255,0.15);
  transition: transform 0.25s var(--ease);
}
.as2-opt:hover .as2-opt-swatch { transform: scale(1.18); }

.as2-opt-name { font-weight: 500; color: var(--black-1); }
.as2-opt-desc { color: var(--muted); font-size: 12px; margin-left: 6px; }

.as2-opt-check {
  margin-left: auto;
  width: 14px;
  height: 14px;
  color: var(--primary);
  opacity: 0;
  transition: opacity 0.2s;
  flex-shrink: 0;
}
.as2-opt.is-selected .as2-opt-check { opacity: 1; }

.as2-empty {
  padding: 16px;
  text-align: center;
  color: var(--muted);
  font-family: var(--ff-mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: none;
}
.as2-empty.is-visible { display: block; }

/* Stagger */
.as2.is-open .as2-opt {
  animation: slideL 0.25s var(--ease) backwards;
}
.as2.is-open .as2-opt:nth-child(1) { animation-delay: 0.02s; }
.as2.is-open .as2-opt:nth-child(2) { animation-delay: 0.05s; }
.as2.is-open .as2-opt:nth-child(3) { animation-delay: 0.08s; }
.as2.is-open .as2-opt:nth-child(4) { animation-delay: 0.11s; }
.as2.is-open .as2-opt:nth-child(5) { animation-delay: 0.14s; }
@keyframes slideL {
  from { opacity: 0; transform: translateX(-8px); }
  to   { opacity: 1; transform: translateX(0); }
}


/* ══════════════════════════════════════════════════
   TYPE 3 — GLASS PANEL
   Rounded, frosted blur, search inside dropdown
   ══════════════════════════════════════════════════ */
.as3 { position: relative; }

.as3-trigger {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 12px 16px;
  font-family: var(--ff-main);
  font-size: 14px;
  color: var(--black-1);
  background: rgba(0,0,0,0.02);
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  border: 1px solid var(--border);
  border-radius: 10px;
  cursor: pointer;
  outline: none;
  text-align: left;
  line-height: 1.4;
  transition: border-color 0.25s var(--ease), box-shadow 0.25s var(--ease);
}
.as3-trigger:hover { border-color: rgba(0,0,0,0.22); }
.as3.is-open .as3-trigger {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(244,95,0,0.10);
}

.as3-swatch {
  width: 14px;
  height: 14px;
  border-radius: 4px;
  flex-shrink: 0;
  background: var(--primary);
  transition: background 0.3s var(--ease);
}

.as3-label {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.as3-trigger.is-placeholder .as3-label { color: var(--muted); }
.as3-lbl-name { font-weight: 500; color: var(--black-1); }
.as3-lbl-desc { color: var(--muted); margin-left: 4px; font-size: 13px; }

.as3-chevron {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  color: var(--muted);
  transition: transform 0.3s var(--ease), color 0.2s;
}
.as3.is-open .as3-chevron {
  transform: rotate(180deg);
  color: var(--primary);
}

/* Dropdown */
.as3-drop {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  z-index: 100;
  background: rgba(255,255,255,0.65);
  backdrop-filter: blur(24px) saturate(200%);
  -webkit-backdrop-filter: blur(24px) saturate(200%);
  border: 1px solid var(--border);
  border-radius: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04);
  padding: 6px;
  max-height: 280px;
  overflow-y: auto;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px) scale(0.98);
  transition: opacity 0.2s var(--ease), transform 0.25s var(--ease), visibility 0s 0.25s;
}
.as3.is-open .as3-drop {
  opacity: 1;
  visibility: visible;
  transform: translateY(0) scale(1);
  transition: opacity 0.25s var(--ease), transform 0.3s var(--ease), visibility 0s 0s;
}

/* Search */
.as3-search-wrap { position: sticky; top: 0; z-index: 2; padding: 2px 2px 6px; }
.as3-search {
  width: 100%;
  padding: 9px 12px 9px 32px;
  font-family: var(--ff-main);
  font-size: 13px;
  color: var(--txt);
  background: rgba(0,0,0,0.04);
  border: 1px solid var(--border);
  border-radius: 8px;
  outline: none;
  transition: border-color 0.2s var(--ease);
}
.as3-search::placeholder { color: var(--muted); }
.as3-search:focus { border-color: var(--primary); }
.as3-search-icon {
  position: absolute;
  left: 14px;
  top: 13px;
  width: 14px;
  height: 14px;
  color: var(--muted);
  pointer-events: none;
}

/* Options */
.as3-opt {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  cursor: pointer;
  color: var(--txt);
  font-size: 14px;
  transition: background 0.15s var(--ease);
}
.as3-opt:hover, .as3-opt.is-focused { background: rgba(0,0,0,0.04); }
.as3-opt.is-selected { background: rgba(244,95,0,0.06); color: var(--black-1); }
.as3-opt[hidden] { display: none; }

.as3-opt-sw { width: 14px; height: 14px; border-radius: 4px; flex-shrink: 0; }
.as3-opt-name { font-weight: 500; color: var(--black-1); }
.as3-opt-desc { color: var(--muted); font-size: 13px; margin-left: 4px; }

.as3-opt-check {
  margin-left: auto;
  width: 16px;
  height: 16px;
  color: var(--primary);
  opacity: 0;
  transition: opacity 0.2s;
  flex-shrink: 0;
}
.as3-opt.is-selected .as3-opt-check { opacity: 1; }

.as3-empty {
  padding: 20px 12px;
  text-align: center;
  color: var(--muted);
  font-size: 12px;
  font-family: var(--ff-mono);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: none;
}
.as3-empty.is-visible { display: block; }

/* Stagger */
.as3.is-open .as3-opt { animation: fadeUp 0.3s var(--ease) backwards; }
.as3.is-open .as3-opt:nth-child(1) { animation-delay: 0.03s; }
.as3.is-open .as3-opt:nth-child(2) { animation-delay: 0.06s; }
.as3.is-open .as3-opt:nth-child(3) { animation-delay: 0.09s; }
.as3.is-open .as3-opt:nth-child(4) { animation-delay: 0.12s; }
.as3.is-open .as3-opt:nth-child(5) { animation-delay: 0.15s; }
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ══════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════ */
@media (pointer: coarse) {
  .as1.is-open .as1-opt { height: 56px; }
  .as2-opt { min-height: 44px; }
  .as3-opt { min-height: 44px; padding: 12px; }
  .as3-trigger { padding: 14px 16px; }
}
`;
 const style = document.createElement('style');
  /* id="forms-apollo" unique identifier for this injected style block.
     Allows devtools inspection, CSP reporting, and runtime detection:
     document.getElementById('forms-apollo') confirms injection presence. */
  style.id = 'forms-apollo';
  style.textContent =  cssForms;
  const head = document.head || document.getElementsByTagName('head')[0];
  const firstChild = head.firstChild;
  firstChild ? head.insertBefore(style, firstChild) : head.appendChild(style);
})();


(function() { 
'use strict';
  
 const cssFormsURL = 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&family=Space+Mono:wght@400;700&display=swap';
 const linkFormsId = 'apollo-token-css';
  
 if (document.getElementById(linkFormsId)) return;
  
  const linkForms = document.createElement('link');
  linkForms.id = linkFormsId;
  linkForms.rel = 'stylesheet';
  linkForms.href = cssFormsURL;
  linkForms.media = 'all';
 
  const head = document.head || document.getElementsByTagName('head')[0];
  const firstChild = head.firstChild;
  firstChild ? head.insertBefore(linkForms, firstChild) : head.appendChild(linkForms);
    })();



document.addEventListener('DOMContentLoaded', function() {

  // ─── Helper: close on outside click ───
  function onOutside(root, closeFn) {
    document.addEventListener('click', function(e) {
      if (!root.contains(e.target)) closeFn();
    });
  }

  // ════════════════════════════════════════
  // TYPE 1 — Form Inline
  // ════════════════════════════════════════
  document.querySelectorAll('.as1').forEach(function(root) {
    var trigger = root.querySelector('.as1-trigger');
    var opts    = root.querySelectorAll('.as1-opt');
    var valIcon = trigger.querySelector('.as1-val-icon');
    var valText = trigger.querySelector('.as1-val-text');

    function open() {
      document.querySelectorAll('.as1.is-open').forEach(function(el) {
        if (el !== root) el.classList.remove('is-open');
      });
      root.classList.add('is-open');
    }
    function close() {
      root.classList.remove('is-open');
      opts.forEach(function(o) { o.classList.remove('is-focused'); });
    }
    function toggle() { root.classList.contains('is-open') ? close() : open(); }

    function select(opt) {
      opts.forEach(function(o) { o.classList.remove('is-selected'); });
      opt.classList.add('is-selected');
      trigger.classList.remove('is-placeholder');
      root.classList.add('has-value');
      valText.textContent = opt.dataset.name;
      valIcon.style.background = opt.dataset.iconBg;
      valIcon.innerHTML = opt.querySelector('.as1-opt-icon').innerHTML;
      close();
      trigger.focus();
    }

    trigger.addEventListener('click', toggle);

    trigger.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
      if (e.key === 'Escape') close();
    });

    opts.forEach(function(opt, i) {
      opt.addEventListener('click', function(e) {
        e.stopPropagation();
        select(opt);
      });
      opt.addEventListener('mouseenter', function() {
        opts.forEach(function(o) { o.classList.remove('is-focused'); });
        opt.classList.add('is-focused');
      });
    });

    onOutside(root, close);
  });


  // ════════════════════════════════════════
  // TYPE 2 — Palette Writable
  // ════════════════════════════════════════
  document.querySelectorAll('.as2').forEach(function(root) {
    var input  = root.querySelector('.as2-input');
    var opts   = root.querySelectorAll('.as2-opt');
    var empty  = root.querySelector('.as2-empty');
    var swatch = root.querySelector('.as2-swatch');

    function open()  { root.classList.add('is-open'); }
    function close() {
      root.classList.remove('is-open');
      opts.forEach(function(o) { o.classList.remove('is-focused'); });
    }

    function filter() {
      var q = input.value.toLowerCase().trim();
      var visible = 0;
      opts.forEach(function(opt) {
        var text = (opt.dataset.name + ' ' + (opt.dataset.desc || '')).toLowerCase();
        var show = !q || text.indexOf(q) !== -1;
        opt.hidden = !show;
        if (show) visible++;
      });
      empty.classList.toggle('is-visible', visible === 0);
    }

    function select(opt) {
      opts.forEach(function(o) { o.classList.remove('is-selected'); });
      opt.classList.add('is-selected');
      input.value = opt.dataset.name;
      root.classList.add('has-value');
      swatch.style.background = opt.dataset.value;
      close();
    }

    input.addEventListener('focus', open);
    input.addEventListener('input', function() { open(); filter(); });

    opts.forEach(function(opt) {
      opt.addEventListener('click', function(e) {
        e.stopPropagation();
        select(opt);
      });
      opt.addEventListener('mouseenter', function() {
        opts.forEach(function(o) { o.classList.remove('is-focused'); });
        opt.classList.add('is-focused');
      });
    });

    input.addEventListener('keydown', function(e) {
      var visOpts = Array.from(opts).filter(function(o) { return !o.hidden; });
      var focused = visOpts.find(function(o) { return o.classList.contains('is-focused'); });
      var idx = visOpts.indexOf(focused);

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        visOpts.forEach(function(o) { o.classList.remove('is-focused'); });
        idx = idx < visOpts.length - 1 ? idx + 1 : 0;
        visOpts[idx].classList.add('is-focused');
        visOpts[idx].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        visOpts.forEach(function(o) { o.classList.remove('is-focused'); });
        idx = idx > 0 ? idx - 1 : visOpts.length - 1;
        visOpts[idx].classList.add('is-focused');
        visOpts[idx].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (focused) select(focused);
      } else if (e.key === 'Escape') {
        close();
        input.blur();
      }
    });

    onOutside(root, close);
  });


  // ════════════════════════════════════════
  // TYPE 3 — Glass Panel
  // ════════════════════════════════════════
  document.querySelectorAll('.as3').forEach(function(root) {
    var trigger  = root.querySelector('.as3-trigger');
    var search   = root.querySelector('.as3-search');
    var opts     = root.querySelectorAll('.as3-opt');
    var empty    = root.querySelector('.as3-empty');
    var swatchEl = root.querySelector('.as3-swatch');
    var labelEl  = root.querySelector('.as3-label');

    function open() {
      root.classList.add('is-open');
      search.value = '';
      filter();
      requestAnimationFrame(function() { search.focus(); });
    }
    function close() {
      root.classList.remove('is-open');
      opts.forEach(function(o) { o.classList.remove('is-focused'); });
    }
    function toggle() { root.classList.contains('is-open') ? close() : open(); }

    function filter() {
      var q = search.value.toLowerCase().trim();
      var visible = 0;
      opts.forEach(function(opt) {
        var text = (opt.dataset.name + ' ' + (opt.dataset.desc || '')).toLowerCase();
        var show = !q || text.indexOf(q) !== -1;
        opt.hidden = !show;
        if (show) visible++;
      });
      empty.classList.toggle('is-visible', visible === 0);
      opts.forEach(function(o) { o.classList.remove('is-focused'); });
    }

    function select(opt) {
      opts.forEach(function(o) { o.classList.remove('is-selected'); });
      opt.classList.add('is-selected');
      trigger.classList.remove('is-placeholder');
      var name = opt.dataset.name;
      var desc = opt.dataset.desc || '';
      var html = '<span class="as3-lbl-name">' + name + '</span>';
      if (desc) html += '<span class="as3-lbl-desc">' + desc + '</span>';
      labelEl.innerHTML = html;
      var optSw = opt.querySelector('.as3-opt-sw');
      if (swatchEl && optSw) swatchEl.style.background = optSw.style.background;
      close();
      trigger.focus();
    }

    trigger.addEventListener('click', toggle);

    trigger.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
      if (e.key === 'Escape') close();
    });

    search.addEventListener('input', filter);

    search.addEventListener('keydown', function(e) {
      var visOpts = Array.from(opts).filter(function(o) { return !o.hidden; });
      var focused = visOpts.find(function(o) { return o.classList.contains('is-focused'); });
      var idx = visOpts.indexOf(focused);

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        visOpts.forEach(function(o) { o.classList.remove('is-focused'); });
        idx = idx < visOpts.length - 1 ? idx + 1 : 0;
        visOpts[idx].classList.add('is-focused');
        visOpts[idx].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        visOpts.forEach(function(o) { o.classList.remove('is-focused'); });
        idx = idx > 0 ? idx - 1 : visOpts.length - 1;
        visOpts[idx].classList.add('is-focused');
        visOpts[idx].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (focused) select(focused);
      } else if (e.key === 'Escape') {
        close();
        trigger.focus();
      }
    });

    opts.forEach(function(opt) {
      opt.addEventListener('click', function() { select(opt); });
      opt.addEventListener('mouseenter', function() {
        opts.forEach(function(o) { o.classList.remove('is-focused'); });
        opt.classList.add('is-focused');
      });
    });

    onOutside(root, close);
  });

});

/**
 * signing-doc-refined.js — Apollo Sign · Placement + Interaction
 * ═══════════════════════════════════════════════════════════════════
 *
 *  Registry  : _inventory/apollo-registry.json → plugins.apollo-sign
 *  Plugin    : Apollo\Sign  (L5_documents)
 *  Namespace : Apollo\Sign
 *  Requires  : page-layout.js (ApolloSlider v2 CDN), GSAP 3.13+, interact.js
 *
 *  PHP target files (production):
 *    assets/js/sign-placement.js   ← this file adapted for WP
 *    assets/js/signature-pad.js    ← hand-drawn pad (separate)
 *    assets/css/sign-placement.css ← companion styles
 *    assets/css/signature-pad.css  ← pad styles
 *
 *  State machine (registry: apollo-sign.state_machine):
 *    IDLE ──(btn-iniciar)──► PLACING ──(tap×2)──► PLACED
 *    PLACED ──(drag/resize)──► PLACED  (fractions auto-update)
 *    PLACED ──(Assinar + agree)──► SIGNED ──(ApolloSlider)──► Panel done
 *
 *  Coordinate system (registry: apollo-sign.coordinate_system):
 *    Frontend : Canvas px, origin top-left
 *    Storage  : Fractions 0.0–1.0 in apollo_signatures (sig_x..sig_h)
 *    Backend  : PDF points 72pt/in, origin bottom-left, Y inverted
 *
 *  REST (apollo/v1) — all from registry apollo-sign.rest:
 *    POST /signatures                    — create signature request
 *    GET  /signatures/{id}               — get signature (public)
 *    POST /signatures/{id}/sign          — sign with PFX certificate
 *    GET  /signatures/{id}/audit         — audit trail (auth)
 *    POST /signatures/{id}/placement     — save placement coords
 *    GET  /signatures/{id}/placement     — get saved placement
 *    GET  /signatures/verify/{hash}      — public hash verification
 *    POST /signatures/request            — multi-signer workflow
 *    GET  /signatures/users              — list users for signer dropdown
 *    GET  /signatures/doc/{doc_id}       — signer queue + status
 *
 *  Tables (registry: apollo-sign.tables):
 *    apollo_signatures      — sig_x, sig_y, sig_w, sig_h DECIMAL(5,4),
 *                             sig_page SMALLINT, placement_mode VARCHAR(20),
 *                             signature_image_path, stamp_path VARCHAR(512)
 *    apollo_signature_audit — immutable event log (hash, ip_hash, action)
 *
 *  wp_localize_script → window.apolloSign:
 *    { docId, docHash, docTitle, nonce, restBase, sigId, placement, cdnUrl }
 *
 *  Hooks fired: apollo/sign/created, apollo/sign/signed, apollo/sign/verified
 *
 * ═══════════════════════════════════════════════════════════════════
 */

document.addEventListener('DOMContentLoaded', function () {

  /* ═══════════════════════════════════════════════════════════════
     STAMP CONSTANTS
     Natural stamp width: 380px (fixed by design).
     Min resize = 70% of natural. Natural height measured at first render.
     Registry: apollo-sign → provides: visual-stamp
  ═══════════════════════════════════════════════════════════════ */
  var STAMP_NW  = 380;
  var STAMP_MIN = 0.70;
  var MIN_W = Math.round(STAMP_NW * STAMP_MIN); /* 266 */
  var MIN_H = 112;
  var stampNaturalH = 0;
  var STATE = 'IDLE', P1 = null;
  var currentRect = { x: 0, y: 0, w: MIN_W, h: MIN_H };
  var rectPlaced = false, interactInst = null, docZoom = 1;
  var pinchActive = false;


  /* ═══════════════════════════════════════════════════════════════
     DOM REFERENCES
     No identity form fields — user resolved server-side via
     get_current_user_id() → apolloSign.userName (wp_localize_script)
  ═══════════════════════════════════════════════════════════════ */
  var pdfC      = document.getElementById('pdf-p2');
  var overlay   = document.getElementById('sign-overlay');
  var ovBg      = document.getElementById('ov-bg');
  var ovCard    = document.getElementById('ov-card');
  var touchOv   = document.getElementById('touch-ov');
  var ghostSvg  = document.getElementById('ghost-svg');
  var signRect  = document.getElementById('sign-rect');
  var rectLbl   = document.getElementById('rect-lbl');
  var rectDim   = document.getElementById('rect-dim');
  var btnPos    = document.getElementById('btn-pos');
  var btnPosLbl = document.getElementById('btn-pos-lbl');
  var chkAg     = document.getElementById('chk-agree');
  var btnSign   = document.getElementById('btn-assinar');
  var minWarn   = document.getElementById('min-warn');


  /* ═══════════════════════════════════════════════════════════════
     OVERLAY GSAP ANIMATIONS
     #ov-card compact design (230px, sim-* CSS keyframes)
     Ported from sign-doc v2 (design apollo/sign-doc.css)
  ═══════════════════════════════════════════════════════════════ */
  function dismissOverlay(cb) {
    if (typeof gsap !== 'undefined') {
      var tl = gsap.timeline({
        onComplete: function () {
          overlay.style.display = 'none';
          if (cb) cb();
        }
      });
      tl.to(ovCard, { scale: .86, opacity: 0, duration: .2, ease: 'power2.in' })
        .to(ovBg,   { opacity: 0, duration: .28 }, 0.06)
        .to(overlay, { opacity: 0, duration: .14 }, 0.24);
    } else {
      overlay.style.display = 'none';
      if (cb) cb();
    }
  }

  function openOverlay() {
    overlay.style.cssText = 'display:flex; opacity:1;';
    if (typeof gsap !== 'undefined') {
      gsap.fromTo(ovCard, { scale: .82, opacity: 0 }, { scale: 1, opacity: 1, duration: .34, ease: 'back.out(2.4)' });
      gsap.fromTo(ovBg,   { opacity: 0 },             { opacity: 1, duration: .22 });
    }
  }


  /* ═══════════════════════════════════════════════════════════════
     BUTTON HANDLERS — OVERLAY TRIGGERS
     No requireFields gate — identity fields removed.
     Direct dismissOverlay → startManual / autoPlace.
  ═══════════════════════════════════════════════════════════════ */
  document.getElementById('btn-iniciar').addEventListener('click', function () {
    dismissOverlay(startManual);
  });

  document.getElementById('btn-auto').addEventListener('click', function () {
    dismissOverlay(autoPlace);
  });

  btnPos.addEventListener('click', function () {
    if (!rectPlaced) openOverlay();
  });


  /* ═══════════════════════════════════════════════════════════════
     MANUAL PLACEMENT — TWO-POINT TAP
     State: IDLE → V1 (1st tap) → DONE (2nd tap → rect committed)
     Registry ref: apollo-sign.state_machine.IDLE → PLACING
  ═══════════════════════════════════════════════════════════════ */
  function startManual() {
    STATE = 'IDLE'; P1 = null; ghostSvg.innerHTML = '';
    signRect.style.display = 'none';
    touchOv.classList.add('active');
    btnPos.classList.remove('placed');
    btnPos.classList.add('placing');
    btnPosLbl.textContent = '1º ponto…';
    var h = document.getElementById('place-hint');
    if (h) {
      h.style.display = 'block';
      h.innerHTML = 'Toque no 1º ponto<br/><span style="font-size:9px;opacity:.6;">depois no 2º para definir a área</span>';
    }
    /* collapse sign-bottom on mobile to clear view */
    if (!sbOpen) return;
    sbOpen = false;
    setSbHeight();
  }

  function getPt(e) {
    var r  = pdfC.getBoundingClientRect();
    var scaleX = r.width  / (pdfC.offsetWidth  || 794);
    var scaleY = r.height / (pdfC.offsetHeight || 1123);
    var cx = e.clientX != null ? e.clientX : (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
    var cy = e.clientY != null ? e.clientY : (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
    return {
      x: Math.max(0, Math.min((cx - r.left) / scaleX, pdfC.offsetWidth)),
      y: Math.max(0, Math.min((cy - r.top)  / scaleY, pdfC.offsetHeight))
    };
  }

  function onTap(e) {
    if (!touchOv.classList.contains('active')) return;
    if (e.cancelable) e.preventDefault();
    var h = document.getElementById('place-hint');
    var p = getPt(e);
    if (STATE === 'IDLE') {
      P1 = p; STATE = 'V1';
      drawP1(p);
      btnPosLbl.textContent = '2º ponto…';
    } else if (STATE === 'V1') {
      if (h) h.style.display = 'none';
      var d = Math.hypot(p.x - P1.x, p.y - P1.y);
      var P2 = d < 20 ? { x: P1.x + MIN_W, y: P1.y + MIN_H } : p;
      ghostSvg.innerHTML = '';
      commitRect(buildRect(P1, P2));
      STATE = 'DONE';
      touchOv.classList.remove('active');
      reInitInteract();
      markPlaced();
      if (!sbOpen) { sbOpen = true; setSbHeight(); }
    }
  }

  touchOv.addEventListener('touchstart', onTap, { passive: false });
  touchOv.addEventListener('click', onTap);
  touchOv.addEventListener('touchmove', function (e) {
    if (STATE !== 'V1' || !P1) return;
    if (e.cancelable) e.preventDefault();
    drawLive(P1, getPt(e));
  }, { passive: false });
  touchOv.addEventListener('mousemove', function (e) {
    if (STATE === 'IDLE' && touchOv.classList.contains('active')) { drawCross(getPt(e)); return; }
    if (STATE === 'V1' && P1) drawLive(P1, getPt(e));
  });
  touchOv.addEventListener('mouseleave', function () {
    if (STATE === 'IDLE') ghostSvg.innerHTML = '';
  });


  /* ═══════════════════════════════════════════════════════════════
     SVG DRAWING HELPERS
     Ghost overlay: crosshair, P1 marker, live rect preview
  ═══════════════════════════════════════════════════════════════ */
  function svgEl(tag, attrs) {
    var el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.keys(attrs).forEach(function (k) { el.setAttribute(k, attrs[k]); });
    ghostSvg.appendChild(el);
    return el;
  }

  function drawCross(p) {
    ghostSvg.innerHTML = '';
    var cw = 794, ch = 1123;
    var s = 'rgba(244,95,0,.55)';
    svgEl('line', { x1: 0, y1: p.y, x2: cw, y2: p.y, stroke: s, 'stroke-width': 1.5, 'stroke-dasharray': '6,4' });
    svgEl('line', { x1: p.x, y1: 0, x2: p.x, y2: ch, stroke: s, 'stroke-width': 1.5, 'stroke-dasharray': '6,4' });
    svgEl('circle', { cx: p.x, cy: p.y, r: 6, fill: '#f45f00', opacity: .9 });
  }

  function drawP1(p) {
    ghostSvg.innerHTML = '';
    var cw = 794, ch = 1123;
    var s = 'rgba(244,95,0,.70)';
    svgEl('line', { x1: 0, y1: p.y, x2: cw, y2: p.y, stroke: s, 'stroke-width': 2, 'stroke-dasharray': '6,4' });
    svgEl('line', { x1: p.x, y1: 0, x2: p.x, y2: ch, stroke: s, 'stroke-width': 2, 'stroke-dasharray': '6,4' });
    svgEl('circle', { cx: p.x, cy: p.y, r: 8, fill: '#f45f00', opacity: 1 });
    svgEl('circle', { cx: p.x, cy: p.y, r: 4, fill: '#fff', opacity: 1 });
    var h = document.getElementById('place-hint');
    if (h) h.innerHTML = 'Toque no 2º ponto<br/><span style="font-size:9px;opacity:.6;">ponto oposto da área</span>';
  }

  function drawLive(p1, p2) {
    ghostSvg.innerHTML = '';
    var x = Math.min(p1.x, p2.x), y = Math.min(p1.y, p2.y);
    var w = Math.max(Math.abs(p2.x - p1.x), 2), h = Math.max(Math.abs(p2.y - p1.y), 2);
    svgEl('circle', { cx: p1.x, cy: p1.y, r: 8, fill: '#f45f00', opacity: 1 });
    svgEl('circle', { cx: p1.x, cy: p1.y, r: 4, fill: '#fff', opacity: 1 });
    svgEl('rect', {
      x: x, y: y, width: w, height: h,
      fill: 'rgba(244,95,0,.08)', stroke: '#f45f00',
      'stroke-width': 2, 'stroke-dasharray': '6,4'
    });
    svgEl('circle', { cx: p2.x, cy: p2.y, r: 6, fill: '#f45f00', opacity: .7 });
    var t = svgEl('text', {
      x: x + w / 2, y: Math.max(y - 8, 14),
      'text-anchor': 'middle',
      'font-family': 'Space Mono,monospace',
      'font-size': '11', 'font-weight': '700',
      fill: '#f45f00', opacity: .95
    });
    t.textContent = Math.round(w) + '\u00d7' + Math.round(h) + 'px';
  }


  /* ═══════════════════════════════════════════════════════════════
     RECT OPERATIONS
     buildRect  → clamp to paper bounds
     commitRect → apply to DOM + GSAP entrance
     Coords stored in fractions via rectToFraction() for REST POST
  ═══════════════════════════════════════════════════════════════ */
  function buildRect(p1, p2) {
    var cw = pdfC.offsetWidth, ch = pdfC.offsetHeight;
    var x = Math.min(p1.x, p2.x), y = Math.min(p1.y, p2.y);
    var w = Math.max(Math.abs(p2.x - p1.x), MIN_W), h = Math.max(Math.abs(p2.y - p1.y), MIN_H);
    return { x: x, y: y, w: Math.min(w, cw - x), h: Math.min(h, ch - y) };
  }

  function commitRect(r) {
    currentRect = Object.assign({}, r);
    Object.assign(signRect.style, {
      display: 'block', left: r.x + 'px', top: r.y + 'px',
      width: r.w + 'px', height: r.h + 'px', transform: 'none'
    });
    signRect.dataset.x = 0;
    signRect.dataset.y = 0;
    updateDim(r.w, r.h);
    if (typeof gsap !== 'undefined') {
      gsap.fromTo(signRect, { opacity: 0, scale: .82 }, { opacity: 1, scale: 1, duration: .28, ease: 'back.out(2)' });
    }
  }

  function updateDim(w, h) {
    if (rectDim) rectDim.textContent = Math.round(w) + '\u00d7' + Math.round(h) + 'px';
  }

  /**
   * rectToFraction — converts currentRect px → 0.0–1.0 fractions
   * Registry: apollo-sign.coordinate_system.storage
   * DB: apollo_signatures.sig_x, sig_y, sig_w, sig_h DECIMAL(5,4)
   */
  function rectToFraction() {
    var cw = pdfC.offsetWidth  || 794;
    var ch = pdfC.offsetHeight || 1123;
    return {
      sig_x: Math.round(Math.max(0, Math.min(1, currentRect.x / cw)) * 10000) / 10000,
      sig_y: Math.round(Math.max(0, Math.min(1, currentRect.y / ch)) * 10000) / 10000,
      sig_w: Math.round(Math.max(0, Math.min(1, currentRect.w / cw)) * 10000) / 10000,
      sig_h: Math.round(Math.max(0, Math.min(1, currentRect.h / ch)) * 10000) / 10000
    };
  }


  /* ═══════════════════════════════════════════════════════════════
     AUTO PLACEMENT — FOOTER
     Registry: placement_mode = 'auto_footer'
  ═══════════════════════════════════════════════════════════════ */
  function autoPlace() {
    var cw = pdfC.offsetWidth, ch = pdfC.offsetHeight;
    var w = Math.min(380, cw * .48), h = MIN_H * 2;
    commitRect({ x: cw - w - 80, y: ch - h - 90, w: w, h: h });
    STATE = 'DONE';
    reInitInteract();
    markPlaced();
  }

  function markPlaced() {
    rectPlaced = true;
    btnPos.classList.remove('placing');
    btnPos.classList.add('placed');
    btnPosLbl.textContent = '\u2726 Posicionado';
    if (rectDim) rectDim.textContent = 'arraste \u00b7 resize';
    checkState();
  }

  function syncRectData(el) {
    var bx = parseFloat(el.style.left) || 0, by = parseFloat(el.style.top) || 0;
    var tx = parseFloat(el.dataset.x) || 0,  ty = parseFloat(el.dataset.y) || 0;
    currentRect = { x: bx + tx, y: by + ty, w: parseFloat(el.style.width), h: parseFloat(el.style.height) };
    updateDim(currentRect.w, currentRect.h);
  }

  /* ── min-warn toast ── */
  var warnTm;
  function showWarn() {
    clearTimeout(warnTm);
    minWarn.classList.add('vis');
    warnTm = setTimeout(function () { minWarn.classList.remove('vis'); }, 2200);
  }

  /* ── nudge (toolbar +/− buttons or scroll-zoom) ── */
  function nudge(dw, dh) {
    if (!rectPlaced) {
      if (window.innerWidth < 861) return;
      if (pinchActive) return; // Evita conflito com pinch-to-zoom
      docZoom = Math.max(.6, Math.min(1.6, docZoom + dw * .007));
      pdfC.style.transform = 'scale(' + docZoom + ')';
      return;
    }
    var nw = (parseFloat(signRect.style.width)  || currentRect.w) + dw;
    var nh = (parseFloat(signRect.style.height) || currentRect.h) + dh;
    if (nw < MIN_W || nh < MIN_H) { showWarn(); return; }
    signRect.style.width  = nw + 'px';
    signRect.style.height = nh + 'px';
    currentRect.w = nw;
    currentRect.h = nh;
    updateDim(nw, nh);
  }

  document.getElementById('btn-plus').addEventListener('click', function (e) { e.stopPropagation(); nudge(16, 7); });
  document.getElementById('btn-minus').addEventListener('click', function (e) { e.stopPropagation(); nudge(-16, -7); });

  var btnRestartPos = document.getElementById('btn-restart-pos');
  if (btnRestartPos) {
    btnRestartPos.addEventListener('click', function (e) {
      e.stopPropagation();
      resetSig();
      openOverlay();
    });
  }


  /* ═══════════════════════════════════════════════════════════════
     SIGN-BOTTOM TOGGLE — collapsible action bar
  ═══════════════════════════════════════════════════════════════ */
  var sbBottom = document.getElementById('sign-bottom');
  var sbToggle = document.getElementById('sbottom-toggle');
  var sbOpen   = true;

  function setSbHeight() {
    if (!sbOpen) {
      sbBottom.classList.add('collapsed');
      sbToggle.classList.add('collapsed-indicator');
    } else {
      sbBottom.classList.remove('collapsed');
      sbToggle.classList.remove('collapsed-indicator');
    }
  }

  if (sbToggle) {
    sbToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      sbOpen = !sbOpen;
      setSbHeight();
    });
  }


  /* ═══════════════════════════════════════════════════════════════
     RESET SIGNATURE
     btn-reset-sig removed from UI (per design update).
     resetSig() still called by btn-restart-pos toolbar button.
  ═══════════════════════════════════════════════════════════════ */
  function resetSig() {
    if (interactInst) { interactInst.unset(); interactInst = null; }
    signRect.style.display = 'none';
    signRect.classList.remove('confirmed');
    rectLbl.classList.remove('ok');
    rectDim.classList.remove('ok');
    ghostSvg.innerHTML = '';
    touchOv.classList.remove('active');
    STATE = 'IDLE'; P1 = null; rectPlaced = false; docZoom = 1;
    pdfC.style.transform = '';
    scalePapers();
    btnPos.classList.remove('placed', 'placing');
    btnPosLbl.textContent = 'Posicionar';
    var h = document.getElementById('place-hint');
    if (h) h.style.display = 'none';
    if (!sbOpen) { sbOpen = true; setSbHeight(); }
    checkState();
  }

  function checkState() { btnSign.disabled = !(chkAg.checked && rectPlaced); }
  chkAg.addEventListener('change', checkState);

  function genHash() {
    return Array.from({ length: 64 }, function () {
      return Math.floor(Math.random() * 16).toString(16);
    }).join('');
  }


  /* ═══════════════════════════════════════════════════════════════
     TITAN STAMP BUILDER
     Registry: apollo-sign → provides: visual-stamp
     PHP: class StampGenerator (GD) — this is the frontend preview.
     Production: stamp_path stored in apollo_signatures.stamp_path
  ═══════════════════════════════════════════════════════════════ */
  var WATERMARK_PATH = 'M10.208521 14.887119l2.975605 0c2.878458 0 4.409489 0.720844 6.359568 2.627895c0.108236 0.105828 0.12279 0.278202 0.020394 0.389215l-2.484774 2.694557c-0.102376 0.110992 -0.286064 0.130942 -0.389215 0.020407l-3.228883 -3.454773 0 4.553169c0 0.151587 -0.124013 0.2756 -0.2756 0.2756l-2.975605 0c-0.151574 0 -0.2756 -0.124013 -0.2756 -0.2756l0 -6.554906c0 -0.151574 0.124026 -0.275587 0.2756 -0.275587zM9.106143 10.203854l0 2.975605c0 2.878458 -0.720832 4.409489 -2.627895 6.359581c-0.105828 0.108223 -0.278215 0.122777 -0.389215 0.020381l-2.69457 -2.484774c-0.110979 -0.102376 -0.130929 -0.286064 -0.020407 -0.389215l3.454773 -3.228883 -4.553169 0c-0.151574 0 -0.275587 -0.124026 -0.275587 -0.275613l0 -2.975592c0 -0.151587 0.124013 -0.2756 0.275587 -0.2756l6.554906 0c0.151574 0 0.2756 0.124013 0.2756 0.2756zM13.791479 9.115478l-2.975592 0c-2.878458 0 -4.409489 -0.720844 -6.359581 -2.627895c-0.108223 -0.105828 -0.122777 -0.278202 -0.020394 -0.389215l2.484774 -2.694557c0.102363 -0.110979 0.286051 -0.130929 0.389215 -0.020407l3.228883 3.454773 0 -4.553169c0 -0.151574 0.124026 -0.275587 0.2756 -0.275587l2.975592 0c0.151587 0 0.275613 0.124013 0.275613 0.275587l0 6.554906c0 0.151587 -0.124026 0.275613 -0.275613 0.275613zM14.893857 13.783143l0 -2.975592c0 -2.878458 0.720832 -4.409489 2.627895 -6.359581c0.105828 -0.108236 0.278202 -0.12279 0.389215 -0.020394l2.694557 2.484774c0.110992 0.102376 0.130942 0.286064 0.02042 0.389215l-3.454786 3.228883 4.553182 0c0.151574 0 0.275587 0.124026 0.275587 0.275613l0 2.975592c0 0.151587 -0.124013 0.2756 -0.275587 0.2756l-6.554906 0c-0.151574 0 -0.275587 -0.124013 -0.275587 -0.2756z';

  function buildTitanStamp(userName, date, hash) {
    return '<div class="ts-wrap">'
      + '<svg class="ts-watermark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="0.5" stroke-linejoin="round"><path d="' + WATERMARK_PATH + '"/></svg>'
      + '<div class="ts-header">ASSINATURA DIGITAL \u2022 APOLLO.RIO.BR</div>'
      + '<div class="ts-label">Signat\u00e1rio</div>'
      + '<div class="ts-name">' + userName + '</div>'
      + '<div class="ts-meta"><span class="ts-label">Documento</span><span class="ts-mono">***.XXX.XXX-**</span></div>'
      + '<div class="ts-meta"><span class="ts-label">Registro (ACT)</span><span class="ts-mono">' + date + '</span></div>'
      + '<div class="ts-hash"><b>HASH</b> <span>' + hash + '</span></div>'
      + '</div>';
  }

  /**
   * injectStamp — CONTAIN mode: stamp fits rect exactly, nothing clipped.
   * 1. Render at natural 380px to measure real height
   * 2. containScale = min(availW/380, availH/naturalH)
   * 3. Clamp: floor=70% natural, ceil=uncapped
   * 4. Resize containerEl to finalW×finalH so rect = stamp exactly
   * Returns { w, h, scale } for caller to update currentRect / done-sig-rect
   */
  function injectStamp(containerEl, availW, availH, userName, date, hash) {
    /* step 1: inject at natural size to measure */
    containerEl.innerHTML = buildTitanStamp(userName, date, hash);
    var wrap = containerEl.querySelector('.ts-wrap');
    wrap.style.cssText += ';transform:none !important;width:' + STAMP_NW + 'px;';

    /* step 2: measure natural height */
    var natH = wrap.offsetHeight;
    if (!natH || natH < 10) natH = 170;
    stampNaturalH = natH;
    MIN_H = Math.round(natH * STAMP_MIN);

    /* step 3: contain scale */
    var scaleW = availW / STAMP_NW;
    var scaleH = availH / natH;
    var scale  = Math.min(scaleW, scaleH);
    scale = Math.max(scale, STAMP_MIN);

    /* step 4: apply */
    wrap.style.transform       = 'scale(' + scale + ')';
    wrap.style.transformOrigin = 'top left';

    /* step 5: resize container */
    var finalW = Math.round(STAMP_NW * scale);
    var finalH = Math.round(natH    * scale);
    containerEl.style.width  = finalW + 'px';
    containerEl.style.height = finalH + 'px';

    return { w: finalW, h: finalH, scale: scale };
  }


  /* ═══════════════════════════════════════════════════════════════
     INTERACT.JS — DRAG + RESIZE
     Re-init with updated MIN_W / MIN_H after stamp is placed.
     Registry: apollo-sign → cdn_deps: interact.min.js
  ═══════════════════════════════════════════════════════════════ */
  function reInitInteract() {
    if (interactInst) { interactInst.unset(); interactInst = null; }
    if (typeof interact === 'undefined') return;
    interactInst = interact('#sign-rect')
      .draggable({
        listeners: {
          move: function (ev) {
            var el = ev.target;
            var x = (parseFloat(el.dataset.x) || 0) + ev.dx;
            var y = (parseFloat(el.dataset.y) || 0) + ev.dy;
            el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
            el.dataset.x = x;
            el.dataset.y = y;
            syncRectData(el);
          }
        },
        modifiers: [interact.modifiers.restrictRect({ restriction: '#pdf-p2', endOnly: true })]
      })
      .resizable({
        edges: { left: true, right: true, bottom: true, top: true },
        listeners: {
          move: function (ev) {
            var el = ev.target;
            if (ev.rect.width < MIN_W || ev.rect.height < MIN_H) { showWarn(); return; }
            el.style.width  = ev.rect.width  + 'px';
            el.style.height = ev.rect.height + 'px';
            var x = (parseFloat(el.dataset.x) || 0) + ev.deltaRect.left;
            var y = (parseFloat(el.dataset.y) || 0) + ev.deltaRect.top;
            el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
            el.dataset.x = x;
            el.dataset.y = y;
            syncRectData(el);
          }
        },
        modifiers: [interact.modifiers.restrictSize({ min: { width: MIN_W, height: MIN_H } })]
      });
  }


  /* ═══════════════════════════════════════════════════════════════
     SIGN BUTTON — MAIN HANDLER
     ─────────────────────────────────────────────────────────────
     REST target: POST apollo/v1/signatures/{id}/sign
     Tables: apollo_signatures, apollo_signature_audit
     Hooks: apollo/sign/signed ($sig_id, $hash)
     ─────────────────────────────────────────────────────────────
     Prototype: fakes hash + date inline.
     Production: POST to restBase + '/signatures/' + sigId + '/sign'
     with nonce header X-WP-Nonce and body { placement: rectToFraction() }
  ═══════════════════════════════════════════════════════════════ */
  btnSign.addEventListener('click', function () {
    if (btnSign.disabled) return;
    btnSign.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Processando...';
    btnSign.style.opacity = '.7';

    setTimeout(function () {
      var hash = genHash();
      var date = new Date().toLocaleString('pt-BR');

      /**
       * userName: prototype stub — hardcoded 'GESTOR'.
       * Production: resolved server-side via get_current_user_id()
       * and passed through wp_localize_script → apolloSign.userName
       */
      var userName = 'GESTOR';

      /* ── Panel 2: inject stamp with CONTAIN scale ──
         injectStamp returns { w, h } = final rendered dimensions.
         sign-rect must snap to exactly those dimensions. */
      signRect.classList.add('confirmed');
      var inner = signRect.querySelector('.rect-inner');
      var stamped = injectStamp(inner, currentRect.w, currentRect.h, userName, date, hash);

      /* snap sign-rect to exact stamp footprint */
      signRect.style.width  = stamped.w + 'px';
      signRect.style.height = stamped.h + 'px';
      currentRect.w = stamped.w;
      currentRect.h = stamped.h;
      updateDim(stamped.w, stamped.h);

      /* re-init interact with 70% floor from now-known natural height */
      reInitInteract();

      /* ── Panel 2 paper sig-line ── */
      document.getElementById('sig-stamp').style.opacity = '1';
      document.getElementById('user-sig-line').innerHTML =
        '<div style="font-size:11px;font-weight:700;color:var(--live);font-family:var(--mono);text-transform:uppercase;letter-spacing:.04em;">' + userName + '</div>';

      var userSigCpf = document.getElementById('user-sig-cpf');
      if (userSigCpf) userSigCpf.textContent = 'CPF: ***.XXX.XXX-**';

      document.getElementById('user-sig-date').textContent = date;
      document.getElementById('doc-hash-prev').textContent = hash.substring(0, 10) + '\u2026';
      document.getElementById('doc-hash-prev').style.color = 'var(--live)';

      /* ── pills → signed ── */
      ['pill-p1', 'pill-p2'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.className = 'pill signed';
        el.innerHTML = '<i class="ri-check-line"></i> Assinado';
      });

      /* ═══════════════════════════════════════════════════════
         PANEL 3 — DONE
         Registry: apollo-sign.template_parts → parts/signed-info.php
         Mobile: done-info shows first (aside with badge, participants,
         share, download). Button "Ver Arquivo Assinado" slides to
         done-doc (paper preview). Desktop: both visible side-by-side.
      ═══════════════════════════════════════════════════════ */
      document.getElementById('done-id-line').textContent = 'APOLLO-2025-X992 \u00b7 ' + date;
      document.getElementById('done-part-name').textContent = userName;
      document.getElementById('done-sig-name').textContent = userName;
      document.getElementById('done-sig-name').style.color = 'var(--live)';

      var doneSigCpf = document.getElementById('done-sig-cpf');
      if (doneSigCpf) {
        doneSigCpf.textContent = 'CPF: ***.XXX.XXX-**';
        doneSigCpf.style.color = 'var(--live)';
      }

      document.getElementById('done-sig-date').textContent = date;
      document.getElementById('sig-stamp-p3').style.opacity = '1';
      document.getElementById('done-hash-short').textContent = hash.substring(0, 14) + '\u2026';
      document.getElementById('done-hash-full').textContent = hash;

      /* ── Panel 3: done-sig-rect mirrors sign-rect at paper scale 0.76 ── */
      var PAPER_SCALE = 0.76;
      var sr = document.getElementById('done-sig-rect');
      sr.style.left    = (currentRect.x * PAPER_SCALE) + 'px';
      sr.style.top     = (currentRect.y * PAPER_SCALE) + 'px';
      var srW = stamped.w * PAPER_SCALE;
      var srH = stamped.h * PAPER_SCALE;
      sr.style.width   = srW + 'px';
      sr.style.height  = srH + 'px';
      sr.style.display = 'block';
      injectStamp(sr, srW, srH, userName, date, hash);

      confetti();

      /* ── navigate to done panel via ApolloSlider (page-layout.js) ── */
      setTimeout(function () {
        if (window.ApolloSlider) window.ApolloSlider.navigate('done', 'right');

        /* GSAP stagger entrance for done panel elements */
        if (typeof gsap !== 'undefined') {
          gsap.fromTo('.done-badge',
            { opacity: 0, y: 8 },
            { opacity: 1, y: 0, duration: .44, ease: 'back.out(1.8)', delay: .2 });
          gsap.fromTo('.done-aside .aside-card',
            { opacity: 0, y: 10 },
            { opacity: 1, y: 0, stagger: .08, duration: .38, ease: 'power3.out', delay: .3 });
          gsap.fromTo('#sig-stamp-p3',
            { opacity: 0, rotate: -14 },
            { opacity: 1, rotate: -8, duration: .44, ease: 'back.out(2)', delay: .5 });
          gsap.fromTo('#done-sig-rect',
            { opacity: 0, scale: .88 },
            { opacity: 1, scale: 1, duration: .44, ease: 'back.out(1.6)', delay: .4, transformOrigin: 'top left' });
        }
      }, 580);

    }, 1400);
  });


  /* ═══════════════════════════════════════════════════════════════
     DONE PANEL — MOBILE SUB-VIEW NAVIGATION
     ─────────────────────────────────────────────────────────────
     Mobile (<= 860px): done-info first, btn-preview-doc slides
     to done-doc (paper preview). done-doc-back returns.
     Desktop (>= 861px): both visible, buttons hidden via CSS.
     CSS classes: .done-info.out, .done-doc.in
     Registry: apollo-sign → parts/signed-info.php
  ═══════════════════════════════════════════════════════════════ */
  var doneDoc     = document.getElementById('done-doc');
  var doneInfo    = document.getElementById('done-info');
  var btnPreview  = document.getElementById('btn-preview-doc');
  var doneDocBack = document.getElementById('done-doc-back');

  function showDoneDoc() {
    if (window.innerWidth >= 861) return;
    doneInfo.classList.add('out');
    doneDoc.classList.add('in');
  }

  function showDoneInfo() {
    doneInfo.classList.remove('out');
    doneDoc.classList.remove('in');
  }

  if (btnPreview) {
    btnPreview.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      showDoneDoc();
    });
  }

  if (doneDocBack) {
    doneDocBack.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      showDoneInfo();
    });
  }


  /* ═══════════════════════════════════════════════════════════════
     SEND / SHARE HANDLERS
     ─────────────────────────────────────────────────────────────
     btn-send-email → opens mailto or future REST POST to
       apollo/v1/signatures/request (multi-signer workflow)
     btn-send-sign → initiate multi-signer workflow
     Registry: POST /signatures/request, GET /signatures/users
  ═══════════════════════════════════════════════════════════════ */
  var btnSendEmail = document.getElementById('btn-send-email');
  if (btnSendEmail) {
    btnSendEmail.addEventListener('click', function () {
      /**
       * Prototype: opens mailto with doc reference.
       * Production: opens email compose modal OR calls
       * REST POST /signatures/request with recipient list.
       */
      window.location.href = 'mailto:?subject=Documento%20Assinado%20APOLLO-2025-X992&body=Segue%20o%20documento%20assinado%20digitalmente.%0A%0AVerifique%20em%3A%20apollo.rio.br%2Fdocs%2Fverify';
    });
  }

  var btnSendSign = document.getElementById('btn-send-sign');
  if (btnSendSign) {
    btnSendSign.addEventListener('click', function () {
      /**
       * Prototype: alert placeholder.
       * Production: opens user search modal (GET /signatures/users)
       * then POST /signatures/request to initiate multi-signer workflow.
       * Creates sig record, saves _doc_signers queue, sends invite email.
       */
      alert('Funcionalidade: Solicitar assinatura de outra parte.\n\nEm produ\u00e7\u00e3o, abre modal de busca de usu\u00e1rios (GET apollo/v1/signatures/users) e inicia workflow multi-assinatura.');
    });
  }


  /* ═══════════════════════════════════════════════════════════════
     DOWNLOAD BUTTON
  ═══════════════════════════════════════════════════════════════ */
  var btnDownload = document.getElementById('btn-download');
  if (btnDownload) {
    btnDownload.addEventListener('click', function () {
      btnDownload.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Gerando PDF...';
      setTimeout(function () {
        btnDownload.innerHTML = '<i class="ri-check-line"></i> Download Conclu\u00eddo';
      }, 1800);
    });
  }


  /* ═══════════════════════════════════════════════════════════════
     MOBILE PAPER SCALER
     Scales .paper-sheet elements to fit viewport on mobile.
     Uses CSS custom props --safe-left, --safe-right from
     page-layout.js structural CSS (safe-area insets).
  ═══════════════════════════════════════════════════════════════ */
  function scalePapers() {
    var vw = (window.visualViewport ? window.visualViewport.width : window.innerWidth);

    if (vw >= 861) {
      document.querySelectorAll('.paper-sheet').forEach(function (s) {
        s.style.willChange   = '';
        s.style.transform    = '';
        s.style.marginBottom = '';
      });
      return;
    }

    var cs    = getComputedStyle(document.documentElement);
    var safeL = parseFloat(cs.getPropertyValue('--safe-left'))  || 0;
    var safeR = parseFloat(cs.getPropertyValue('--safe-right')) || 0;
    var pad   = 18;
    var avail = vw - (pad * 2) - safeL - safeR;
    var scale = avail / 794;
    var a4h   = 1123;

    document.querySelectorAll('.paper-sheet').forEach(function (s) {
      s.style.willChange      = 'transform';
      s.style.transform       = 'scale(' + scale + ')';
      s.style.transformOrigin = 'top left';
      s.style.marginBottom    = Math.round(a4h * scale - a4h) + 'px';
    });

    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        document.querySelectorAll('.paper-sheet').forEach(function (s) {
          s.style.willChange = 'auto';
        });
      });
    });
  }

  scalePapers();
  window.addEventListener('resize', scalePapers);
  if (window.visualViewport) window.visualViewport.addEventListener('resize', scalePapers);


  /* ═══════════════════════════════════════════════════════════════
     PINCH-TO-ZOOM — WeakMap per-paper tracking
     Applies to done-doc paper + preview paper (Panel 1).
     Touch-only; resets on touchend via scalePapers().
  ═══════════════════════════════════════════════════════════════ */
  (function () {
    var pinchState = null;

    function getDistance(touches) {
      var dx = touches[0].clientX - touches[1].clientX;
      var dy = touches[0].clientY - touches[1].clientY;
      return Math.sqrt(dx * dx + dy * dy);
    }

    function getCenter(touches) {
      return {
        x: (touches[0].clientX + touches[1].clientX) / 2,
        y: (touches[0].clientY + touches[1].clientY) / 2
      };
    }

    function handlePinchStart(e, paper) {
      if (e.touches.length !== 2) return;
      e.preventDefault();
      pinchActive = true;
      var rect = paper.getBoundingClientRect();
      var center = getCenter(e.touches);
      var initialDist = getDistance(e.touches);
      var currentTransform = paper.style.transform || '';
      var currentScale = 1;
      var scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
      if (scaleMatch) currentScale = parseFloat(scaleMatch[1]);
      pinchState = {
        initialDist: initialDist,
        initialScale: currentScale,
        centerX: center.x - rect.left,
        centerY: center.y - rect.top
      };
      paper.style.transformOrigin = pinchState.centerX + 'px ' + pinchState.centerY + 'px';
      paper.style.willChange = 'transform';
    }

    function handlePinchMove(e, paper) {
      if (!pinchState || e.touches.length !== 2) return;
      e.preventDefault();
      var newDist = getDistance(e.touches);
      var scale = Math.max(0.5, Math.min(3.0, pinchState.initialScale * (newDist / pinchState.initialDist)));
      paper.style.transform = 'scale(' + scale + ')';
    }

    function handlePinchEnd(e, paper) {
      if (!pinchState) return;
      pinchState = null;
      pinchActive = false;
      paper.style.willChange = 'auto';
      setTimeout(scalePapers, 100);
    }

    /* done-doc paper */
    var doneDocPaper = document.querySelector('#done-doc .paper-sheet');
    if (doneDocPaper) {
      doneDocPaper.addEventListener('touchstart', function (e) { handlePinchStart(e, this); }, { passive: false });
      doneDocPaper.addEventListener('touchmove',  function (e) { handlePinchMove(e, this); },  { passive: false });
      doneDocPaper.addEventListener('touchend',   function (e) { handlePinchEnd(e, this); },   { passive: true });
    }

    /* preview paper (Panel 1) */
    var previewPaper = document.querySelector('#pdf-p1');
    if (previewPaper) {
      previewPaper.addEventListener('touchstart', function (e) { handlePinchStart(e, this); }, { passive: false });
      previewPaper.addEventListener('touchmove',  function (e) { handlePinchMove(e, this); },  { passive: false });
      previewPaper.addEventListener('touchend',   function (e) { handlePinchEnd(e, this); },   { passive: true });
    }
  })();


  /* ═══════════════════════════════════════════════════════════════
     CANVAS CONFETTI — celebration after signing
     Respects prefers-reduced-motion.
  ═══════════════════════════════════════════════════════════════ */
  function confetti() {
    var REDUCED = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    if (REDUCED) return;

    var canvas = document.createElement('canvas');
    canvas.id = 'confetti-canvas';
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    document.body.appendChild(canvas);
    var ctx = canvas.getContext('2d');

    var COLORS = ['#f45f00', '#16a34a', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6', '#ffffff'];
    var SHAPES = ['rect', 'circle', 'strip'];
    var COUNT  = 180;
    var pieces = [];

    for (var i = 0; i < COUNT; i++) {
      pieces.push({
        x  : Math.random() * canvas.width,
        y  : -20 - Math.random() * canvas.height * 0.6,
        vx : (Math.random() - 0.5) * 5,
        vy : Math.random() * 4 + 2,
        rot: Math.random() * Math.PI * 2,
        rv : (Math.random() - 0.5) * 0.22,
        c  : COLORS[i % COLORS.length],
        s  : SHAPES[Math.floor(Math.random() * SHAPES.length)],
        w  : Math.random() * 10 + 5,
        h  : Math.random() * 6  + 3,
        r  : Math.random() * 5  + 3,
        g  : 0.12 + Math.random() * 0.12,
        wo : (Math.random() - 0.5) * 0.8,
        t  : 0
      });
    }

    var start = performance.now();
    var DURATION = 4200;

    function draw(now) {
      var elapsed  = now - start;
      var progress = Math.min(elapsed / DURATION, 1);
      if (progress >= 1) { canvas.remove(); return; }

      ctx.clearRect(0, 0, canvas.width, canvas.height);
      var fadeStart = 0.65;
      var alpha = progress < fadeStart ? 1 : 1 - (progress - fadeStart) / (1 - fadeStart);

      pieces.forEach(function (p) {
        p.t += 1;
        p.x += p.vx + Math.sin(p.t * 0.04) * p.wo;
        p.y += p.vy;
        p.vy += p.g;
        p.rot += p.rv;

        ctx.save();
        ctx.globalAlpha = alpha * Math.max(0, 1 - p.y / (canvas.height * 1.1));
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rot);
        ctx.fillStyle = p.c;

        if (p.s === 'circle') {
          ctx.beginPath();
          ctx.arc(0, 0, p.r, 0, Math.PI * 2);
          ctx.fill();
        } else if (p.s === 'strip') {
          ctx.fillRect(-p.w * 0.5, -1.5, p.w, 3);
        } else {
          ctx.fillRect(-p.w * 0.5, -p.h * 0.5, p.w, p.h);
        }
        ctx.restore();
      });

      requestAnimationFrame(draw);
    }
    requestAnimationFrame(draw);
  }

}); /* end DOMContentLoaded */

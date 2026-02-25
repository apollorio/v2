/* ═══════════════════════════════════════════════════════════════════
   APOLLO::RIO — sign-doc.js  v1.1.0
   Digital Signature Engine — Stamp positioning, interact.js drag/resize,
   pinch-to-zoom, Titan Stamp builder, confetti celebration.
   Depends on: Apollo CDN (GSAP 3.14.2), page-layout.js, interact.js
   ═══════════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function() {
  /* ── stamp constants ──
     Natural stamp width: 380px (fixed by design)
     Min resize = 70% of natural. Natural height measured at first render. */
  var STAMP_NW  = 380;
  var STAMP_MIN = 0.70;             /* 70% floor */
  var MIN_W = Math.round(STAMP_NW * STAMP_MIN); /* 266 */
  var MIN_H = 112;                  /* updated after first stamp render */
  var stampNaturalH = 0;            /* measured at runtime */
  var STATE = 'IDLE', P1 = null;
  var currentRect = {x:0, y:0, w:MIN_W, h:MIN_H};
  var rectPlaced = false, interactInst = null, docZoom = 1;

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

  /* ─── overlay anims ─── */
  function dismissOverlay(cb) {
    if (typeof gsap !== 'undefined') {
      var tl = gsap.timeline({ onComplete:function(){ overlay.style.display='none'; if(cb)cb(); }});
      tl.to(ovCard, {scale:.86, opacity:0, duration:.2, ease:'power2.in'})
        .to(ovBg,   {opacity:0, duration:.28},0.06)
        .to(overlay,{opacity:0, duration:.14},0.24);
    } else { overlay.style.display='none'; if(cb)cb(); }
  }
  function openOverlay() {
    overlay.style.cssText = 'display:flex; opacity:1;';
    if (typeof gsap !== 'undefined') {
      gsap.fromTo(ovCard, {scale:.82,opacity:0}, {scale:1,opacity:1,duration:.34,ease:'back.out(2.4)',overwrite:'auto'});
      gsap.fromTo(ovBg,   {opacity:0},           {opacity:1,duration:.22,overwrite:'auto'});
    }
  }

  document.getElementById('btn-iniciar').addEventListener('click', function(){ dismissOverlay(startManual); });
  document.getElementById('btn-auto').addEventListener('click',    function(){ dismissOverlay(autoPlace); });
  btnPos.addEventListener('click', function(){ if(!rectPlaced) openOverlay(); });

  function startManual() {
    STATE='IDLE'; P1=null; ghostSvg.innerHTML='';
    signRect.style.display='none';
    touchOv.classList.add('active');
    btnPos.classList.remove('placed'); btnPos.classList.add('placing');
    btnPosLbl.textContent='1º ponto…';
    if (btnResetSig) btnResetSig.classList.add('visible');
    var h=document.getElementById('place-hint');
    if(h){ h.style.display='block'; h.innerHTML='Toque no 1º ponto<br/><span style="font-size:9px;opacity:.6;">depois no 2º para definir a área</span>'; }
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
    var h=document.getElementById('place-hint');
    var p = getPt(e);
    if (STATE==='IDLE') {
      P1=p; STATE='V1';
      drawP1(p);
      btnPosLbl.textContent='2º ponto…';
    }
    else if (STATE==='V1') {
      if(h) h.style.display='none';
      var d = Math.hypot(p.x-P1.x,p.y-P1.y);
      var P2 = d<20 ? {x:P1.x+MIN_W,y:P1.y+MIN_H} : p;
      ghostSvg.innerHTML='';
      commitRect(buildRect(P1,P2));
      STATE='DONE'; touchOv.classList.remove('active');
      reInitInteract(); markPlaced();
      if (!sbOpen) { sbOpen=true; setSbHeight(); }
    }
  }

  touchOv.addEventListener('touchstart', onTap, {passive:false});
  touchOv.addEventListener('click', onTap);
  touchOv.addEventListener('touchmove', function(e){
    if(STATE!=='V1'||!P1)return; if(e.cancelable)e.preventDefault(); drawLive(P1,getPt(e));
  },{passive:false});
  touchOv.addEventListener('mousemove', function(e){
    if(STATE==='IDLE'&&touchOv.classList.contains('active')){drawCross(getPt(e));return;}
    if(STATE==='V1'&&P1)drawLive(P1,getPt(e));
  });
  touchOv.addEventListener('mouseleave', function(){ if(STATE==='IDLE') ghostSvg.innerHTML=''; });

  function svgEl(tag, attrs) {
    var el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.keys(attrs).forEach(function(k){ el.setAttribute(k,attrs[k]); });
    ghostSvg.appendChild(el); return el;
  }
  function drawCross(p) {
    ghostSvg.innerHTML='';
    var cw=794, ch=1123;
    var s='rgba(244,95,0,.55)';
    svgEl('line',{x1:0,y1:p.y,x2:cw,y2:p.y,stroke:s,'stroke-width':1.5,'stroke-dasharray':'6,4'});
    svgEl('line',{x1:p.x,y1:0,x2:p.x,y2:ch,stroke:s,'stroke-width':1.5,'stroke-dasharray':'6,4'});
    svgEl('circle',{cx:p.x,cy:p.y,r:6,fill:'#f45f00',opacity:.9});
  }
  function drawP1(p) {
    ghostSvg.innerHTML='';
    var cw=794, ch=1123;
    var s='rgba(244,95,0,.70)';
    svgEl('line',{x1:0,y1:p.y,x2:cw,y2:p.y,stroke:s,'stroke-width':2,'stroke-dasharray':'6,4'});
    svgEl('line',{x1:p.x,y1:0,x2:p.x,y2:ch,stroke:s,'stroke-width':2,'stroke-dasharray':'6,4'});
    svgEl('circle',{cx:p.x,cy:p.y,r:8,fill:'#f45f00',opacity:1});
    svgEl('circle',{cx:p.x,cy:p.y,r:4,fill:'#fff',opacity:1});
    var h=document.getElementById('place-hint');
    if(h){ h.innerHTML='Toque no 2º ponto<br/><span style="font-size:9px;opacity:.6;">ponto oposto da área</span>'; }
  }
  function drawLive(p1,p2) {
    ghostSvg.innerHTML='';
    var x=Math.min(p1.x,p2.x), y=Math.min(p1.y,p2.y);
    var w=Math.max(Math.abs(p2.x-p1.x),2), h=Math.max(Math.abs(p2.y-p1.y),2);
    svgEl('circle',{cx:p1.x,cy:p1.y,r:8,fill:'#f45f00',opacity:1});
    svgEl('circle',{cx:p1.x,cy:p1.y,r:4,fill:'#fff',opacity:1});
    svgEl('rect',{x:x,y:y,width:w,height:h,
      fill:'rgba(244,95,0,.08)',stroke:'#f45f00',
      'stroke-width':2,'stroke-dasharray':'6,4'});
    svgEl('circle',{cx:p2.x,cy:p2.y,r:6,fill:'#f45f00',opacity:.7});
    var t=svgEl('text',{x:x+w/2,y:Math.max(y-8,14),
      'text-anchor':'middle',
      'font-family':'Space Mono,monospace',
      'font-size':'11','font-weight':'700',
      fill:'#f45f00',opacity:.95});
    t.textContent=Math.round(w)+'×'+Math.round(h)+'px';
  }
  function buildRect(p1,p2) {
    var cw=pdfC.offsetWidth, ch=pdfC.offsetHeight;
    var x=Math.min(p1.x,p2.x), y=Math.min(p1.y,p2.y);
    var w=Math.max(Math.abs(p2.x-p1.x),MIN_W), h=Math.max(Math.abs(p2.y-p1.y),MIN_H);
    return {x:x,y:y,w:Math.min(w,cw-x),h:Math.min(h,ch-y)};
  }
  function commitRect(r) {
    currentRect = Object.assign({},r);
    Object.assign(signRect.style,{display:'block',left:r.x+'px',top:r.y+'px',width:r.w+'px',height:r.h+'px',transform:'none'});
    signRect.dataset.x=0; signRect.dataset.y=0;
    updateDim(r.w,r.h);
    if (typeof gsap!=='undefined') gsap.fromTo(signRect,{opacity:0,scale:.82},{opacity:1,scale:1,duration:.28,ease:'back.out(2)',overwrite:'auto',clearProps:'transform'});
  }
  function updateDim(w,h) { if(rectDim) rectDim.textContent=Math.round(w)+'×'+Math.round(h)+'px'; }

  function autoPlace() {
    var cw=pdfC.offsetWidth, ch=pdfC.offsetHeight;
    var w=Math.min(380, cw*.48), h=MIN_H*2;
    commitRect({x:cw-w-80,y:ch-h-90,w:w,h:h});
    STATE='DONE'; reInitInteract(); markPlaced();
  }
  function markPlaced() {
    rectPlaced=true;
    btnPos.classList.remove('placing'); btnPos.classList.add('placed');
    btnPosLbl.textContent='✦ Posicionado';
    if (rectDim) rectDim.textContent='arraste · resize';
    if (btnResetSig) btnResetSig.classList.add('visible');
    checkState();
  }

  function syncRectData(el) {
    var bx=parseFloat(el.style.left)||0, by=parseFloat(el.style.top)||0;
    var tx=parseFloat(el.dataset.x)||0,  ty=parseFloat(el.dataset.y)||0;
    currentRect={x:bx+tx,y:by+ty,w:parseFloat(el.style.width),h:parseFloat(el.style.height)};
    updateDim(currentRect.w,currentRect.h);
  }

  var warnTm;
  function showWarn(){ clearTimeout(warnTm); minWarn.classList.add('vis'); warnTm=setTimeout(function(){minWarn.classList.remove('vis');},2200); }

  function nudge(dw,dh) {
    if (!rectPlaced) {
      if (window.innerWidth < 861) return;
      docZoom = Math.max(.6, Math.min(1.6, docZoom + dw * .007));
      pdfC.style.transform = 'scale(' + docZoom + ')';
      return;
    }
    var nw = (parseFloat(signRect.style.width )  || currentRect.w) + dw;
    var nh = (parseFloat(signRect.style.height) || currentRect.h) + dh;
    if (nw < MIN_W || nh < MIN_H) { showWarn(); return; }
    signRect.style.width  = nw + 'px';
    signRect.style.height = nh + 'px';
    currentRect.w = nw; currentRect.h = nh; updateDim(nw, nh);
  }

  document.getElementById('btn-plus').addEventListener('click',  function(e){e.stopPropagation();nudge(16,7);});
  document.getElementById('btn-minus').addEventListener('click', function(e){e.stopPropagation();nudge(-16,-7);});
  var btnRestartPos = document.getElementById('btn-restart-pos');
  if (btnRestartPos) btnRestartPos.addEventListener('click', function(e){ e.stopPropagation(); resetSig(); openOverlay(); });

  /* ─── sign-bottom toggle ─── */
  var sbBottom   = document.getElementById('sign-bottom');
  var sbToggle   = document.getElementById('sbottom-toggle');
  var sbOpen     = true;

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
    sbToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      sbOpen = !sbOpen;
      setSbHeight();
    });
  }

  /* ─── reset signature ─── */
  var btnResetSig = document.getElementById('btn-reset-sig');
  function resetSig() {
    if (interactInst) { interactInst.unset(); interactInst = null; }
    signRect.style.display = 'none';
    signRect.classList.remove('confirmed');
    rectLbl.classList.remove('ok'); rectDim.classList.remove('ok');
    ghostSvg.innerHTML = '';
    touchOv.classList.remove('active');
    STATE = 'IDLE'; P1 = null; rectPlaced = false; docZoom = 1;
    pdfC.style.transform = '';
    scalePapers();
    btnPos.classList.remove('placed','placing');
    btnPosLbl.textContent = 'Posicionar';
    if (btnResetSig) btnResetSig.classList.remove('visible');
    var h = document.getElementById('place-hint');
    if (h) h.style.display = 'none';
    if (!sbOpen) { sbOpen = true; setSbHeight(); }
    checkState();
  }
  if (btnResetSig) btnResetSig.addEventListener('click', resetSig);

  function checkState(){ btnSign.disabled = !(chkAg.checked && rectPlaced); }
  chkAg.addEventListener('change', checkState);

  function genHash(){ return Array.from({length:64},function(){return Math.floor(Math.random()*16).toString(16);}).join(''); }

  /* ─── Titan Stamp builder ─── */
  var WATERMARK_PATH = 'M10.208521 14.887119l2.975605 0c2.878458 0 4.409489 0.720844 6.359568 2.627895c0.108236 0.105828 0.12279 0.278202 0.020394 0.389215l-2.484774 2.694557c-0.102376 0.110992 -0.286064 0.130942 -0.389215 0.020407l-3.228883 -3.454773 0 4.553169c0 0.151587 -0.124013 0.2756 -0.2756 0.2756l-2.975605 0c-0.151574 0 -0.2756 -0.124013 -0.2756 -0.2756l0 -6.554906c0 -0.151574 0.124026 -0.275587 0.2756 -0.275587zM9.106143 10.203854l0 2.975605c0 2.878458 -0.720832 4.409489 -2.627895 6.359581c-0.105828 0.108223 -0.278215 0.122777 -0.389215 0.020381l-2.69457 -2.484774c-0.110979 -0.102376 -0.130929 -0.286064 -0.020407 -0.389215l3.454773 -3.228883 -4.553169 0c-0.151574 0 -0.275587 -0.124026 -0.275587 -0.275613l0 -2.975592c0 -0.151587 0.124013 -0.2756 0.275587 -0.2756l6.554906 0c0.151574 0 0.2756 0.124013 0.2756 0.2756zM13.791479 9.115478l-2.975592 0c-2.878458 0 -4.409489 -0.720844 -6.359581 -2.627895c-0.108223 -0.105828 -0.122777 -0.278202 -0.020394 -0.389215l2.484774 -2.69457c0.102363 -0.110979 0.286051 -0.130929 0.389215 -0.020407l3.228883 3.454773 0 -4.553169c0 -0.151574 0.124026 -0.275587 0.2756 -0.275587l2.975592 0c0.151587 0 0.275613 0.124013 0.275613 0.275587l0 6.554906c0 0.151587 -0.124026 0.275613 -0.275613 0.275613zM14.893857 13.783143l0 -2.975592c0 -2.878458 0.720832 -4.409489 2.627895 -6.359581c0.105828 -0.108236 0.278202 -0.12279 0.389215 -0.020394l2.694557 2.484774c0.110992 0.102376 0.130942 0.286064 0.02042 0.389215l-3.454786 3.228883 4.553182 0c0.151574 0 0.275587 0.124026 0.275587 0.275613l0 2.975592c0 0.151587 -0.124013 0.2756 -0.275587 0.2756l-6.554906 0c-0.151574 0 -0.275587 -0.124013 -0.275587 -0.2756z';

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

  /* ─── injectStamp: CONTAIN — nothing cut, rect = stamp exactly ───
     1. Render at natural 380px to measure real height
     2. containScale = min(availW/380, availH/naturalH)  ← CONTAIN
     3. Clamp: floor=70% natural, ceil=uncapped (stamp can grow with big rect)
     4. Resize the containerEl to finalW×finalH so rect === stamp
     Returns { w, h, scale } for caller to update currentRect / done-sig-rect  */
  function injectStamp(containerEl, availW, availH, userName, date, hash) {
    /* — step 1: inject at natural size to measure — */
    containerEl.innerHTML = buildTitanStamp(userName, date, hash);
    var wrap = containerEl.querySelector('.ts-wrap');
    wrap.style.cssText += ';transform:none !important;width:' + STAMP_NW + 'px;';

    /* — step 2: measure natural height — */
    var natH = wrap.offsetHeight;
    if (!natH || natH < 10) natH = 170; /* fallback if layout not flushed */
    stampNaturalH = natH;
    /* update global minimums so interact respects the 70% floor */
    MIN_H = Math.round(natH * STAMP_MIN);

    /* — step 3: contain scale — */
    var scaleW = availW / STAMP_NW;
    var scaleH = availH / natH;
    var scale  = Math.min(scaleW, scaleH); /* CONTAIN = minimum axis */
    scale = Math.max(scale, STAMP_MIN);    /* floor: 70% natural */
    /* no upper cap — let large rects show a larger stamp */

    /* — step 4: apply — */
    wrap.style.transform       = 'scale(' + scale + ')';
    wrap.style.transformOrigin = 'top left';

    /* — step 5: resize container to exact stamp footprint — */
    var finalW = Math.round(STAMP_NW * scale);
    var finalH = Math.round(natH    * scale);
    containerEl.style.width  = finalW + 'px';
    containerEl.style.height = finalH + 'px';

    return { w: finalW, h: finalH, scale: scale };
  }

  /* Re-init interact with updated MIN_W / MIN_H after stamp is placed */
  function reInitInteract() {
    if (interactInst) { interactInst.unset(); interactInst = null; }
    if (typeof interact === 'undefined') return;
    interactInst = interact('#sign-rect')
      .draggable({
        listeners: { move: function(ev) {
          var el = ev.target;
          var x = (parseFloat(el.dataset.x) || 0) + ev.dx;
          var y = (parseFloat(el.dataset.y) || 0) + ev.dy;
          el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
          el.dataset.x = x; el.dataset.y = y; syncRectData(el);
        }},
        modifiers: [interact.modifiers.restrictRect({ restriction: '#pdf-p2', endOnly: true })]
      })
      .resizable({
        edges: { left: true, right: true, bottom: true, top: true },
        listeners: { move: function(ev) {
          var el = ev.target;
          if (ev.rect.width < MIN_W || ev.rect.height < MIN_H) { showWarn(); return; }
          el.style.width  = ev.rect.width  + 'px';
          el.style.height = ev.rect.height + 'px';
          var x = (parseFloat(el.dataset.x) || 0) + ev.deltaRect.left;
          var y = (parseFloat(el.dataset.y) || 0) + ev.deltaRect.top;
          el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
          el.dataset.x = x; el.dataset.y = y; syncRectData(el);
        }},
        modifiers: [interact.modifiers.restrictSize({ min: { width: MIN_W, height: MIN_H } })]
      });
  }

  btnSign.addEventListener('click', function(){
    if (btnSign.disabled) return;
    btnSign.innerHTML='<i class="ri-loader-4-line ri-spin"></i> Processando...';
    btnSign.style.opacity='.7';

    setTimeout(function(){
      var hash = genHash(), date = new Date().toLocaleString('pt-BR');
      var userName = 'GESTOR';

      /* ── Panel 2: inject stamp with CONTAIN scale ──
         injectStamp returns { w, h } = final rendered dimensions.
         sign-rect must snap to exactly those dimensions (rect = stamp).   */
      signRect.classList.add('confirmed');
      var inner = signRect.querySelector('.rect-inner');
      var stamped = injectStamp(inner, currentRect.w, currentRect.h, userName, date, hash);

      /* snap sign-rect to exact stamp footprint — nothing cut, nothing extra */
      signRect.style.width  = stamped.w + 'px';
      signRect.style.height = stamped.h + 'px';
      currentRect.w = stamped.w;
      currentRect.h = stamped.h;
      updateDim(stamped.w, stamped.h);

      /* re-init interact with 70% floor from now-known natural height */
      reInitInteract();

      /* ── Panel 2 paper sig-line ── */
      document.getElementById('sig-stamp').style.opacity='1';
      document.getElementById('user-sig-line').innerHTML =
        '<div style="font-size:11px;font-weight:700;color:var(--live);font-family:var(--mono);text-transform:uppercase;letter-spacing:.04em;">' + userName + '</div>';
      document.getElementById('user-sig-date').textContent = date;
      document.getElementById('doc-hash-prev').textContent = hash.substring(0,10)+'…';
      document.getElementById('doc-hash-prev').style.color='var(--live)';

      ['pill-p1','pill-p2'].forEach(function(id){
        var el=document.getElementById(id); if(!el)return;
        el.className='pill signed'; el.innerHTML='<i class="ri-check-line"></i> Assinado';
      });

      /* ── Panel 3 paper ── */
      document.getElementById('done-id-line').textContent='APOLLO-2025-X992 · '+date;
      document.getElementById('done-part-name').textContent=userName;
      document.getElementById('done-sig-name').textContent=userName;
      document.getElementById('done-sig-name').style.color='var(--live)';
      document.getElementById('done-sig-date').textContent=date;
      document.getElementById('sig-stamp-p3').style.opacity='1';
      document.getElementById('done-hash-short').textContent=hash.substring(0,14)+'…';
      document.getElementById('done-hash-full').textContent=hash;

      /* ── Panel 4 (signed-file) paper ── */
      document.getElementById('sf-sig-name').textContent=userName;
      document.getElementById('sf-sig-name').style.color='var(--live)';
      document.getElementById('sf-sig-date').textContent=date;
      document.getElementById('sig-stamp-p4').style.opacity='1';
      document.getElementById('sf-hash-short').textContent=hash.substring(0,14)+'…';
      document.getElementById('sf-hash-inline').textContent=hash.substring(0,18)+'…';
      var sfRect = document.getElementById('sf-sig-rect');
      sfRect.style.left    = currentRect.x + 'px';
      sfRect.style.top     = currentRect.y + 'px';
      sfRect.style.display = 'block';
      injectStamp(sfRect, currentRect.w, currentRect.h, userName, date, hash);

      /* ── Panel 3: done-sig-rect mirrors sign-rect at paper scale 0.76 ──
         Use the final stamped dims (already "snapped") × paperScale.        */
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

      setTimeout(function(){
        if (window.ApolloSlider) window.ApolloSlider.navigate('done','right');
        if (typeof gsap!=='undefined'){
          gsap.fromTo('.done-badge',             {opacity:0,y:8}, {opacity:1,y:0,duration:.44,ease:'back.out(1.8)',delay:.2});
          gsap.fromTo('.done-aside .aside-card', {opacity:0,y:10},{opacity:1,y:0,stagger:.08,duration:.38,ease:'power3.out',delay:.3});
          gsap.fromTo('#sig-stamp-p3',           {opacity:0,rotate:-14},{opacity:1,rotate:-8,duration:.44,ease:'back.out(2)',delay:.5});
          gsap.fromTo('#done-sig-rect',          {opacity:0,scale:.88},{opacity:1,scale:1,duration:.44,ease:'back.out(1.6)',delay:.4,transformOrigin:'top left'});
        }
      }, 580);

    }, 1400);
  });

  /* ─── mobile paper scaler ─── */
  function scalePapers() {
    var vw = (window.visualViewport ? window.visualViewport.width : window.innerWidth);

    if (vw >= 861) {
      document.querySelectorAll('.paper-sheet').forEach(function(s) {
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

    document.querySelectorAll('.paper-sheet').forEach(function(s) {
      s.style.willChange      = 'transform';
      s.style.transform       = 'scale(' + scale + ')';
      s.style.transformOrigin = 'top left';
      s.style.marginBottom    = Math.round(a4h * scale - a4h) + 'px';
    });

    requestAnimationFrame(function() {
      requestAnimationFrame(function() {
        document.querySelectorAll('.paper-sheet').forEach(function(s) {
          s.style.willChange = 'auto';
        });
      });
    });
  }
  scalePapers();
  window.addEventListener('resize', scalePapers);
  if (window.visualViewport) window.visualViewport.addEventListener('resize', scalePapers);

  document.getElementById('btn-download').addEventListener('click', function(){
    var btn = document.getElementById('btn-download');
    btn.innerHTML='<i class="ri-loader-4-line ri-spin"></i> Gerando PDF...';
    setTimeout(function(){ btn.innerHTML='<i class="ri-check-line"></i> Download Concluído'; }, 1800);
  });

  /* ─── pinch-to-zoom ─── */
  (function() {
    var pinchState = null;

    function getDistance(touches) {
      var dx = touches[0].clientX - touches[1].clientX;
      var dy = touches[0].clientY - touches[1].clientY;
      return Math.sqrt(dx * dx + dy * dy);
    }
    function getCenter(touches) {
      return { x: (touches[0].clientX + touches[1].clientX) / 2, y: (touches[0].clientY + touches[1].clientY) / 2 };
    }
    function handlePinchStart(e, paper) {
      if (e.touches.length !== 2) return;
      e.preventDefault();
      var rect = paper.getBoundingClientRect();
      var center = getCenter(e.touches);
      var initialDist = getDistance(e.touches);
      var currentTransform = paper.style.transform || '';
      var currentScale = 1;
      var scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
      if (scaleMatch) currentScale = parseFloat(scaleMatch[1]);
      pinchState = { initialDist: initialDist, initialScale: currentScale, centerX: center.x - rect.left, centerY: center.y - rect.top };
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
      paper.style.willChange = 'auto';
      setTimeout(scalePapers, 100);
    }

    var doneDocPaper = document.querySelector('#done-doc .paper-sheet');
    if (doneDocPaper) {
      doneDocPaper.addEventListener('touchstart', function(e) { handlePinchStart(e, this); }, {passive: false});
      doneDocPaper.addEventListener('touchmove',  function(e) { handlePinchMove(e, this); },  {passive: false});
      doneDocPaper.addEventListener('touchend',   function(e) { handlePinchEnd(e, this); },   {passive: true});
    }
    var previewPaper = document.querySelector('#pdf-p1');
    if (previewPaper) {
      previewPaper.addEventListener('touchstart', function(e) { handlePinchStart(e, this); }, {passive: false});
      previewPaper.addEventListener('touchmove',  function(e) { handlePinchMove(e, this); },  {passive: false});
      previewPaper.addEventListener('touchend',   function(e) { handlePinchEnd(e, this); },   {passive: true});
    }
  })();

  /* ─── Canvas Confetti ─── */
  function confetti() {
    var REDUCED = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    if (REDUCED) return;

    var canvas = document.createElement('canvas');
    canvas.id = 'confetti-canvas';
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    document.body.appendChild(canvas);
    var ctx = canvas.getContext('2d');

    var COLORS = ['#f45f00','#16a34a','#3b82f6','#f59e0b','#ec4899','#8b5cf6','#ffffff'];
    var SHAPES = ['rect','circle','strip'];
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
        g  : 0.12 + Math.random() * 0.12,   /* gravity per shape */
        wo : (Math.random() - 0.5) * 0.8,   /* wobble x */
        t  : 0,
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

      pieces.forEach(function(p) {
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
});
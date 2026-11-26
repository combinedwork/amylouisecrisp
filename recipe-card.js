/* =========================================================================
   Recipe card + Cook Mode + video players.
   Builds everything from a recipe in data.js, so any recipe that is added
   gets a servings scaler, ingredient checklist, method, video, tips and a
   full step-by-step Cook Mode with timers automatically.
   ========================================================================= */
(() => {
  const { $, $$, toast, favs, toggleFav, onFavs, swipe, esc, cat } = window.ALC;

  /* ---------- helpers ---------- */
  const fr = n => { const w = Math.floor(n), f = n - w, m = { .25: '¼', .5: '½', .75: '¾' };
    const k = Object.keys(m).find(x => Math.abs(f - x) < .07);
    if (f < .07) return String(w || ''); if (k) return (w || '') + m[k];
    if (Math.abs(f - 1/3) < .05) return (w || '') + '⅓'; if (Math.abs(f - 2/3) < .05) return (w || '') + '⅔';
    return n.toFixed(1); };
  const amount = (q, u, s, base) => { if (q == null) return ''; const v = q * s / base;
    if (u === 'g' || u === 'ml') return Math.max(5, Math.round(v / 5) * 5) + u + ' ';
    return fr(v) + (u ? ' ' + u : '') + ' '; };
  const play = s => `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>`;

  /* ---------- video players (load only when played) ---------- */
  let framed = true; try { framed = window.self !== window.top; } catch (e) {}
  const openOnYouTube = id => { const a = document.createElement('a'); a.href = 'https://youtu.be/' + id; a.target = '_blank'; a.rel = 'noopener'; document.body.appendChild(a); a.click(); a.remove(); };
  const posterHTML = (v, img, pos, label, sub, size) => `
    <button class="video-poster" aria-label="Play video: ${esc(v.title)}">
      <img src="${img}" alt="" style="object-position:${pos || '50% 50%'}">
      <span class="play">${play(size || 26)}</span>
      <span class="video-label"><b>${esc(label)}</b><small>${esc(framed ? 'Opens on YouTube ↗' : sub)}</small></span>
    </button>`;
  const stopVideo = fr => { if (fr.classList.contains('playing')) { fr.innerHTML = fr._poster; fr.classList.remove('playing'); bindVideo(fr); } };
  const bindVideo = fr => {
    if (!fr._poster) fr._poster = fr.innerHTML;
    const b = fr.querySelector('.video-poster'); if (!b) return;
    b.onclick = () => {
      const id = fr.dataset.yt;
      if (framed) { openOnYouTube(id); return; }
      $$('.video-frame').forEach(o => { if (o !== fr) stopVideo(o); });
      const f = document.createElement('iframe');
      f.src = `https://www.youtube-nocookie.com/embed/${id}?autoplay=1&rel=0&modestbranding=1&playsinline=1`;
      f.title = fr.dataset.title || 'Recipe video';
      f.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      f.referrerPolicy = 'strict-origin-when-cross-origin'; f.allowFullscreen = true;
      fr.replaceChildren(f); fr.classList.add('playing');
    };
  };
  document.addEventListener('securitypolicyviolation', e => {
    if (/youtube/.test(e.blockedURI || '')) { framed = true; $$('.video-frame').forEach(stopVideo); }
  });
  const bindVideos = root => (root || document).querySelectorAll('.video-frame[data-yt]').forEach(bindVideo);

  /* ---------- Cook Mode (one overlay, filled per recipe) ---------- */
  document.body.insertAdjacentHTML('beforeend', `
  <div class="cook" id="cook" role="dialog" aria-modal="true" aria-label="Cook mode">
    <div class="cook-top">
      <span class="serif" id="cookTitle"></span>
      <div style="display:flex;gap:14px;align-items:center">
        <label class="wake"><input type="checkbox" id="wake"> Keep screen on</label>
        <button class="close" id="cookClose" aria-label="Exit cook mode">✕</button>
      </div>
    </div>
    <div class="bar"><i id="cookBar"></i></div>
    <div class="cook-body">
      <div class="n" id="cookN"></div>
      <p id="cookStep"></p>
      <div class="cook-ings" id="cookIngs"></div>
      <div class="timer" id="timer">
        <span class="t" id="tDisp">0:00</span>
        <button data-add="60">+1 min</button><button data-add="300">+5 min</button>
        <button id="tGo">Start</button><button id="tReset">Reset</button>
      </div>
      <div class="swipe-hint">Swipe left or right to change step</div>
    </div>
    <div class="cook-nav">
      <button class="btn ghost" id="prev">← Back</button>
      <button class="btn" id="next">Next step →</button>
    </div>
  </div>`);

  let R = null, steps = [], cur = 0, secs = 0, run = null, wl = null, servesNow = 4;
  const tShow = () => { $('#tDisp').textContent = Math.floor(secs / 60) + ':' + String(secs % 60).padStart(2, '0'); };
  const tStop = () => { clearInterval(run); run = null; $('#tGo').textContent = 'Start'; };
  const tStart = () => { if (!secs) return; $('#timer').classList.remove('ring'); $('#tGo').textContent = 'Pause';
    run = setInterval(() => { secs--; tShow(); if (secs <= 0) { tStop(); $('#timer').classList.add('ring'); toast('⏰ Time’s up!'); try { navigator.vibrate && navigator.vibrate([200, 100, 200]); } catch (e) {} } }, 1000); };
  const stepTimer = i => ((R.timers || {})[i + 1] || 0) * 60;
  const allItems = () => R.ingredients.flatMap(g => g.items);
  // Cook Mode shows each ingredient on the step where it is first used, scaled to the chosen servings
  const STOP = ['dried', 'white', 'green', 'plain', 'fresh', 'large', 'small', 'extra'];
  let firstUse = [];
  const mapIngredients = () => {
    const seen = new Set();
    firstUse = steps.map(s => { const t = s.toLowerCase();
      return allItems().filter((it, idx) => { const [q, , n] = it; if (q == null || seen.has(idx)) return false;
        const words = n.split(',')[0].toLowerCase().split(/\s+/).filter(w => w.length > 3 && !STOP.includes(w));
        if (words.some(w => t.includes(w.replace(/s$/, '')))) { seen.add(idx); return true; } return false; }); });
  };
  const ingsFor = i => (firstUse[i] || []).map(([q, u, n]) => `<span>${esc(amount(q, u, servesNow, R.serves))}${esc(n.split(',')[0])}</span>`).join('');
  const showStep = () => {
    $('#cookN').textContent = `Step ${cur + 1} of ${steps.length}`;
    const p = $('#cookStep'); p.textContent = steps[cur]; p.style.animation = 'none'; void p.offsetWidth; p.style.animation = '';
    const ings = ingsFor(cur); $('#cookIngs').innerHTML = ings ? `<small>You’ll need</small>${ings}` : '';
    $('#cookBar').style.width = ((cur + 1) / steps.length * 100) + '%';
    $('#prev').disabled = cur === 0;
    $('#next').textContent = cur === steps.length - 1 ? 'Finish 🎉' : 'Next step →';
    tStop(); secs = stepTimer(cur); $('#timer').classList.remove('ring'); tShow();
  };
  const isCook = () => $('#cook').classList.contains('open');
  const openCook = (recipe, serves) => { R = recipe; steps = recipe.method; servesNow = serves || recipe.serves; cur = 0;
    $('#cookTitle').textContent = recipe.title; mapIngredients(); showStep();
    $('#cook').classList.add('open'); document.body.classList.add('lock'); $('#next').focus(); };
  const closeCook = () => { tStop(); $('#cook').classList.remove('open'); document.body.classList.remove('lock');
    if (wl) { wl.release().catch(() => {}); wl = null; $('#wake').checked = false; } };
  $('#tGo').onclick = () => run ? tStop() : tStart();
  $('#tReset').onclick = () => { tStop(); secs = stepTimer(cur); $('#timer').classList.remove('ring'); tShow(); };
  $$('#timer [data-add]').forEach(b => b.onclick = () => { secs += +b.dataset.add; $('#timer').classList.remove('ring'); tShow(); });
  $('#cookClose').onclick = closeCook;
  $('#prev').onclick = () => { if (cur > 0) { cur--; showStep(); } };
  $('#next').onclick = () => { if (cur < steps.length - 1) { cur++; showStep(); } else { closeCook(); toast('Enjoy! 💕'); } };
  swipe($('#cook .cook-body'), () => $('#next').click(), () => $('#prev').click());
  $('#wake').onchange = async e => {
    if (e.target.checked) { try { wl = await navigator.wakeLock.request('screen'); toast('Screen will stay on'); } catch (err) { e.target.checked = false; toast('Not supported on this device'); } }
    else if (wl) { wl.release().catch(() => {}); wl = null; }
  };
  document.addEventListener('keydown', e => {
    if (!isCook()) return;
    if (e.key === 'Escape') closeCook(); if (e.key === 'ArrowRight') $('#next').click(); if (e.key === 'ArrowLeft') $('#prev').click();
  });

  /* ---------- the recipe card ---------- */
  const renderRecipe = (host, r, opts = {}) => {
    const total = r.prep + r.cook;
    const cats = r.categories.map(cat).filter(Boolean);
    const H = opts.h1 ? 'h1' : 'h2';
    host.innerHTML = `
    <div class="wrap rec">
      <div class="ph reveal"><img src="${r.image}" alt="${esc(r.title)}" style="object-position:${r.imagePos || '50% 50%'}"></div>
      <div class="reveal">
        <span class="eyebrow">${esc(opts.eyebrow || cats.map(c => c.name).join(' · '))}</span>
        <${H}>${esc(r.title)}</${H}>
        <p class="intro">${esc(r.summary)}</p>
        <div class="facts">
          <div><small>Prep</small><b>${r.prep}m</b></div>
          <div><small>Cook</small><b>${r.cook}m</b></div>
          <div><small>Total</small><b>${total}m</b></div>
          <div><small>Serves</small><span class="stepper"><button data-s="-1" aria-label="Fewer servings">−</button><b class="serves">${r.serves}</b><button data-s="1" aria-label="More servings">+</button></span></div>
        </div>
        <div class="actions">
          <button class="btn dark cook-btn">${play(18)}Start Cook Mode</button>
          <button class="btn line save-btn">♡ Save recipe</button>
          <button class="btn line" onclick="window.print()">Print</button>
        </div>
        <div class="tabbar" role="tablist">
          <button class="on" data-tab="ing" role="tab">Ingredients</button>
          <button data-tab="method" role="tab">Method</button>
          ${r.video ? `<button data-tab="video" role="tab"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-right:5px;vertical-align:-1px"><path d="M7 4v16l13-8z"/></svg>Video</button>` : ''}
          ${r.tips && r.tips.length ? `<button data-tab="tips" role="tab"><span class="t-full">Tips &amp; Storage</span><span class="t-short">Tips</span></button>` : ''}
        </div>
        <div class="panel on" data-panel="ing"><div class="ing-list"></div><div class="progress-note ing-note"></div></div>
        <div class="panel" data-panel="method">
          <ol class="steps">${r.method.map((s, i) => `<li tabindex="0">${esc(s)}${(r.timers || {})[i + 1] ? `<span class="step-timer">⏱ ${r.timers[i + 1]} min timer in Cook Mode</span>` : ''}</li>`).join('')}</ol>
          <div class="progress-note">Tap a step to mark it done, or use <a href="#" class="open-cook">Cook Mode</a> to follow one step at a time.</div>
        </div>
        ${r.video ? `<div class="panel" data-panel="video">
          <div class="video-frame card-video" data-yt="${r.video.youtube}" data-title="${esc(r.video.title)}">${posterHTML(r.video, r.image, r.imagePos, 'Watch me make it', 'See every step in action', 26)}</div>
          <p class="progress-note">Want to cook along? Press play, then tap <a href="#" class="open-cook">Start Cook Mode</a> to follow the steps one at a time. <a href="https://youtu.be/${r.video.youtube}" target="_blank" rel="noopener">Watch on YouTube ↗</a></p>
        </div>` : ''}
        ${r.tips && r.tips.length ? `<div class="panel" data-panel="tips">${r.tips.map(([t, p], i) => `<details class="tip"${i ? '' : ' open'}><summary>${esc(t)}</summary><p>${esc(p)}</p></details>`).join('')}</div>` : ''}
      </div>
    </div>`;

    let serves = r.serves; const checked = new Set();
    const TOTAL = r.ingredients.reduce((a, g) => a + g.items.length, 0);
    const q = s => host.querySelector(s), qa = s => [...host.querySelectorAll(s)];
    const note = () => { const n = checked.size; q('.ing-note').textContent = n === 0 ? 'Tick things off as you gather them.' : n === TOTAL ? 'Everything’s ready. Time to cook! 🎉' : `${n} of ${TOTAL} gathered`; };
    const renderIng = () => { let h = '', i = 0;
      r.ingredients.forEach(g => { h += `<div class="ing-group">${esc(g.group)}</div><ul class="ing">`;
        g.items.forEach(([qq, u, n]) => { const id = i++; h += `<li><label><input type="checkbox" data-i="${id}" ${checked.has(id) ? 'checked' : ''}><span><span class="amt">${esc(amount(qq, u, serves, r.serves))}</span>${esc(n)}</span></label></li>`; });
        h += '</ul>'; });
      q('.ing-list').innerHTML = h; note(); };
    q('.ing-list').addEventListener('change', e => { const i = +e.target.dataset.i; e.target.checked ? checked.add(i) : checked.delete(i); note(); });
    qa('.stepper button').forEach(b => b.onclick = () => { serves = Math.min(12, Math.max(1, serves + +b.dataset.s));
      const s = q('.serves'); s.textContent = serves; s.classList.remove('bump'); void s.offsetWidth; s.classList.add('bump'); renderIng(); });
    renderIng();
    q('.steps').addEventListener('click', e => { const li = e.target.closest('li'); if (li) li.classList.toggle('done'); });
    q('.steps').addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); e.target.click(); } });
    qa('.tabbar button').forEach(b => b.onclick = () => {
      qa('.tabbar button').forEach(x => x.classList.toggle('on', x === b));
      qa('.panel').forEach(p => p.classList.toggle('on', p.dataset.panel === b.dataset.tab));
      if (b.dataset.tab !== 'video') { const cv = q('.card-video'); if (cv) stopVideo(cv); }
    });
    q('.cook-btn').onclick = () => openCook(r, serves);
    qa('.open-cook').forEach(a => a.onclick = e => { e.preventDefault(); openCook(r, serves); });
    const sb = q('.save-btn'); const syncSave = () => { sb.textContent = favs.has(r.slug) ? '♥ Saved' : '♡ Save recipe'; };
    sb.onclick = () => toggleFav(r.slug); onFavs(syncSave); syncSave();
    bindVideos(host); window.ALC.reveal(host);
  };

  Object.assign(window.ALC, { renderRecipe, openCook, bindVideos, posterHTML });
})();

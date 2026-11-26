(() => {
  const { $, $$, data: D, cat, recipeUrl, catUrl, inCat, time, cardHTML, soonCard, catThumb, favs, onFavs, esc, swipe, renderRecipe, bindVideos, posterHTML, syncFavs, reveal } = window.ALC;

  /* ---------- four main categories ---------- */
  $('#mainCats').innerHTML = D.categories.filter(c => c.group === 'main').map(c =>
    `<a class="cat reveal" href="${catUrl(c.slug)}"><div class="ph">${catThumb(c)}</div><h3>${esc(c.name)}</h3><p>${esc(c.blurb)}</p></a>`).join('');

  /* ---------- seasonal + extra collections ---------- */
  $('#collGrid').innerHTML = D.categories.filter(c => c.group === 'more').map(c => {
    const n = inCat(c.slug).length, count = n ? `${n} recipe${n > 1 ? 's' : ''}` : 'Recipes coming soon';
    if (c.seasonal) return `<a class="coll coll-festive reveal" href="${catUrl(c.slug)}">
        <span class="season-badge">In season · ${esc(D.season.name)}</span>
        <span class="festive-mark" aria-hidden="true">${D.season.emoji || '✨'}</span>
        <span class="coll-body"><span class="eyebrow">${esc(c.name)}</span><h3>${esc(D.season.heading)}</h3><p>${esc(D.season.text)}</p><span class="coll-link">Browse ${esc(D.season.name)} recipes →</span></span></a>`;
    return `<a class="coll reveal" href="${catUrl(c.slug)}"><span class="coll-img">${catThumb(c)}</span>
        <span class="coll-body"><h3>${esc(c.name)}</h3><p>${esc(c.blurb)}</p><span class="coll-link">${count} →</span></span></a>`;
  }).join('');

  /* ---------- latest recipe card + cook mode ---------- */
  const latest = D.recipes[0];
  if (latest) renderRecipe($('#recipe'), latest, { eyebrow: 'Latest recipe' + ((cat(latest.categories[0]) || {}).name ? ' · ' + cat(latest.categories[0]).name : '') });
  else $('#recipe').remove();

  /* ---------- watch & cook along ---------- */
  const vf = $('#videoFrame');
  if (vf) vf.innerHTML = posterHTML({ title: vf.dataset.title }, vf.dataset.img || (latest || {}).image, vf.dataset.pos || '50% 50%', vf.dataset.label || 'Watch me make it', vf.dataset.sub || 'YouTube', 30);
  if (vf) bindVideos($('#video'));
  const y = $('#ytChannel'); if (y && D.youtubeChannel) { y.href = D.youtubeChannel; y.textContent = 'Visit my YouTube channel ↗'; }

  /* ---------- latest recipes grid (real recipes, then "coming soon" for empty main categories) ---------- */
  const mains = D.categories.filter(c => c.group === 'main');
  $('#filters').innerHTML = `<button class="on" data-filter="all">All</button>` + mains.map(c => `<button data-filter="${c.slug}">${esc(c.name)}</button>`).join('') + `<button data-filter="saved">♥ Saved</button>`;
  const cards = D.recipes.slice(0, 4).map(r => cardHTML(r, 'reveal')).join('') +
    mains.filter(c => !inCat(c.slug).length).slice(0, Math.max(0, 4 - D.recipes.length)).map(c => soonCard(c, 'reveal')).join('');
  $('#grid').innerHTML = cards + `<div class="empty" id="empty"><b class="serif" style="font-size:22px">Nothing here yet</b><p style="margin:6px 0 0"></p></div>`;
  let active = 'all';
  const applyFilter = f => {
    active = f; let shown = 0;
    $$('#filters button').forEach(b => b.classList.toggle('on', b.dataset.filter === f));
    $$('#grid .card').forEach(c => { const ok = f === 'all' || (f === 'saved' ? favs.has(c.dataset.id) : c.dataset.cats.split(' ').includes(f));
      c.classList.toggle('hide', !ok); if (ok) { shown++; c.classList.add('in'); } });
    $('#empty').classList.toggle('show', !shown);
    const fc = cat(f);
    $('#empty p').innerHTML = f === 'saved' ? 'Tap the ♡ on any recipe to save it for later.' : fc ? `New ${esc(fc.name)} recipes are on their way. <a href="#newsletter">Sign up</a> to hear first!` : '';
  };
  $$('#filters button').forEach(b => b.onclick = () => applyFilter(b.dataset.filter));
  onFavs(() => { if (active === 'saved') applyFilter('saved'); });
  syncFavs(); reveal();

  /* ---------- "What are you in the mood for?" (suggests a real recipe when there is one) ---------- */
  $('#moodPills').innerHTML = D.moods.map((m, i) => `<button class="pill" data-i="${i}">${esc(m.label)}</button>`).join('');
  const lastPick = {};
  $$('#moodPills .pill').forEach(p => p.onclick = () => {
    const m = D.moods[+p.dataset.i];
    $$('#moodPills .pill').forEach(x => x.classList.toggle('on', x === p));
    const pool = D.recipes.filter(r => r.categories.some(c => m.match.includes(c)));
    const c = cat(m.match[0]) || { slug: '', name: m.label };
    let html;
    if (pool.length) {
      let r = pool[Math.floor(Math.random() * pool.length)];
      if (pool.length > 1 && r === lastPick[m.label]) r = pool[(pool.indexOf(r) + 1) % pool.length];
      lastPick[m.label] = r;
      html = `How about my <a href="${recipeUrl(r)}">${esc(r.title)}</a>? Ready in ${time(r.prep + r.cook)}. Or browse all <a href="${catUrl(c.slug)}">${esc(c.name)}</a>.`;
    } else {
      html = `${esc(c.seasonal ? D.season.name : c.name)} recipes are on their way! <a href="#newsletter">Sign up</a> to be the first to know.`;
    }
    const o = $('#moodOut'); o.innerHTML = html; o.classList.add('show');
  });

  /* ---------- hero carousel ---------- */
  (() => {
    const car = $('#carousel'); if (!car) return;
    const slides = [...car.querySelectorAll('.slide')], dots = [...car.querySelectorAll('.car-dots button')];
    const st = $('#carSticker');
    const icon = p => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${p}</svg>`;
    const icons = [
      icon('<path d="M4 11h16a8 8 0 0 1-16 0zM12 3v4M8 5v2M16 5v2"/>'),
      icon('<circle cx="12" cy="12" r="8"/><circle cx="9.5" cy="10" r="1" fill="currentColor"/><circle cx="14" cy="9" r="1" fill="currentColor"/><circle cx="13" cy="14" r="1" fill="currentColor"/>'),
      icon('<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>'),
      icon('<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>')
    ];
    // each slide carries its own label text and link (set in WordPress under Appearance > Customise > Homepage slideshow)
    const INFO = slides.map((s, i) => ({ dot: s.dataset.dot ? esc(s.dataset.dot) : icons[i % icons.length], t: s.dataset.title || '', s: s.dataset.sub || '', href: s.dataset.href || '#' }));
    if (!slides.length) return;
    const MS = (+car.dataset.seconds || 3) * 1000;
    car.style.setProperty('--car-ms', MS + 'ms');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let cur = 0, hover = false, userPaused = reduced, swapT;
    const go = n => {
      const prev = cur; cur = (n + slides.length) % slides.length;
      slides.forEach((s, i) => { s.classList.toggle('on', i === cur); s.setAttribute('aria-hidden', i !== cur); });
      dots.forEach((d, i) => { d.classList.toggle('on', i === cur); d.setAttribute('aria-selected', i === cur); const f = d.querySelector('i'); f.style.animation = 'none'; void f.offsetWidth; f.style.animation = ''; });
      if (prev !== cur || !st.dataset.ready) {
        st.dataset.ready = 1; st.classList.add('swap'); clearTimeout(swapT);
        swapT = setTimeout(() => { const d = INFO[cur]; st.href = d.href; st.querySelector('.dot').innerHTML = d.dot; st.querySelector('.st-txt').innerHTML = `<b>${esc(d.t)}</b>${esc(d.s)}`; st.classList.remove('swap'); }, 280);
      }
    };
    const sync = () => {
      car.classList.toggle('paused', userPaused || hover || document.hidden);
      car.classList.toggle('static', userPaused);
      car.classList.toggle('user-paused', userPaused && !reduced);
      const pb = car.querySelector('.car-pause');
      pb.setAttribute('aria-label', userPaused ? 'Play slideshow' : 'Pause slideshow');
      pb.innerHTML = userPaused
        ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5v14l12-7z"/></svg>'
        : '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';
    };
    dots.forEach(d => d.querySelector('i').addEventListener('animationend', () => { if (!userPaused) go(cur + 1); }));
    dots.forEach((d, i) => d.onclick = e => { e.preventDefault(); go(i); });
    car.querySelector('.prev').onclick = () => go(cur - 1);
    car.querySelector('.next').onclick = () => go(cur + 1);
    car.querySelector('.car-pause').onclick = () => { userPaused = !userPaused; sync(); };
    car.addEventListener('mouseenter', () => { hover = true; sync(); });
    car.addEventListener('mouseleave', () => { hover = false; sync(); });
    car.addEventListener('keydown', e => { if (e.key === 'ArrowRight') go(cur + 1); if (e.key === 'ArrowLeft') go(cur - 1); });
    document.addEventListener('visibilitychange', sync);
    swipe(car.querySelector('.frame'), () => go(cur + 1), () => go(cur - 1));
    go(0); sync();
  })();
})();

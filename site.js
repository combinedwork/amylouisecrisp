/* Shared behaviour for every page: header, reveal, toast, favourites, search, newsletter */
(() => {
  const $ = s => document.querySelector(s), $$ = s => [...document.querySelectorAll(s)];
  const store = {
    get(k, d){ try{ const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; }catch(e){ return d; } },
    set(k, v){ try{ localStorage.setItem(k, JSON.stringify(v)); }catch(e){} }
  };

  /* ---------- content helpers (everything comes from data.js) ---------- */
  const D = window.ALC_DATA;
  const esc = s => String(s).replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  const cat = slug => D.categories.find(c => c.slug === slug);
  // page addresses: the static mock-up uses .html files, WordPress passes its own links in data.links
  const L = Object.assign({ home:'index.html', recipes:'recipes.html', about:'about.html', recipe:'recipe.html?r=' }, D.links || {});
  const recipeUrl = r => r.url || L.recipe + encodeURIComponent(r.slug);
  const catUrl = slug => L.recipes + '#' + slug;
  // words a recipe can be found by: title, collections, keywords and ingredient names
  const kw = r => [r.title, r.keywords || '', r.categories.map(x => (cat(x)||{}).name || '').join(' '), (r.ingredients || []).flatMap(g => g.items.map(i => i[2])).join(' ')].join(' ').toLowerCase();
  const inCat = slug => D.recipes.filter(r => r.categories.includes(slug));
  const time = m => m>=60 ? (m%60 ? `${Math.floor(m/60)} hr ${m%60} mins` : `${m/60} hr${m>=120?'s':''}`) : `${m} mins`;
  const heartSvg = '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg>';
  const catThumb = c => c.image ? `<img src="${esc(c.image)}" alt="" style="object-position:${c.pos||'50% 50%'}">` : c.seasonal ? `<span class="festive-art" aria-hidden="true">${D.season.emoji||'✨'}</span>` : `<span class="festive-art plain" aria-hidden="true">${esc(c.name.charAt(0))}</span>`;
  const cardHTML = (r, extra='') => {
    const c = cat(r.categories[0]) || { name: 'Recipe' };
    return `<div class="card ${extra}" data-id="${r.slug}" data-cats="${r.categories.join(' ')}">
      <button class="heart" aria-label="Save ${esc(r.title)}">${heartSvg}</button>
      <a href="${esc(recipeUrl(r))}"><div class="ph">${r.image ? `<img src="${esc(r.image)}" alt="${esc(r.title)}" style="object-position:${r.imagePos||'50% 50%'}" loading="lazy">` : `<span class="festive-art plain" aria-hidden="true">${esc(r.title.charAt(0))}</span>`}${r.isNew?'<span class="badge">New</span>':''}</div>
      <span class="label">${esc(c.name)}</span><h3>${esc(r.title)}</h3><span class="meta">${time(r.prep+r.cook)} · Serves ${r.serves}</span>
      <span class="desc">${esc(r.summary)}</span></a></div>`;
  };
  const soonCard = (c, extra='') => `<div class="card soon-card ${extra}" data-id="soon-${c.slug}" data-cats="${c.slug}">
      <a href="${catUrl(c.slug)}"><div class="ph soon"><span>Coming soon</span></div><span class="label">${esc(c.name)}</span>
      <h3>New ${esc(c.seasonal ? D.season.name+' ' : '')}recipes on the way</h3><span class="meta">${esc(c.blurb)}</span></a></div>`;
  // footer + "browse" strips are filled from the category list
  document.querySelectorAll('[data-cat-links]').forEach(ul => ul.innerHTML = D.categories.filter(c=>c.group!=='tag').map(c => `<li><a href="${catUrl(c.slug)}">${esc(c.name)}</a></li>`).join(''));
  document.querySelectorAll('[data-cat-strip]').forEach(el => el.innerHTML = D.categories.filter(c=>c.group!=='tag').map(c => {
    const n = inCat(c.slug).length;
    return `<a href="${catUrl(c.slug)}" class="reveal"><span class="th${c.image?'':' festive-th'}">${catThumb(c)}</span><span><b>${esc(c.name)}</b><small>${c.seasonal ? esc(D.season.name)+' · ' : ''}${n ? n+' recipe'+(n>1?'s':'') : 'Coming soon'}</small></span></a>`;
  }).join(''));

  // Mock-up only: add ?ads=preview to the address to see where adverts could go
  if (!D.links && /[?&]ads=preview/.test(location.search)) document.querySelectorAll('.ad-slot[data-ad]').forEach(el => { el.classList.add('ad-slot--preview'); el.innerHTML = '<span>Advert space</span><small>' + esc(el.dataset.ad) + '</small>'; });
  document.querySelectorAll('[data-yt-channel]').forEach(a => { if (D.youtubeChannel) { a.href = D.youtubeChannel; a.target = '_blank'; a.rel = 'noopener'; } });

  /* toast + back to top (injected) */
  document.body.insertAdjacentHTML('beforeend', '<div class="toast" id="toast" role="status"></div><button class="top-btn" id="topBtn" aria-label="Back to top">↑</button>' + (D.links ? '' : '<div class="mock-badge">Design mock‑up</div>'));
  let tt; const toast = msg => { const t=$('#toast'); t.textContent=msg; t.classList.add('show'); clearTimeout(tt); tt=setTimeout(()=>t.classList.remove('show'),2200); };

  /* header */
  const onScroll = () => { const y=scrollY; $('#hdr').classList.toggle('scrolled', y>20); $('#topBtn').classList.toggle('show', y>900); };
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  $('#topBtn').onclick = () => scrollTo({top:0});
  const page = document.body.dataset.page;

  /* mobile slide-in drawer, built from the desktop nav */
  const ico = {
    home:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7v9a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/></svg>',
    book:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M4 19V5M8 7h7"/></svg>',
    search:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>',
    heart:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg>',
    user:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>'
  };
  document.body.insertAdjacentHTML('beforeend', `
    <div class="drawer-bg" id="drawerBg"></div>
    <aside class="drawer" id="drawer" aria-label="Menu" aria-hidden="true">
      <div class="drawer-head"><span class="serif">Menu</span><button class="close" id="drawerClose" aria-label="Close menu">✕</button></div>
      <nav><ul>${$('#nav ul').innerHTML}</ul></nav>
      <div class="drawer-cta"><p><b class="serif" style="font-size:19px">New recipes, straight to your inbox</b><br>A monthly round‑up of new recipes and family favourites.</p><a class="btn" href="#newsletter" data-close-drawer>Sign me up</a></div>
      ${$('footer .socials') ? '<div class="socials">'+$('footer .socials').innerHTML+'</div>' : ''}
    </aside>
    <nav class="tabbar-m" aria-label="Quick navigation">
      <a href="${L.home}" data-tab="home">${ico.home}Home</a>
      <a href="${L.recipes}" data-tab="recipes">${ico.book}Recipes</a>
      <button id="tabSearch" data-tab="search">${ico.search}Search</button>
      <a href="${L.recipes}#saved" data-tab="saved">${ico.heart}Saved<span class="count" data-favcount>0</span></a>
      <a href="${L.about}" data-tab="about">${ico.user}About</a>
    </nav>`);
  const setDrawer = open => {
    document.body.classList.toggle('drawer-open', open);
    const dr = $('#drawer');
    dr.setAttribute('aria-hidden', !open);
    dr.inert = !open;  // closed menu can't be tabbed into
    $('#menuBtn').setAttribute('aria-expanded', open);
    if (open) $('#drawerClose').focus();
  };
  $('#drawer').inert = true;
  $('#menuBtn').onclick = () => setDrawer(true);
  $('#drawerClose').onclick = () => setDrawer(false);
  $('#drawerBg').onclick = () => setDrawer(false);
  $$('#drawer a').forEach(a => a.addEventListener('click', () => setDrawer(false)));
  addEventListener('resize', () => { if (innerWidth > 900) setDrawer(false); });

  const markNav = () => {
    const h = location.hash;
    $$('[data-nav]').forEach(a => a.classList.toggle('on', a.dataset.nav === page && !(page==='recipes' && a.hash && a.hash!==h)));
    $$('.tabbar-m [data-tab]').forEach(t => t.classList.toggle('on', t.dataset.tab === (page==='recipes' && h==='#saved' ? 'saved' : page)));
  };
  markNav(); addEventListener('hashchange', markNav);

  /* swipe helper for touch screens */
  const swipe = (el, onLeft, onRight) => {
    let x0=null, y0=null;
    el.addEventListener('touchstart', e => { x0=e.touches[0].clientX; y0=e.touches[0].clientY; }, {passive:true});
    el.addEventListener('touchend', e => {
      if (x0===null) return; const dx=e.changedTouches[0].clientX-x0, dy=e.changedTouches[0].clientY-y0; x0=null;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)*1.5) (dx<0 ? onLeft : onRight)();
    }, {passive:true});
  };

  /* reveal */
  const io = 'IntersectionObserver' in window ? new IntersectionObserver(es => es.forEach(e => { if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target);} }), {threshold:.12}) : null;
  const reveal = root => (root||document).querySelectorAll('.reveal:not(.in)').forEach((el,i) => { el.style.transitionDelay = (i%4)*80+'ms'; io ? io.observe(el) : el.classList.add('in'); });
  reveal();

  /* favourites */
  const favs = new Set(store.get('alc-favs', []));
  const listeners = [];
  const syncFavs = () => {
    $$('[data-id] .heart').forEach(h => h.classList.toggle('on', favs.has(h.closest('[data-id]').dataset.id)));
    $$('#favCount,[data-favcount]').forEach(n => { n.textContent=favs.size; n.classList.toggle('show', favs.size>0); });
    store.set('alc-favs', [...favs]);
    listeners.forEach(f => f());
  };
  const toggleFav = (id, el) => {
    const on=!favs.has(id); on?favs.add(id):favs.delete(id);
    if(el){ el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop'); }
    toast(on?'Saved to your recipes ♥':'Removed from saved'); syncFavs();
  };
  document.addEventListener('click', e => { const h=e.target.closest('.heart'); if(h){ e.preventDefault(); toggleFav(h.closest('[data-id]').dataset.id, h); } });
  $('#favBtn').onclick = () => { location.href = L.recipes + '#saved'; if(page==='recipes') dispatchEvent(new HashChangeEvent('hashchange')); };

  /* search (overlay injected) */
  document.body.insertAdjacentHTML('beforeend', `
  <div class="overlay" id="searchOv" role="dialog" aria-modal="true" aria-label="Search recipes">
    <div class="modal">
      <div class="search-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--sage-d)" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
        <input id="q" placeholder="Search recipes, ingredients…" autocomplete="off" aria-label="Search">
        <button class="close" data-close aria-label="Close">✕</button>
      </div>
      <div class="results" id="results"></div>
      <div class="hint" id="hint">Popular searches:
        <div class="pills" style="justify-content:flex-start;margin-top:8px">
          <button class="pill" data-q="chicken">chicken</button><button class="pill" data-q="pasta">pasta</button><button class="pill" data-q="pesto">pesto</button><button class="pill" data-q="30 minute">30 minute</button><button class="pill" data-q="baking">baking</button>
        </div>
      </div>
    </div>
  </div>`);
  const INDEX = [
    ...D.recipes.map(r => ({t:r.title, s:r.categories.map(x=>(cat(x)||{}).name).filter(Boolean).join(' · ')+' · '+time(r.prep+r.cook), k:kw(r), img:r.image, href:recipeUrl(r)})),
    ...D.categories.map(c => ({t:c.name, s:c.seasonal ? 'Category · '+D.season.name : 'Category', k:c.blurb+(c.seasonal?' '+D.season.name+' seasonal christmas easter halloween summer':''), img:c.image||(D.recipes[0]||{}).image||'', href:catUrl(c.slug)})),
    {t:'All Recipes', s:'Page', k:'recipes all browse index', img:(D.recipes[0]||{}).image||'', href:L.recipes},
    {t:'About Me', s:'Page', k:'about amy mum story contact', img:D.aboutImage||(D.recipes[0]||{}).image||'', href:L.about}
  ];
  let sel=0, hits=[];
  const hl = (t,q) => { if(!q) return esc(t); const i=t.toLowerCase().indexOf(q); return i<0?esc(t):esc(t.slice(0,i))+'<mark>'+esc(t.slice(i,i+q.length))+'</mark>'+esc(t.slice(i+q.length)); };
  const doSearch = () => {
    const q=$('#q').value.trim().toLowerCase();
    $('#hint').style.display = q ? 'none' : '';
    hits = q ? INDEX.filter(r => (r.t+' '+r.k).toLowerCase().includes(q)) : [];
    sel=0;
    $('#results').innerHTML = q ? (hits.length ? hits.map((r,i)=>`<a class="result${i===0?' sel':''}" href="${r.href}" data-i="${i}"><div class="th"><img src="${r.img}" alt=""></div><div><b>${hl(r.t,q)}</b><span class="meta">${r.s}</span></div></a>`).join('')
      : `<div class="hint">No recipes for “${esc(q)}” yet. Amy’s adding new ones all the time!</div>`) : '';
  };
  const openSearch = () => { $('#searchOv').classList.add('open'); document.body.classList.add('lock'); $('#q').value=''; doSearch(); setTimeout(()=>$('#q').focus(),50); };
  const closeSearch = () => { $('#searchOv').classList.remove('open'); document.body.classList.remove('lock'); };
  $('#searchBtn').onclick = openSearch; $('#tabSearch').onclick = openSearch;
  $('#q').oninput = doSearch;
  $('#q').onkeydown = e => {
    const rs=$$('.result'); if(e.key==='ArrowDown'||e.key==='ArrowUp'){ e.preventDefault(); if(!rs.length) return; sel=(sel+(e.key==='ArrowDown'?1:-1)+rs.length)%rs.length; rs.forEach((r,i)=>r.classList.toggle('sel',i===sel)); }
    if(e.key==='Enter' && hits[sel]){ closeSearch(); location.href = hits[sel].href; }
  };
  $('#results').addEventListener('click', () => closeSearch());
  $$('#hint [data-q]').forEach(p => p.onclick = () => { $('#q').value=p.dataset.q; doSearch(); $('#q').focus(); });
  $('#searchOv').onclick = e => { if(e.target.id==='searchOv' || e.target.closest('[data-close]')) closeSearch(); };
  document.addEventListener('keydown', e => {
    if(e.key==='Escape'){ closeSearch(); setDrawer(false); }
    if(e.key==='/' && !document.body.classList.contains('lock') && !document.body.classList.contains('drawer-open') && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)){ e.preventDefault(); openSearch(); }
  });

  /* newsletter */
  const nf = $('#newsForm');
  if (nf) {
    nf.onsubmit = e => {
      e.preventDefault(); const em=$('#email'), v=em.value.trim();
      if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)){ em.classList.add('bad'); $('#err').textContent='Please enter a valid email address.'; em.focus(); return; }
      em.classList.remove('bad');
      $('#newsWrap').innerHTML = '<p class="serif" style="font-size:28px;margin:0">Thank you! 💕</p><p style="margin-top:6px">You’re on the list. Look out for my next monthly email.</p>';
    };
    $('#email').oninput = () => { $('#email').classList.remove('bad'); $('#err').textContent=''; };
  }

  syncFavs();
  window.ALC = { $, $$, store, toast, favs, toggleFav, onFavs: f => listeners.push(f), reveal, esc, swipe,
    data: D, links: L, kw, cat, recipeUrl, catUrl, inCat, time, cardHTML, soonCard, catThumb, syncFavs };
})();

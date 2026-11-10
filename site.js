/* Shared behaviour for every page: header, reveal, toast, favourites, search, newsletter */
(() => {
  const $ = s => document.querySelector(s), $$ = s => [...document.querySelectorAll(s)];
  const store = {
    get(k, d){ try{ const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; }catch(e){ return d; } },
    set(k, v){ try{ localStorage.setItem(k, JSON.stringify(v)); }catch(e){} }
  };

  /* toast + back to top (injected) */
  document.body.insertAdjacentHTML('beforeend', '<div class="toast" id="toast" role="status"></div><button class="top-btn" id="topBtn" aria-label="Back to top">↑</button><div class="mock-badge">Design mock‑up</div>');
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
      <div class="drawer-cta"><p><b class="serif" style="font-size:19px">New recipes, straight to your inbox</b><br>Be the first to hear when new recipes go live.</p><a class="btn" href="#newsletter" data-close-drawer>Sign me up</a></div>
      ${$('footer .socials') ? '<div class="socials">'+$('footer .socials').innerHTML+'</div>' : ''}
    </aside>
    <nav class="tabbar-m" aria-label="Quick navigation">
      <a href="index.html" data-tab="home">${ico.home}Home</a>
      <a href="recipes.html" data-tab="recipes">${ico.book}Recipes</a>
      <button id="tabSearch" data-tab="search">${ico.search}Search</button>
      <a href="recipes.html#saved" data-tab="saved">${ico.heart}Saved<span class="count" data-favcount>0</span></a>
      <a href="about.html" data-tab="about">${ico.user}About</a>
    </nav>`);
  const setDrawer = open => {
    document.body.classList.toggle('drawer-open', open);
    $('#drawer').setAttribute('aria-hidden', !open);
    $('#menuBtn').setAttribute('aria-expanded', open);
    if (open) $('#drawerClose').focus();
  };
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
  $('#favBtn').onclick = () => { location.href = 'recipes.html#saved'; if(page==='recipes') dispatchEvent(new HashChangeEvent('hashchange')); };

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
    {t:'Creamy Chicken Pesto Pasta', s:'Easy Family Dinners · 30 Minute Meals · 20 mins', k:'chicken pasta pesto creamy mushrooms mozzarella parmesan dinner 30 minute quick', img:'img/pasta.jpg', href:'index.html#recipe'},
    {t:'Easy Family Dinners', s:'Category', k:'dinner family weeknight', img:'img/sq-7-1.jpg', href:'recipes.html#dinners'},
    {t:'30 Minute Meals', s:'Category', k:'30 minute quick fast', img:'img/pasta.jpg', href:'recipes.html#30'},
    {t:'Slow Cooker Stash', s:'Category', k:'slow cooker stew casserole', img:'img/sq-33-1.jpg', href:'recipes.html#slow'},
    {t:'Lunchbox Line Up', s:'Category', k:'lunchbox lunch school kids packed', img:'img/hero.jpg', href:'recipes.html#lunch'},
    {t:'Baking', s:'Category', k:'baking cake cookies bake sweet treats', img:'img/sq-21.jpg', href:'recipes.html#baking'},
    {t:'All Recipes', s:'Page', k:'recipes all browse index', img:'img/sq-7-1.jpg', href:'recipes.html'},
    {t:'About Me', s:'Page', k:'about amy mum story contact', img:'img/sq-21.jpg', href:'about.html'}
  ];
  let sel=0, hits=[];
  const esc = s => String(s).replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
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
    if(e.key==='/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)){ e.preventDefault(); openSearch(); }
  });

  /* newsletter */
  const nf = $('#newsForm');
  if (nf) {
    nf.onsubmit = e => {
      e.preventDefault(); const em=$('#email'), v=em.value.trim();
      if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)){ em.classList.add('bad'); $('#err').textContent='Please enter a valid email address.'; em.focus(); return; }
      em.classList.remove('bad');
      $('#newsWrap').innerHTML = '<p class="serif" style="color:#fff;font-size:26px;margin:0">Thank you! 💛</p><p style="margin-top:6px">You’re on the list. Keep an eye on your inbox for new recipes.</p>';
    };
    $('#email').oninput = () => { $('#email').classList.remove('bad'); $('#err').textContent=''; };
  }

  syncFavs();
  window.ALC = { $, $$, store, toast, favs, toggleFav, onFavs: f => listeners.push(f), reveal, esc, swipe };
})();

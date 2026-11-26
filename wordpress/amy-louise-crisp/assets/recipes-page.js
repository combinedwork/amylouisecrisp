(() => {
  const { $, $$, data: D, kw, cat, inCat, cardHTML, soonCard, catThumb, favs, onFavs, esc, syncFavs } = window.ALC;
  const ALL = { name: 'All Recipes', blurb: 'Simple, family‑friendly recipes you’ll actually want to make again and again. Search, filter by category or save your favourites for later.' };
  const SAVED = { name: 'Saved Recipes', blurb: 'Your favourites, all in one place. Tap the ♡ on any recipe to add it here.' };
  const info = f => f === 'all' ? ALL : f === 'saved' ? SAVED : cat(f);

  $('#filters').innerHTML = `<button data-filter="all">All</button>` +
    D.categories.map(c => `<button data-filter="${c.slug}">${esc(c.name)}</button>`).join('') + `<button data-filter="saved">♥ Saved</button>`;

  let filter = 'all', q = '', sort = 'new', view = 'grid';
  try { view = localStorage.getItem('alc-view') || 'grid'; } catch (e) {}

  const render = () => {
    const c = info(filter), seasonal = c.seasonal;
    $('#pageTitle').textContent = seasonal ? `${c.name}: ${D.season.name}` : c.name;
    $('#crumb').textContent = filter === 'all' ? 'Recipes' : c.name;
    $('#pageSub').textContent = seasonal ? D.season.text : c.blurb;
    $('#seasonBadge').hidden = !seasonal; $('#seasonBadge').textContent = seasonal ? `${D.season.emoji || ''} In season now` : '';
    document.title = c.name + ' · Amy Louise Crisp';
    $$('#filters button').forEach(b => b.classList.toggle('on', b.dataset.filter === filter));
    $$('[data-nav]').forEach(a => a.classList.toggle('on', a.dataset.nav === 'recipes' && (a.getAttribute('href') === 'recipes.html#' + filter || (filter === 'all' && a.getAttribute('href') === 'recipes.html'))));
    $$('.tabbar-m [data-tab]').forEach(t => t.classList.toggle('on', t.dataset.tab === (filter === 'saved' ? 'saved' : 'recipes')));
    const showIntro = filter !== 'all' && filter !== 'saved';
    $('#catIntro').classList.toggle('show', showIntro);
    if (showIntro) { $('#ciTh').className = 'th' + (c.image ? '' : ' festive-th'); $('#ciTh').innerHTML = catThumb(c); $('#ciTitle').textContent = c.name; $('#ciText').textContent = seasonal ? D.season.text : c.blurb; }

    const ql = q.toLowerCase();
    let list = D.recipes.filter(r => (filter === 'all' || (filter === 'saved' ? favs.has(r.slug) : r.categories.includes(filter)))
      && (!ql || kw(r).includes(ql)));
    const order = D.recipes.slice();
    list.sort(sort === 'quick' ? (a, b) => (a.prep + a.cook) - (b.prep + b.cook) : sort === 'az' ? (a, b) => a.title.localeCompare(b.title) : (a, b) => order.indexOf(a) - order.indexOf(b));
    // "Coming soon" cards for categories that don't have recipes yet (not while searching or viewing saved)
    const soon = q || filter === 'saved' ? [] : (filter === 'all' ? D.categories.filter(k => k.group !== 'tag' && !inCat(k.slug).length) : (inCat(filter).length ? [] : [c]));

    const grid = $('#grid'); grid.classList.toggle('list', view === 'list');
    grid.innerHTML = list.map(r => cardHTML(r, 'in')).join('') + soon.map(k => soonCard(k, 'in')).join('');
    $('#count').innerHTML = list.length || soon.length ? `Showing <b>${list.length}</b> recipe${list.length === 1 ? '' : 's'}${soon.length ? ` · ${soon.length} collection${soon.length > 1 ? 's' : ''} coming soon` : ''}${q ? ` for “${esc(q)}”` : ''}` : '';
    $('#empty').classList.toggle('show', !list.length && !soon.length);
    $('#emptyText').textContent = filter === 'saved' && !q ? 'Tap the ♡ on any recipe to save it for later.' : `No recipes match “${q}” yet. Amy’s adding new ones all the time!`;
    $$('.view-toggle button').forEach(b => b.classList.toggle('on', b.dataset.view === view));
    syncFavs();
  };

  const fromHash = () => { const h = decodeURIComponent(location.hash.slice(1)); filter = h === 'saved' || cat(h) ? h : 'all'; render(); };
  $$('#filters button').forEach(b => b.onclick = () => { history.replaceState(null, '', b.dataset.filter === 'all' ? 'recipes.html' : '#' + b.dataset.filter); filter = b.dataset.filter; render(); });
  let deb; $('#find').oninput = e => { clearTimeout(deb); deb = setTimeout(() => { q = e.target.value.trim(); render(); }, 120); };
  $('#sort').onchange = e => { sort = e.target.value; render(); };
  $$('.view-toggle button').forEach(b => b.onclick = () => { view = b.dataset.view; try { localStorage.setItem('alc-view', view); } catch (e) {} render(); });
  $('#clearBtn').onclick = () => { q = ''; $('#find').value = ''; filter = 'all'; history.replaceState(null, '', 'recipes.html'); render(); };
  addEventListener('hashchange', fromHash);
  onFavs(() => { if (filter === 'saved') render(); });
  $$('[data-cat-strip] a').forEach(a => a.addEventListener('click', () => scrollTo({ top: 0 })));
  fromHash();
})();

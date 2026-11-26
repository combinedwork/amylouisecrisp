(() => {
  const { $, data: D, links: L, cat, catUrl, inCat, cardHTML, soonCard, renderRecipe, esc, syncFavs, reveal } = window.ALC;
  // WordPress tells us which recipe this page is; the static mock-up uses ?r=slug
  const slug = D.current || new URLSearchParams(location.search).get('r');
  const r = D.recipes.find(x => x.slug === slug) || D.recipes[0];
  if (!r) return;
  const c = cat(r.categories[0]) || { slug: '', name: 'Recipes' };

  if (!D.current) {
    document.title = r.title + ' · Amy Louise Crisp';
    $('#crumbs').innerHTML = `<a href="${L.home}">Home</a> / <a href="${L.recipes}">Recipes</a> / <a href="${catUrl(c.slug)}">${esc(c.name)}</a> / <span>${esc(r.title)}</span>`;
  }
  renderRecipe($('#recipe'), r, { h1: true });

  // More from the same collection, topped up with "coming soon" cards
  const others = D.recipes.filter(x => x !== r && x.categories.some(k => r.categories.includes(k))).slice(0, 4);
  const fill = D.categories.filter(k => k.group === 'main' && !inCat(k.slug).length).slice(0, Math.max(0, 4 - others.length));
  $('#moreTitle').textContent = 'More ' + c.name;
  $('#moreLink').href = c.slug ? catUrl(c.slug) : L.recipes;
  $('#moreGrid').innerHTML = others.map(x => cardHTML(x, 'reveal')).join('') + fill.map(k => soonCard(k, 'reveal')).join('');
  if (!others.length && !fill.length) $('#more').hidden = true;
  syncFavs(); reveal();

  // Recipe details for Google. In WordPress the plugin prints this on the page instead.
  if (D.current) return;
  const iso = m => 'PT' + m + 'M';
  const ld = { '@context': 'https://schema.org', '@type': 'Recipe', name: r.title, description: r.summary,
    image: [new URL(r.image, location.href).href], author: { '@type': 'Person', name: 'Amy Louise Crisp' },
    prepTime: iso(r.prep), cookTime: iso(r.cook), totalTime: iso(r.prep + r.cook), recipeYield: String(r.serves),
    recipeCategory: r.categories.map(k => (cat(k) || {}).name).filter(Boolean),
    recipeIngredient: r.ingredients.flatMap(g => g.items.map(([q, u, n]) => (q == null ? '' : q + (u ? ' ' + u : '') + ' ') + n)),
    recipeInstructions: r.method.map(t => ({ '@type': 'HowToStep', text: t })) };
  if (r.video) ld.video = { '@type': 'VideoObject', name: r.video.title, embedUrl: 'https://www.youtube.com/embed/' + r.video.youtube, thumbnailUrl: new URL(r.image, location.href).href };
  const s = document.createElement('script'); s.type = 'application/ld+json'; s.textContent = JSON.stringify(ld); document.head.appendChild(s);
})();

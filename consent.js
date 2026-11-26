/* =========================================================================
   Cookie consent (UK GDPR / PECR, ICO guidance)
   - Nothing optional loads until the visitor chooses.
   - "Accept all" and "Reject all" are equally easy; "Choose" picks categories.
   - Scripts that need consent are written inside
       <template data-consent="analytics|ads|media"> … </template>
     and are only added to the page once that category is allowed.
   - Footer link [data-cookie-settings] reopens the choices any time.
   Settings come from window.ALC_CONSENT (WordPress) or the defaults below.
   ========================================================================= */
(() => {
  const C = Object.assign({
    enabled: true,
    privacyUrl: 'privacy.html',
    heading: 'Cookies & your privacy',
    text: 'I use a few cookies to understand how the site is used and, in future, to show adverts that keep my recipes free. Saved recipes stay on your device. You choose what’s allowed.',
    categories: ['analytics', 'ads', 'media'],
    version: 1
  }, window.ALC_CONSENT || {});

  const LABELS = {
    necessary: ['Essential', 'Keeps the site working and remembers your cookie choice, saved recipes and view settings. Always on.'],
    analytics: ['Visitor statistics', 'Helps me see which recipes are popular, using anonymous statistics (e.g. Google Analytics).'],
    ads: ['Advertising', 'Lets adverts be shown, which help keep the recipes free. Advertisers may use cookies to choose and measure adverts.'],
    media: ['Videos', 'Plays recipe videos from YouTube, which may set its own cookies.']
  };
  const KEY = 'alc-consent';
  const read = () => { try { const v = JSON.parse(localStorage.getItem(KEY)); return v && v.v === C.version ? v : null; } catch (e) { return null; } };
  const write = v => { try { localStorage.setItem(KEY, JSON.stringify(v)); } catch (e) {} };
  let state = read();

  const allowed = cat => !C.enabled || cat === 'necessary' || !!(state && state[cat]);

  // Add any held-back scripts for categories that are now allowed
  const run = () => {
    document.querySelectorAll('template[data-consent]').forEach(t => {
      if (t.dataset.done || !allowed(t.dataset.consent)) return;
      t.dataset.done = '1';
      const frag = t.content.cloneNode(true);
      // Scripts copied from a template don't run, so recreate them
      frag.querySelectorAll('script').forEach(old => {
        const s = document.createElement('script');
        [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));
        s.text = old.textContent;
        old.replaceWith(s);
      });
      t.parentNode.insertBefore(frag, t.nextSibling);
    });
    document.dispatchEvent(new CustomEvent('alc:consent', { detail: state }));
  };

  const save = prefs => {
    const before = state;
    state = Object.assign({ v: C.version, ts: new Date().toISOString() }, prefs);
    // Something that was allowed is now switched off: reload so it stops straight away
    const withdrawn = before && C.categories.some(c => before[c] && !state[c]);
    write(state);
    close();
    if (withdrawn && document.querySelector('template[data-consent][data-done]')) { location.reload(); return; }
    run();
  };
  const all = on => C.categories.reduce((o, c) => (o[c] = on, o), {});

  /* ---------- banner + preferences ---------- */
  const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  let box = null;
  const close = () => { if (box) { box.remove(); box = null; } document.body.classList.remove('consent-open'); };
  const open = (showPrefs) => {
    close();
    const cur = state || {};
    box = document.createElement('div');
    box.className = 'consent' + (showPrefs ? ' consent--prefs' : '');
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-labelledby', 'consentTitle');
    box.setAttribute('aria-describedby', 'consentText');
    box.innerHTML = `
      <div class="consent-card">
        <h2 id="consentTitle" class="consent-title">🍪 ${esc(C.heading)}</h2>
        <p id="consentText">${esc(C.text)} <a href="${esc(C.privacyUrl)}">Privacy &amp; cookie policy</a></p>
        <div class="consent-prefs" ${showPrefs ? '' : 'hidden'}>
          ${['necessary'].concat(C.categories).map(c => `
            <label class="consent-row">
              <span><b>${esc(LABELS[c][0])}</b><small>${esc(LABELS[c][1])}</small></span>
              <input type="checkbox" role="switch" data-cat="${c}" ${c === 'necessary' ? 'checked disabled' : (cur[c] ? 'checked' : '')}>
            </label>`).join('')}
        </div>
        <div class="consent-actions">
          <button type="button" class="btn consent-btn" data-act="reject">Reject all</button>
          <button type="button" class="btn consent-btn" data-act="accept">Accept all</button>
          <button type="button" class="btn line consent-choose" data-act="${showPrefs ? 'save' : 'choose'}">${showPrefs ? 'Save my choices' : 'Choose'}</button>
        </div>
      </div>`;
    document.body.appendChild(box);
    document.body.classList.add('consent-open');
    box.addEventListener('click', e => {
      const act = e.target.closest('[data-act]'); if (!act) return;
      const a = act.dataset.act;
      if (a === 'accept') save(all(true));
      if (a === 'reject') save(all(false));
      if (a === 'choose') { box.classList.add('consent--prefs'); box.querySelector('.consent-prefs').hidden = false; act.dataset.act = 'save'; act.textContent = 'Save my choices'; box.querySelector('[data-cat="analytics"],[data-cat]:not([disabled])').focus(); }
      if (a === 'save') { const p = {}; box.querySelectorAll('[data-cat]:not([disabled])').forEach(i => { p[i.dataset.cat] = i.checked; }); save(p); }
    });
    // Focus the banner itself (not "Accept all"), so neither choice is nudged
    const card = box.querySelector('.consent-card'); card.tabIndex = -1;
    setTimeout(() => { const f = box && (showPrefs ? box.querySelector('[data-cat]:not([disabled])') : card); if (f) f.focus({ preventScroll: true }); }, 50);
  };

  document.addEventListener('click', e => {
    const l = e.target.closest('[data-cookie-settings],a[href$="#cookie-settings"]');
    if (l) { e.preventDefault(); open(true); }
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && box && state) close(); });

  window.ALCConsent = {
    allowed,
    allow: cat => save(Object.assign({}, all(false), state || {}, { [cat]: true })),
    open: () => open(true),
    get: () => state
  };

  // Ask again if the site has started using a kind of cookie this visitor hasn't been asked about
  const unanswered = () => !state || C.categories.some(c => !(c in state));
  const start = () => { run(); if (C.enabled && unanswered()) open(false); };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
})();

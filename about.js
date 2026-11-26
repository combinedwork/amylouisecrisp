(() => {
  const { $, $$, toast, swipe } = window.ALC;

  /* gallery lightbox */
  const imgs = $$('#gallery img'); let gi = 0;
  const show = i => { gi=(i+imgs.length)%imgs.length; $('#lbImg').src=imgs[gi].src; $('#lbImg').alt=imgs[gi].alt; };
  const lbOpen = i => { show(i); $('#lightbox').classList.add('open'); document.body.classList.add('lock'); $('#lbClose').focus(); };
  const lbClose = () => { $('#lightbox').classList.remove('open'); document.body.classList.remove('lock'); };
  $$('#gallery button').forEach(b => b.onclick = () => lbOpen(+b.dataset.i));
  $('#lbClose').onclick = lbClose; $('#lbPrev').onclick = () => show(gi-1); $('#lbNext').onclick = () => show(gi+1);
  swipe($('#lightbox'), () => show(gi+1), () => show(gi-1));
  $('#lightbox').onclick = e => { if(e.target.id==='lightbox') lbClose(); };
  document.addEventListener('keydown', e => { if(!$('#lightbox').classList.contains('open')) return;
    if(e.key==='Escape') lbClose(); if(e.key==='ArrowLeft') show(gi-1); if(e.key==='ArrowRight') show(gi+1); });

  /* FAQ: one open at a time */
  $$('.faq-list details').forEach(d => d.addEventListener('toggle', () => { if(d.open) $$('.faq-list details').forEach(o => { if(o!==d) o.open=false; }); }));

  /* contact form */
  $('#cMsg').oninput = e => { $('#cCnt').textContent = e.target.value.length+' / 600'; e.target.classList.remove('bad'); };
  ['#cName','#cEmail'].forEach(s => $(s).addEventListener('input', e => e.target.classList.remove('bad')));
  $('#contactForm').onsubmit = e => {
    e.preventDefault();
    const n=$('#cName'), em=$('#cEmail'), m=$('#cMsg'); const bad=[];
    if(!n.value.trim()) bad.push([n,'your name']);
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value.trim())) bad.push([em,'a valid email']);
    if(m.value.trim().length<5) bad.push([m,'a message']);
    bad.forEach(([el])=>el.classList.add('bad'));
    if(bad.length){ $('#cErr').textContent = 'Please add '+bad.map(b=>b[1]).join(', ').replace(/, ([^,]*)$/,' and $1')+'.'; bad[0][0].focus(); return; }
    const first = n.value.trim().split(' ')[0].replace(/[<>&"]/g,'');
    $('#contactWrap').innerHTML = `<form style="display:block"><div class="sent"><div class="big">💌</div><h3 style="font-size:26px;margin:8px 0">Thanks, ${first}!</h3><p style="color:var(--muted);margin:0">Your message is on its way. I’ll get back to you as soon as I can.</p></div></form>`;
    toast('Message sent');
  };
})();

"""
One source for the Privacy & Cookie Policy.
Builds:
  privacy.html                                  (static mock-up page)
  wordpress/amy-louise-crisp/inc/privacy-content.php  (WordPress block content)
Run from the repository root:  python3 wordpress/privacy-source.py
Placeholders filled in later: {SITE} {EMAIL} {DATE} {URL}
"""
import html, re, os

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

POLICY = [
    ('p', 'This policy explains how I, {SITE}, collect and use personal information when you visit {URL}, sign up to my newsletter or get in touch. I keep things simple: I only collect what I need, I never sell your information, and anything optional only happens if you say yes.'),
    ('p', '<strong>Last updated:</strong> {DATE}'),

    ('h2', 'Who I am'),
    ('p', 'This website is run by {SITE}. For anything to do with your personal information, email me at <a href="mailto:{EMAIL}">{EMAIL}</a>.'),

    ('h2', 'What I collect and why'),
    ('h3', 'Newsletter'),
    ('p', 'If you sign up, I collect your email address (and your name if you give it) so I can send my monthly email. My newsletter is sent using an email service provider, which stores your details on my behalf. I rely on your <strong>consent</strong>, and every email has an unsubscribe link. I keep your details until you unsubscribe.'),
    ('h3', 'Emails and messages'),
    ('p', 'If you email me or use a contact form, I use what you send (usually your name, email address and message) to reply. I keep messages for as long as I need to deal with them, and then for a reasonable time in case you get back in touch. This is in my <strong>legitimate interest</strong> in replying to you.'),
    ('h3', 'Visitor statistics (only if you allow them)'),
    ('p', 'With your permission, I use analytics tools such as Google Analytics to see which recipes are popular and how people find the site. This collects information like the pages you visit, the type of device and browser you use, and your approximate location (town or region). It does not tell me who you are. I rely on your <strong>consent</strong>, which you can change at any time.'),
    ('h3', 'Advertising (only if you allow it)'),
    ('p', 'In the future I may show adverts to help keep my recipes free. If I do, and you allow advertising cookies, my advertising partner may use cookies and similar technologies to choose and measure adverts. I will name my advertising partner on this page when adverts start. I rely on your <strong>consent</strong>.'),
    ('h3', 'Recipe videos'),
    ('p', 'Recipe videos are hosted on YouTube. They use YouTube’s privacy-enhanced mode and only load when you press play and allow videos. YouTube may then set its own cookies, as described in Google’s privacy policy.'),
    ('h3', 'Saved recipes and settings'),
    ('p', 'When you save a recipe with the ♡, or choose grid or list view, this is stored <strong>only on your own device</strong> (in your browser’s storage). It is never sent to me. You can remove it at any time by clearing your browser data.'),
    ('h3', 'Keeping the website running'),
    ('p', 'Like all websites, my hosting company keeps basic technical logs (such as IP addresses) to keep the site secure and working. This is in my <strong>legitimate interest</strong> in running a safe website.'),

    ('h2', 'Cookies'),
    ('p', 'Cookies are small files stored on your device. I only use essential ones without asking. Everything else is off until you choose.'),
    ('table', [
        ['Name', 'What it does', 'Type', 'How long'],
        ['alc-consent', 'Remembers your cookie choices', 'Essential', 'Until you change them'],
        ['alc-favs', 'Your saved recipes (on your device only)', 'Essential', 'Until you clear them'],
        ['alc-view', 'Remembers grid or list view', 'Essential', 'Until you clear it'],
        ['_ga, _ga_*', 'Google Analytics visitor statistics', 'Statistics (only with permission)', 'Up to 2 years'],
        ['YouTube cookies (e.g. YSC, VISITOR_INFO1_LIVE)', 'Playing recipe videos', 'Videos (only with permission)', 'Up to 6 months'],
        ['Advertising partner cookies', 'Showing and measuring adverts', 'Advertising (only with permission)', 'Set by the partner'],
        ['wordpress_*', 'Keeps the site owner logged in', 'Essential (site owner only)', 'Up to 14 days'],
    ]),
    ('cookiebtn', 'Change my cookie settings'),

    ('h2', 'Who I share information with'),
    ('p', 'I never sell your personal information. I only share it with trusted services that help me run the site, such as my website host, my newsletter provider, Google (for statistics and YouTube) and, if adverts are shown, my advertising partner. They may only use it to provide their service to me.'),
    ('p', 'Some of these services are based outside the UK, for example in the USA. Where this happens, they use approved safeguards (such as the UK International Data Transfer Agreement or the UK–US data bridge) to protect your information.'),

    ('h2', 'Your rights'),
    ('p', 'Under UK data protection law you have the right to:'),
    ('ul', [
        'ask for a copy of the information I hold about you',
        'ask me to correct or delete it',
        'object to, or ask me to limit, how I use it',
        'ask for your information in a format you can take elsewhere',
        'withdraw your consent at any time (for example, by unsubscribing or changing your cookie settings)',
    ]),
    ('p', 'To use any of these rights, email <a href="mailto:{EMAIL}">{EMAIL}</a>. I will reply within one month.'),
    ('p', 'If you are unhappy with how I have handled your information, you can complain to the Information Commissioner’s Office (ICO) at <a href="https://ico.org.uk/make-a-complaint/">ico.org.uk</a> or on 0303 123 1113. I would always appreciate the chance to put things right first.'),

    ('h2', 'Children'),
    ('p', 'My recipes are made for families, but this website is not aimed at children, and I do not knowingly collect personal information from children under 13. If you think a child has given me their details, please get in touch and I will delete them.'),

    ('h2', 'Changes to this policy'),
    ('p', 'I may update this policy from time to time, for example when I start showing adverts. The date at the top shows when it was last changed.'),
]


def text_only(s):
    return re.sub(r'<[^>]+>', '', s)


def to_html(items):
    out = []
    for kind, val in items:
        if kind in ('h2', 'h3'):
            out.append(f'<{kind}>{val}</{kind}>')
        elif kind == 'p':
            out.append(f'<p>{val}</p>')
        elif kind == 'ul':
            out.append('<ul>' + ''.join(f'<li>{li}</li>' for li in val) + '</ul>')
        elif kind == 'table':
            head, *rows = val
            out.append('<div class="table-wrap"><table><thead><tr>' + ''.join(f'<th>{h}</th>' for h in head) + '</tr></thead><tbody>'
                       + ''.join('<tr>' + ''.join(f'<td>{c}</td>' for c in r) + '</tr>' for r in rows) + '</tbody></table></div>')
        elif kind == 'cookiebtn':
            out.append(f'<p><a class="btn line cookie-btn" href="#cookie-settings" data-cookie-settings>{val}</a></p>')
    return '\n'.join(out)


def to_blocks(items):
    out = []
    for kind, val in items:
        if kind == 'h2':
            out.append(f'<!-- wp:heading --><h2 class="wp-block-heading">{val}</h2><!-- /wp:heading -->')
        elif kind == 'h3':
            out.append(f'<!-- wp:heading {{"level":3}} --><h3 class="wp-block-heading">{val}</h3><!-- /wp:heading -->')
        elif kind == 'p':
            out.append(f'<!-- wp:paragraph --><p>{val}</p><!-- /wp:paragraph -->')
        elif kind == 'ul':
            out.append('<!-- wp:list --><ul>' + ''.join(f'<!-- wp:list-item --><li>{li}</li><!-- /wp:list-item -->' for li in val) + '</ul><!-- /wp:list -->')
        elif kind == 'table':
            head, *rows = val
            out.append('<!-- wp:table --><figure class="wp-block-table"><table><thead><tr>' + ''.join(f'<th>{h}</th>' for h in head) + '</tr></thead><tbody>'
                       + ''.join('<tr>' + ''.join(f'<td>{c}</td>' for c in r) + '</tr>' for r in rows) + '</tbody></table></figure><!-- /wp:table -->')
        elif kind == 'cookiebtn':
            out.append('<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline">'
                       f'<a class="wp-block-button__link wp-element-button" href="#cookie-settings">{val}</a></div><!-- /wp:button --></div><!-- /wp:buttons -->')
    return '\n\n'.join(out)


# ---- static page (built from about.html's header/footer) ----
about = open(os.path.join(ROOT, 'about.html'), encoding='utf-8').read()
head = about[:about.index('<main>')]
foot = about[about.index('</main>'):]
head = head.replace('<title>About Me · Amy Louise Crisp</title>', '<title>Privacy & Cookie Policy · Amy Louise Crisp</title>').replace('data-page="about"', 'data-page="privacy"')
foot = re.sub(r'<script src="about.js"></script>\s*', '', foot)
fill = lambda s: s.replace('{SITE}', 'Amy Louise Crisp').replace('{EMAIL}', 'hello@amylouisecrisp.co.uk').replace('{URL}', 'amylouisecrisp.co.uk').replace('{DATE}', '26 November 2026')
body = f'''<main>
  <div class="page-hero page-hero--slim">
    <div class="wrap">
      <div class="crumbs"><a href="index.html">Home</a> / <span>Privacy &amp; Cookie Policy</span></div>
      <h1>Privacy &amp; Cookie Policy</h1>
    </div>
  </div>
  <section class="page-body">
    <div class="wrap policy">
      <p class="note"><strong>Mock-up note:</strong> the email address below is a placeholder. On WordPress this page is filled in with the site’s real contact email.</p>
{fill(to_html(POLICY))}
    </div>
  </section>
'''
open(os.path.join(ROOT, 'privacy.html'), 'w', encoding='utf-8').write(head + body + foot)

# ---- WordPress block content ----
blocks = to_blocks(POLICY).replace("'", "\\'")
php = f"""<?php
/**
 * Privacy & Cookie Policy page content (generated by wordpress/privacy-source.py – edit there, or edit the page in WordPress).
 */
if ( ! defined( 'ABSPATH' ) ) {{
	exit;
}}

function alc_privacy_content() {{
	$content = '{blocks}';
	return strtr( $content, array(
		'{{SITE}}'  => esc_html( get_bloginfo( 'name' ) ),
		'{{EMAIL}}' => esc_html( get_option( 'admin_email' ) ),
		'{{URL}}'   => esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ),
		'{{DATE}}'  => esc_html( wp_date( 'j F Y' ) ),
	) );
}}
"""
open(os.path.join(ROOT, 'wordpress/amy-louise-crisp/inc/privacy-content.php'), 'w', encoding='utf-8').write(php)
print('built privacy.html and inc/privacy-content.php')

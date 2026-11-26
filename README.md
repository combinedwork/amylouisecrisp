# Amy Louise Crisp website

**Family recipes you’ll actually make**

This repository has two things:

1. **The WordPress website** (in the `wordpress` folder). This is the real site. Once it’s installed, Amy can
   change everything herself from her WordPress dashboard: no code, no help needed.
2. **The online mock-up** (the `.html` files at the top). The preview at
   https://combinedwork.github.io/amylouisecrisp/ that shows how the site looks and works.

---

## Contents

- [Installing on WordPress](#installing-on-wordpress) (once, by whoever sets the site up)
- [Your dashboard at a glance](#your-dashboard-at-a-glance)
- [Adding a recipe](#adding-a-recipe)
- [Editing or removing a recipe](#editing-or-removing-a-recipe)
- [Changing photos](#changing-photos)
- [Collections (recipe categories)](#collections-recipe-categories)
- [Festive Favourites through the year](#festive-favourites-through-the-year)
- [“What are you in the mood for?” buttons](#what-are-you-in-the-mood-for-buttons)
- [Changing words on the homepage](#changing-words-on-the-homepage)
- [Editing the About Me page](#editing-the-about-me-page)
- [Menus](#menus)
- [Newsletter sign-up](#newsletter-sign-up)
- [Logo, site name, The Seasons font and social links](#logo-site-name-the-seasons-font-and-social-links)
- [Advertising (for later)](#advertising-for-later)
- [Privacy policy and cookie banner](#privacy-policy-and-cookie-banner)
- [If something goes wrong](#if-something-goes-wrong)
- [For developers](#for-developers)

---

## Installing on WordPress

You need two files from the `wordpress/dist` folder:

| File | What it is |
|---|---|
| `amy-recipes-plugin.zip` | **Amy’s Recipes** plugin: recipes, collections, Cook Mode, festive season, the Website Guide |
| `amy-louise-crisp-theme.zip` | **Amy Louise Crisp** theme: the design |

To download them from GitHub, open each file in the `wordpress/dist` folder and click **Download raw file**.

**Before you start:** back up the current site (most hosts have a one-click backup, or use a plugin such as
UpdraftPlus). If the host offers a *staging* copy, try it there first.

1. In WordPress go to **Plugins → Add New Plugin → Upload Plugin**, choose `amy-recipes-plugin.zip`,
   click **Install Now**, then **Activate Plugin**.
   *This creates the eight collections with photos and adds the Creamy Chicken Pesto Pasta recipe.*
2. Go to **Appearance → Themes → Add New Theme → Upload Theme**, choose `amy-louise-crisp-theme.zip`,
   click **Install Now**, then **Activate**.
3. Visit the dashboard once (**Dashboard → Home**). The theme finishes its setup: it creates the
   **About Me** page, the **Main menu** and **Footer menu**, and sets the tagline.
4. Go to **Settings → Permalinks** and click **Save Changes** (this refreshes web addresses).
5. View the site. Done!

Install the **plugin first**, then the theme, so the menus link to the right places.

**Moving from the current site:** the current site uses the Kadence theme and the WP Recipe Maker plugin.
The new plugin keeps its own recipes, so once you’re happy, you can deactivate WP Recipe Maker and
Kadence. Any other existing recipes need adding with the new recipe form (copy and paste works well).

---

## Your dashboard at a glance

After logging in you’ll see:

- **Your website** box on the Dashboard: one-click buttons for everything below.
- **Website Guide** (left menu, with a heart): a full help page inside WordPress, with buttons to each place.
- **Recipes** (left menu): *All Recipes*, *Add New Recipe*, *Collections*, *Festive Season & Moods*.
- **Appearance → Customise**: all homepage words and photos, newsletter, social links, logo and font,
  with a live preview before you publish.

---

## Adding a recipe

1. Go to **Recipes → Add New Recipe**.
2. Type the **recipe name** in the big box at the top.
3. Fill in **Recipe details**:

   | Box | What to type | Example |
   |---|---|---|
   | Short description | 1–2 sentences for recipe cards and the top of the recipe | *One of those easy family dinners you’ll make again and again!* |
   | Prep time / Cook time | minutes | `5` and `15` |
   | Serves | number of people | `4` |
   | Ingredients | **one per line, amount first** | `300g dried pasta` |
   | Method | **one step per line**, in order | `Heat the oil in a large frying pan.` |
   | Tips & storage | one per line, `Heading: tip` | `Freezing: Freeze in portions for up to 3 months.` |
   | YouTube video link | paste the link from YouTube’s **Share** button | `https://youtu.be/FvktW_5l0nY` |
   | Extra search words | anything people might search for | `kids, freezer friendly` |
   | “New” badge | tick to show a pink **New** label | |

4. On the right, tick the **Collections** it belongs to (as many as you like), e.g. *Easy Family Dinners*
   and *30 Minute Meals*.
5. On the right, click **Choose recipe photo** → **Upload files** (or pick one from the library) →
   **Choose recipe photo**.
6. Click **Publish**.

That’s it. Automatically:

- it gets its own page, e.g. `yoursite.co.uk/recipe/creamy-chicken-pesto-pasta/`
- it appears on the **Recipes** page, in **search**, in its **collections** and in the **mood picker**
- the newest recipe shows on the **homepage** (recipe card and Latest Recipes)
- **Cook Mode**, the **servings changer**, the **ingredient checklist**, the **Video** tab and the details
  Google uses for recipe search results are all created from what you typed

### Ingredient tips

- Start each line with the amount: `300g pasta`, `2 tbsp olive oil`, `½ jar pesto`, `1 1/2 tsp paprika`,
  `3 eggs`. These amounts change when visitors change the servings.
- Lines without an amount are fine (e.g. `Salt and pepper`). They just don’t change with servings.
- **Groups:** a line ending with a colon starts a new group, e.g. `For the sauce:`

  ```
  Pasta & Chicken:
  300g dried pasta
  1 tbsp olive oil
  500g chicken breasts, diced

  Creamy Pesto Sauce:
  50g butter
  ½ jar green pesto
  ```

### Method and Cook Mode tips

- Each line becomes one big, easy-to-read screen in **Cook Mode**. There’s no need to number them.
- **Timers:** put the time in square brackets at the end of a step, and Cook Mode has that timer ready on
  that step: `Simmer until thickened. [10 min]` or `Cook on low. [6 hr]`
- Cook Mode also shows **“You’ll need”** with the ingredients that step mentions, scaled to the servings.

### Check before you publish

After clicking **Save Draft** (or **Update**), the box **“How your recipe will look in Cook Mode”** shows
exactly how the recipe was read: each ingredient with its amount in bold, and each step with any timers.
If something looks wrong, it’s usually a line missing its amount at the start. Use **Preview** to see the
page before publishing.

---

## Editing or removing a recipe

- **Edit:** **Recipes → All Recipes**, click the recipe name, change anything, click **Update**.
- **Hide it for a while:** set *Status* to **Draft** (in the Publish box) and click **Update**.
- **Delete:** hover the recipe in the list and click **Trash**. (It stays in Trash for 30 days in case
  you change your mind.)
- **Remove the “New” badge:** untick it and click **Update**.

---

## Changing photos

All photos live in **Media** (left menu). You can upload straight from your phone or computer; WordPress
resizes them for you. Landscape photos about **1600 × 1100 pixels** work best for recipes and the
slideshow; square photos for collections and the About photo.

| Photo | Where to change it |
|---|---|
| A recipe’s photo | **Recipes → All Recipes** → open the recipe → click the photo on the right → choose a new one → **Update** |
| Collection tiles (*Baking*, *Fakeaways*…) | **Recipes → Collections** → hover the collection → **Edit** → **Change photo** → **Update** |
| Homepage slideshow (4 photos) | **Appearance → Customise → 🏠 Homepage → Slideshow** → **Change image** under each slide |
| Homepage “About Me” photo | **Appearance → Customise → 🏠 Homepage → Top of the page & About Me** → *About Me photo* |
| “Watch & Cook Along” video thumbnail | **Appearance → Customise → 🏠 Homepage → Watch & Cook Along video** → *Thumbnail photo* |
| About Me page photos | **Pages → About Me** → click a photo → **Replace** (top toolbar) → **Update** |
| Logo | **Your logo is already built in**: the wide version (name and tagline) at the top of every page, the
full version (with the spoon and fork) in the footer, and the spoon and fork as the little icon in the
browser tab.

**Appearance → Customise → ✨ Logo, site name & font**

- **Logo (top of the page):** to use a different one, click **Select logo** and upload it. A wide
  version with a transparent background works best.
- **Footer logo:** the larger logo at the bottom of every page. Click **Change image** to swap it.
- **Site Icon:** the little picture in the browser tab. The spoon and fork are used until you choose one.
- **Site title / Tagline:** used for Google and screen readers (*Family recipes you’ll actually make*).
- **The Seasons font:** upload the licensed **.woff2** web font file and it’s used for all headings.
  Until then, the similar *Playfair Display* is used. (The Seasons is a paid font, so the web licence is
  needed to use it on a website.)

**Appearance → Customise → 📱 Social media links**: Instagram, Facebook, TikTok and YouTube are
already filled in. Change or clear any of them, or add Pinterest. Icons only show for the ones filled in.
The YouTube channel link is also used for the **Visit my YouTube channel** button in the video section.

---

## Advertising (for later)

The site is ready for adverts whenever you are. **Nothing shows until you switch it on**, and you
won’t need any changes to the website itself.

**When you’re ready:**

1. Apply to an ad network. Popular choices for food bloggers are **Mediavine** (including its
   *Journey* programme for growing sites), **Raptive**, or **Google AdSense** (no minimum traffic).
   Each network has its own traffic requirements, so check their current criteria.
2. Go to **Appearance → Customise → 💷 Advertising (for later)**.
3. Paste the code they give you into **Code for the &lt;head&gt;**.
4. Paste their **ads.txt** lines into the **ads.txt** box. They appear at `yoursite.co.uk/ads.txt`
   (networks check this during approval, so you can add it before turning adverts on).
5. Tick **Turn on adverts** and click **Publish**.

**Ad spaces:** most networks (Mediavine, Raptive, AdSense *Auto ads*) place adverts automatically, so
you may only need step 3. If your network gives you code for fixed spots, there are six ready-made
ad spaces you can paste it into:

- Homepage – after *Something a little different*
- Homepage – after *Latest Recipes*
- Recipe page – after the recipe
- Recipe page – after *You might also like*
- Recipes page – below the recipe list
- Every page – above the newsletter sign-up

**See where they are:** while logged in, add `?ads=preview` to the end of any page address
(e.g. `yoursite.co.uk/?ads=preview`) to see dashed “Advert space” boxes.

**Built with adverts in mind:**

- Each recipe page includes the full recipe in the page itself, so ad networks and Google can read it.
- **Cook Mode never shows adverts**, so cooking along stays clean and easy.
- Turning adverts off again is one untick.

**Before going live with adverts:** UK law requires visitors to agree to advertising cookies. Most ad
networks provide a cookie consent banner. Otherwise use a consent plugin such as *Complianz*. Also
update your **Privacy Policy** page (**Settings → Privacy**).

---

## Privacy policy and cookie banner

The site follows UK rules (UK GDPR and the ICO’s cookie guidance):

- **Cookie banner:** first-time visitors see a small banner with equally easy **Accept all** and
  **Reject all** buttons, plus **Choose** to pick categories (visitor statistics, advertising,
  videos). Nothing optional loads until they agree, and their choice is remembered.
- **Cookie settings** link in the footer lets visitors change their mind any time.
- **YouTube videos** ask “Allow and play?” first if the visitor hasn’t allowed videos.
- Saved recipes and view settings stay on the visitor’s own device and are never sent anywhere.

**Changing the banner, adding Google Analytics:** **Appearance → Customise → 🍪 Privacy & cookies**.
Paste your Google Analytics code into *Visitor statistics code* and it only runs for visitors who
allow statistics. If your ad network shows its own consent banner, tick
*My ad network shows its own consent banner*.

**Privacy & Cookie Policy page:** created automatically when the theme is set up (it becomes your
official WordPress privacy page, linked in the footer). It’s a normal page in **Pages**, with your
site name and admin email filled in. **Please read it through** and keep it up to date, for example:

- name your newsletter provider (e.g. MailerLite) once you choose one
- name your ad partner when adverts start
- add anything new you start using (e.g. a contact form plugin or comments)

This policy is a sensible starting point written for this site, but it isn’t legal advice. If in
doubt, the ICO website (ico.org.uk) has free guidance for small businesses.

---

## If something goes wrong

- **Undo a change to a recipe or page:** open it and click **Revisions** on the right to go back to an
  earlier version.
- **Customiser:** nothing goes live until you click **Publish**. Close it to cancel.
- **A recipe looks odd:** check the *“How your recipe will look in Cook Mode”* box. Usually a line just
  needs its amount at the start, or a step needs to be on its own line.
- **A page says “not found” after installing:** go to **Settings → Permalinks** and click **Save Changes**.
- **Recipes and Cook Mode don’t show:** make sure the **Amy’s Recipes** plugin is active (**Plugins**).

---

## For developers

```
data.js, *.html          The static mock-up (GitHub Pages). Recipes live in data.js.
styles.css               Shared design (used by the mock-up and the WordPress theme)
site.js                  Header, menu, search, saved recipes, newsletter, category lists
recipe-card.js           Recipe card, servings scaler, Cook Mode, video players
home.js                  Homepage: categories, collections, mood picker, latest grid, carousel
recipes-page.js          Recipes page: filters, search, sort, list/grid
recipe-page.js           Single recipe page
about.js                 Mock-up About page (gallery lightbox, contact form)
consent.js               Cookie banner; holds back <template data-consent> code until allowed
privacy.html             Mock-up Privacy & Cookie Policy (built by wordpress/privacy-source.py)
wordpress/amy-recipes/        The plugin (recipe post type, collections, form, parser, settings, guide)
wordpress/amy-louise-crisp/   The theme (templates, Customiser, first-run setup)
wordpress/build.sh            Copies the shared files into the theme and builds wordpress/dist/*.zip
img/logo/                     Logo files (wordmark, full, icon, favicons) made from Amy's logo
```

The front-end scripts read `window.ALC_DATA`. In the mock-up that comes from `data.js`; in WordPress the
plugin builds the same structure from recipes, collections and settings (`alc_site_data()`), so both
share one set of scripts. After changing a shared file, run `bash wordpress/build.sh` and commit the
new zips.

Ad spaces: `alc_ad( 'slot' )` in the theme templates (`inc/ads.php`); the mock-up shows them with `?ads=preview`.

**Mock-up only:** to add a recipe to the static mock-up, copy the recipe block in `data.js`. Change the
festive season with `season` at the top of the same file.

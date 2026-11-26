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
| Logo | **Appearance → Customise → ✨ Logo, site name & font** → **Select logo** |

Tip: in the Customiser, nothing changes on the live site until you click **Publish**.

A recipe without a photo borrows its collection’s photo until you add one.

---

## Collections (recipe categories)

Go to **Recipes → Collections**. Each collection has:

- **Name**, e.g. *Fakeaways*
- **Description**: the short line shown under its name on the website
- **Collection photo**
- **Where it appears**:
  - **Main**: one of the four *Find Something Delicious* tiles on the homepage
  - **Extra**: the *Something a little different* cards (like *Fakeaways*, *One Pan Wonders*)
  - **Seasonal**: *Festive Favourites*, which uses the festive season settings
  - **Filter only**: just a filter on the Recipes page (like *30 Minute Meals*)
- **Position**: lower numbers appear first

To **add a new collection** (e.g. *Air Fryer*), fill in the form on the left of the Collections page and
click **Add New Collection**. It appears on the Recipes page, the footer and search straight away. To add it
to the top menu, see [Menus](#menus).

---

## Festive Favourites through the year

Go to **Recipes → Festive Season & Moods**.

1. Click a season button (🎄 Christmas, 💕 Valentine’s Day, 🐣 Easter, ☀️ Summer, 🎒 Back to School,
   🎃 Halloween, 🎆 Bonfire Night). The boxes fill in for you.
2. Change any of the wording you like.
3. Click **Save changes**.

The homepage card, the Festive Favourites page and the mood picker all update. Then tick the
**Festive Favourites** collection on the recipes you want to include, and untick old ones when the
season ends.

---

## “What are you in the mood for?” buttons

On the same page (**Recipes → Festive Season & Moods**), one button per line: the wording, an equals sign,
then the collection(s) it should suggest recipes from:

```
Something quick = 30 Minute Meals
Comfort food = Easy Family Dinners, Slow Cooker Stash
Fakeaway night = Fakeaways
```

When someone taps a button, the site suggests one of your recipes from those collections (a different one
each tap), or says “coming soon” if there aren’t any yet. The question above the buttons is changed in
**Customise → 🏠 Homepage → Top of the page & About Me**.

---

## Changing words on the homepage

Go to **Appearance → Customise → 🏠 Homepage**. You’ll see the site on the right and change it live.

- **Top of the page & About Me**: welcome headings and paragraphs, button wording, section headings, and
  the About Me box (photo, heading, text, sign-off).
  Put a word between `*stars*` in the big heading to give it the pink highlight:
  `Family recipes you’ll *actually* make`.
- **Slideshow**: four photos, each with a label title, a label line and a link. Leave slide 1’s wording
  blank to always show your newest recipe. You can also set the seconds per slide.
- **Watch & Cook Along video**: show or hide it, paste a new YouTube link, change the heading, text,
  quick facts (`20 | Minutes from start to finish`, one per line) and which recipe the
  **Get the Recipe** button opens.

Click **Publish** to make changes live (or close the Customiser to cancel).

The newest recipe and the Latest Recipes section update by themselves whenever you publish a recipe.

---

## Editing the About Me page

Go to **Pages → About Me**. It’s built with normal WordPress blocks:

- click any text to change it
- click a photo → **Replace** to swap it
- click the **+** to add a new block (paragraph, heading, image, gallery, FAQ “Details” block…)
- the six-dots handle moves a block up or down

Click **Update** when you’re done. The **Email me** button uses the site’s admin email. To use a contact
form instead, install a free form plugin such as *WPForms Lite* and add its block.

---

## Menus

Go to **Appearance → Menus**.

- **Main menu**: the menu at the top of every page (and in the mobile menu).
- **Footer menu**: the “More” links at the bottom.

To add a page, tick it on the left and click **Add to Menu**. To add a collection, use **Custom Links**
with the address of the Recipes page plus `#` and the collection’s slug, e.g.
`https://yoursite.co.uk/recipes/#fakeaways`. Drag items to reorder, then click **Save Menu**.

The **Recipes** column in the footer lists your collections automatically.

---

## Newsletter sign-up

The sign-up box appears at the bottom of every page. Change its words in
**Appearance → Customise → 💌 Newsletter sign-up**.

To collect real email addresses, create a free account with a newsletter service, make an
*embedded form*, and paste its code or shortcode into **Sign-up form code**. Recommended:

- **MailerLite**: easy to use, a free plan for smaller lists, and a WordPress plugin.
- **Kit (formerly ConvertKit)**: built for creators, popular with food bloggers, also with a free plan.

Until a form is pasted in, the box shows a sample form that doesn’t save emails.

---

## Logo, site name, The Seasons font and social links

**Appearance → Customise → ✨ Logo, site name & font**

- **Logo:** click **Select logo** and upload it (a PNG with a transparent background works best). Until a
  logo is added, the site name shows in the heading font.
- **Site title / Tagline:** the name and the line under it (*Family recipes you’ll actually make*).
- **The Seasons font:** upload the licensed **.woff2** web font file and it’s used for all headings.
  Until then, the similar *Playfair Display* is used. (The Seasons is a paid font, so the web licence is
  needed to use it on a website.)

**Appearance → Customise → 📱 Social media links**: paste each profile’s address. Icons only show for
the ones filled in. The YouTube channel link also adds a **Visit my YouTube channel** button to the
video section.

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
wordpress/amy-recipes/        The plugin (recipe post type, collections, form, parser, settings, guide)
wordpress/amy-louise-crisp/   The theme (templates, Customiser, first-run setup)
wordpress/build.sh            Copies the shared files into the theme and builds wordpress/dist/*.zip
```

The front-end scripts read `window.ALC_DATA`. In the mock-up that comes from `data.js`; in WordPress the
plugin builds the same structure from recipes, collections and settings (`alc_site_data()`), so both
share one set of scripts. After changing a shared file, run `bash wordpress/build.sh` and commit the
new zips.

**Mock-up only:** to add a recipe to the static mock-up, copy the recipe block in `data.js`. Change the
festive season with `season` at the top of the same file.

# Amy Louise Crisp – website mock-up

Live mock-up: https://combinedwork.github.io/amylouisecrisp/

## Pages
- `index.html` – homepage
- `recipes.html` – all recipes, with search, filters and saved recipes (`recipes.html#fakeaways` opens a category)
- `recipe.html?r=creamy-chicken-pesto-pasta` – one page per recipe, with Cook Mode
- `about.html` – About Me

## Adding a recipe (mock-up)
All content lives in **`data.js`**. Copy the recipe block, change the words and numbers, and the new recipe
appears on the homepage, the Recipes page, its own recipe page, search, the mood picker and Cook Mode.

- `ingredients`: amounts are for the `serves` number and scale automatically.
- `method`: one line per step. Cook Mode shows each step full screen with the ingredients it uses.
- `timers`: `{ 4: 1 }` gives step 4 a ready-set 1-minute timer in Cook Mode.
- `categories`: any of `dinners`, `slow`, `lunch`, `baking`, `festive`, `fakeaways`, `onepan`, `quick`.

## Changing the festive season
Edit `season` at the top of `data.js` (e.g. Easter, Summer, Halloween). The homepage card, the
Festive Favourites page and the mood picker all update.

## On WordPress
In WordPress the `data.js` file is replaced by the recipe editor (WP Recipe Maker), so Amy fills in a form
instead of editing a file. The same fields (servings, ingredient groups, steps, times, video, notes,
categories) feed the recipe card and Cook Mode automatically.

/* =========================================================================
   AMY LOUISE CRISP – SITE CONTENT
   -------------------------------------------------------------------------
   Every recipe, category and the current festive season live here.
   The homepage, Recipes page, recipe pages, search, the "What are you in
   the mood for?" picker and Cook Mode all read from this one place, so a
   new recipe added below appears everywhere automatically.

   In WordPress this file is replaced by the recipe editor (WP Recipe Maker):
   Amy fills in the same fields in a form and the site does the rest.
   ========================================================================= */
window.ALC_DATA = {

  /* ---- Festive Favourites: change this through the year ----------------
     e.g. Christmas, Easter, Summer, Halloween, Bonfire Night              */
  season: {
    name: 'Christmas',
    heading: 'Christmas is coming!',
    text: 'Festive bakes, party food and make‑ahead family dinners to take the stress out of the big day.',
    emoji: '🎄'
  },

  youtubeChannel: 'https://www.youtube.com/channel/UCjtX0o8ZOkc6L4hd2mzneVA',

  /* ---- Categories -------------------------------------------------------
     group "main" = the four "Find Something Delicious" tiles
     group "more" = the extra collections
     group "tag"  = handy filters that aren't full categories             */
  categories: [
    { slug: 'dinners',   name: 'Easy Family Dinners', group: 'main', image: 'img/pasta.jpg', pos: '40% 50%',
      blurb: 'Weeknight wins the whole family will love.' },
    { slug: 'slow',      name: 'Slow Cooker Stash',   group: 'main', image: 'img/sq-33-1.jpg',
      blurb: 'Pop it on in the morning and come home to dinner that’s ready and waiting.' },
    { slug: 'lunch',     name: 'Lunchbox Line‑Up',    group: 'main', image: 'img/hero.jpg', pos: '70% 50%',
      blurb: 'Packed lunch ideas the kids will actually eat.' },
    { slug: 'baking',    name: 'Baking',              group: 'main', image: 'img/sq-21.jpg',
      blurb: 'Bakes, treats and something for the tin.' },
    { slug: 'festive',   name: 'Festive Favourites',  group: 'more', seasonal: true, image: null,
      blurb: 'Seasonal recipes for every celebration of the year.' },
    { slug: 'fakeaways', name: 'Fakeaways',           group: 'more', image: 'img/sq-7-1.jpg',
      blurb: 'Homemade versions of your favourite takeaways and high‑street treats.' },
    { slug: 'onepan',    name: 'One Pan Wonders',     group: 'more', image: 'img/hero.jpg', pos: '25% 60%',
      blurb: 'Easy family dinners with minimal washing up.' },
    { slug: 'quick',     name: '30 Minute Meals',     group: 'tag',  image: 'img/pasta.jpg',
      blurb: 'On the table in half an hour or less.' }
  ],

  /* ---- "What are you in the mood for?" --------------------------------- */
  moods: [
    { label: 'Something quick',   match: ['quick'] },
    { label: 'Comfort food',      match: ['dinners', 'slow'] },
    { label: 'Fakeaway night',    match: ['fakeaways'] },
    { label: 'Less washing up',   match: ['onepan'] },
    { label: 'A sweet treat',     match: ['baking'] },
    { label: 'School lunches',    match: ['lunch'] },
    { label: 'Something festive', match: ['festive'] }
  ],

  /* ---- Recipes ----------------------------------------------------------
     Copy a recipe block to add a new one. Newest first.
     Ingredient amounts are for the "serves" number and scale automatically.
     "timers" gives a step (1 = first step) a ready-set Cook Mode timer, in minutes. */
  recipes: [
    {
      slug: 'creamy-chicken-pesto-pasta',
      title: 'Creamy Chicken Pesto Pasta',
      categories: ['dinners', 'quick'],
      isNew: true,
      image: 'img/pasta.jpg', imagePos: '35% 50%',
      summary: 'One of those easy family dinners you’ll find yourself making again and again! Packed with flavour, wonderfully creamy and perfect for busy weeknights.',
      prep: 5, cook: 15, serves: 4,
      ingredients: [
        { group: 'Pasta & Chicken', items: [
          [300, 'g', 'dried pasta'],
          [1, 'tbsp', 'olive oil'],
          [500, 'g', 'chicken breasts, diced'],
          [1, '', 'white onion, chopped'],
          [150, 'g', 'mushrooms, sliced']
        ]},
        { group: 'Creamy Pesto Sauce', items: [
          [50, 'g', 'butter'],
          [50, 'g', 'plain flour'],
          [500, 'ml', 'milk'],
          [0.5, 'jar', 'green pesto'],
          [50, 'g', 'mozzarella'],
          [30, 'g', 'Parmesan'],
          [null, '', 'Extra mozzarella and Parmesan, to top (optional)']
        ]}
      ],
      method: [
        'Bring a large saucepan of salted water to the boil. Cook the pasta according to the packet instructions, then drain, reserving a little pasta water.',
        'Heat the olive oil in a large frying pan. Add the diced chicken and cook until lightly golden.',
        'Add the chopped onion and sliced mushrooms to the pan. Continue cooking until the vegetables have softened and the chicken is cooked all the way through.',
        'Add the butter and allow it to melt, then stir in the flour. Cook for around 1 minute before gradually pouring in the milk, stirring continuously until you have a smooth, thickened sauce.',
        'Stir through the green pesto, mozzarella and Parmesan over a low heat until the cheese has melted and everything is combined.',
        'Add the drained pasta and stir everything together until well coated in the creamy pesto sauce. Add a splash of reserved pasta water if needed to loosen the sauce.',
        'Optional: top with extra mozzarella and Parmesan, then pop under the grill until golden and bubbling.'
      ],
      timers: { 4: 1 },
      tips: [
        ['Recipe tips', 'For a thinner sauce, add a splash of reserved pasta water or extra milk. You can also swap the mushrooms for spinach or leave them out entirely.'],
        ['Make ahead', 'Chop the chicken, onion and mushrooms in advance and keep them covered in the fridge. Keep raw chicken separate from the vegetables.'],
        ['Leftovers', 'Cool quickly, then refrigerate in an airtight container within 2 hours. Eat within 48 hours. Reheat until piping hot with a splash of milk. Only reheat once.'],
        ['Freezing', 'Best enjoyed fresh, as creamy sauces can change texture. If freezing, portion, freeze promptly and defrost fully in the fridge before reheating thoroughly.']
      ],
      video: { youtube: 'FvktW_5l0nY', title: 'Can I Make Creamy Chicken Pesto Pasta in 30 Minutes?' },
      keywords: 'chicken mushrooms pesto mozzarella parmesan pasta onion milk'
    }
  ]
};

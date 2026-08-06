# CrossWay Darjeeling Travel

Static website — plain HTML, CSS and JavaScript. No PHP, no database, no admin login.
All enquiries and bookings route to WhatsApp on **+91 7797970234**.

## Running it locally

Just double-click `index.html`. It works straight from disk.

For a closer match to how GitHub Pages serves it:

```bash
python3 -m http.server 8000    # then open http://localhost:8000
```

## Updating content

Content lives in three files under `data/`. Each holds a plain JSON array — edit the
values, save, reload the page. **Nothing else needs to change.**

| To change… | Edit |
|---|---|
| Tour packages, prices, itineraries | `data/packages.js` |
| Gallery photos | `data/gallery.js` |
| Homepage "Top Destinations" cards | `data/sightseeing.js` |

### Setting a price

Prices are currently `null`, which renders as **"Price on request"** on the site.
Replace `null` with a number to show a real price:

```js
"price": null,        →    "price": 4500,
"priceUnit": "car",        "priceUnit": "car",     // shows "₹4,500 / car"
```

### Adding a package

Copy an existing block in `data/packages.js` and change the values. The `id` must be
unique and URL-safe (lowercase, hyphens) — it becomes the link
`package-detail.html?id=your-id`. Set `"featured": true` to also show it on the homepage
(the homepage shows the first 3 featured packages).

### Adding a gallery photo

Put the image file in `images/`, then add an entry to `data/gallery.js` with its
`image_path`. The category dropdown on the gallery page builds itself from whatever
`category` values you use.

> There are 4 unused photos in `images/gallery/` (`1785671532_270514.jpg`,
> `1785671577_270511.jpg`, `1785671602_270505.jpg`, `1785671623_270508.jpg`) that aren't
> in the gallery yet, because it wasn't clear what they show. Add them with proper titles
> when you know.

### Changing the phone number

It appears in the page HTML and in `assets/js/site.js`. To change it everywhere:

```bash
grep -rl "7797970234" . --include="*.html" --include="*.js" | xargs sed -i '' 's/7797970234/NEWNUMBER/g'
```

## Layout

```
index.html  packages.html  package-detail.html  cars.html
gallery.html  about.html  contact.html  404.html
data/       ← content you edit
assets/css/style.css   ← all styling
assets/js/site.js      ← navbar, lightbox, contact form → WhatsApp
assets/js/render.js    ← builds the package/gallery/destination cards from data/
images/     ← photos
```

The navbar and footer are copied into each page. If you change one, change it in all
eight HTML files.

## Deploying to GitHub Pages

1. Push this folder to a GitHub repository.
2. Repo **Settings → Pages** → Source: *Deploy from a branch* → `main` / `/ (root)`.
3. It goes live at `https://<username>.github.io/<repo>/`.

### Adding a custom domain

1. Create a file named `CNAME` in this folder containing just your domain, e.g.
   `crosswaydarjeeling.com` — no `http://`, no trailing slash.
2. At your domain registrar, add four `A` records for the bare domain pointing to
   `185.199.108.153`, `185.199.109.153`, `185.199.110.153`, `185.199.111.153`,
   and a `CNAME` record for `www` pointing to `<username>.github.io`.
3. In **Settings → Pages**, enter the domain and tick **Enforce HTTPS** once the
   certificate is issued (can take a few minutes).
4. Replace `REPLACE-WITH-YOUR-DOMAIN` in `robots.txt` and `sitemap.xml` with the real domain.

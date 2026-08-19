# Red Headed Stepchild of Zen Cart&reg; Google Product Search Feeder II (v1.0.5), Reimagined Release v1.0.4

Red Headed Stepchild of Zen Cart® Google Product Search Feeder II generates Google Merchant Center product feeds from a Zen Cart catalog. This independent fork is based on Google Product Search Feeder II v1.0.5 and updates it for current Zen Cart releases. Reimagined Release v1.0.4 is compatible with Zen Cart 2.2.2 and PHP 8.5 while retaining support for Zen Cart 1.5.6b and later and PHP 7.0 and later.

## Features

- Google RSS/XML or tab-delimited TXT feeds
- Dynamic TXT columns, including attributes supplied by feed extensions
- Per-product or store-wide Google product categories
- Product shipping weight with a configurable packaging increase
- A configurable default weight for products whose catalog weight is empty or zero
- Optional database and admin product fields for `material`, `age_group`, `color`, and `gender`
- Server-side feed heartbeat with product counts, progress, memory use, and failure reporting
- Gzip compression, scheduled generation, language selection, and existing GPSF extension support

## Installation and upgrade

1. Back up the store files and database.
2. Rename the package's `YOUR_ADMIN` directory to match the store's admin directory.
3. Upload the package files while preserving their directory structure. Make sure both the catalog-side generator files and admin files are updated.
4. Sign out of Zen Cart admin, then sign back in. The feeder's non-destructive database upgrade runs during admin initialization.
5. Open **Configuration > Red Headed Stepchild of Zen Cart® Google Product Search Feeder II** and confirm that the installed Reimagined Release is `1.0.4`.

The database retains the original `Google Product Search Feeder II` configuration-group identity for upgrade and rollback compatibility. The longer Reimagined Release name is supplied by the admin language files.

When upgrading, do not copy only the admin files. Feed generation runs from catalog-side files such as `includes/classes/gpsfFeedGenerator.php`, so an incomplete upload can show the new version in admin while continuing to generate an older feed.

The optional product columns described below are not created automatically during an upgrade. Install only the fields the store needs by using their individual controls in the feeder configuration page.

## Admin locations

- **Configuration > Red Headed Stepchild of Zen Cart® Google Product Search Feeder II**: feeder settings and optional-field installation
- **Tools > Red Headed Stepchild of Zen Cart® Google Product Search Feeder II**: generate, inspect, and manage feeds
- **Catalog > Categories/Products**: edit installed per-product Google fields

## Feed formats

Set **Feed Output Format** to either:

- **XML**: the traditional Google RSS/XML feed
- **TXT**: a UTF-8, tab-delimited Google product feed with a header row

TXT generation discovers the attributes actually emitted by the products and writes only columns that contain at least one value. This also preserves attributes added by compatible feeder extensions. Repeated values are comma-delimited, and structured values use colon-delimited subfields in accordance with Google text-feed conventions.

The selected format controls the output filename and compressed filename. For example, a TXT feed is written as `feed/store_products_en.txt`, or `feed/store_products_en.txt.gz` when compression is enabled.

Because TXT headers are dynamic, a field such as `color` is intentionally absent when every generated product has an empty color value.

## Google product category

Category selection uses this precedence:

1. A Google product category already supplied for the product by the feeder's attribute or extension processing
2. The value in the configured products-table column, when enabled and non-empty
3. The store-wide default Google product category

To use a database value, enable **Use Product Category Column** and enter the products-table column name in **Product Category Column**. The standard column name is `products_google_product_category`, but another existing products-table column can be entered. If a product has no value in that column, the configured default is used.

## Shipping weight

The feeder chooses a base weight and then applies the packaging increase:

```text
base weight = positive products_weight, otherwise Default Shipping Weight
shipping_weight = base weight * (1 + Shipping Weight Increase Percentage / 100)
```

The percentage defaults to `3`, adding 3% to the selected base weight. The default weight is useful for stores whose products do not have catalog weights. If neither the product weight nor the configured default weight is positive, `shipping_weight` is omitted from that product.

The generated value uses the store's configured weight unit. These settings affect only the Merchant Center feed value; they do not change Zen Cart's checkout or shipping-rate calculations.

## Optional product fields

Reimagined Release v1.0.3 and later can add four independent fields to the products table and Zen Cart admin product editor. In **Configuration > Red Headed Stepchild of Zen Cart® Google Product Search Feeder II**, click the individual **Install** control for each field the store needs.

| Feed attribute | Products-table column | Database type | Admin input |
| --- | --- | --- | --- |
| `material` | `products_material` | `VARCHAR(255)` | Text |
| `age_group` | `products_age_group` | `VARCHAR(32)` | `newborn`, `infant`, `toddler`, `kids`, or `adult` |
| `color` | `products_color` | `VARCHAR(255)` | Text |
| `gender` | `products_gender` | `VARCHAR(16)` | `male`, `female`, or `unisex` |

Each Install action uses a fixed allowlist and an admin security token, and adds only its selected column. It does not remove or overwrite existing product data. Once installed, the field appears on the normal admin product entry/edit page.

Only non-empty values are exported. A value already provided by product attributes or a feeder extension takes precedence over the database field. Stores can therefore install and use only the fields relevant to their inventory.

## Generating a feed

Use **Tools > Red Headed Stepchild of Zen Cart® Google Product Search Feeder II** to generate a feed interactively. Scheduled generation can call the catalog feed endpoint with the configured key and other supported parameters. A typical command is:

```sh
wget -q -O /dev/null "https://example.com/index.php?main_page=google_product_feed&feed=yes&key=YOUR_KEY&language=en"
```

Replace the domain, key, and language with the store's values. Protect the feed key as a credential and use HTTPS.

### Feed heartbeat and large stores

Reimagined Release v1.0.4 writes a server-side heartbeat while a feed runs. The admin tool polls that heartbeat independently from the original generation request and displays the current stage, products scanned, products written, products skipped, total products, completion percentage, memory use, elapsed time, and age of the last heartbeat.

The heartbeat is updated at least every five seconds while products are being processed, while a TXT spool is written to the final file, and during gzip compression. If the browser request times out but PHP continues running, the status monitor continues polling. A run is marked unresponsive when no heartbeat is received for more than two minutes. Check the PHP logs before starting another feed when that warning appears.

Heartbeat data is stored in a hidden status file beside the configured feed output. Successful and failed runs retain their final status for inspection. Gzip compression is streamed in chunks so a large completed feed is not loaded into PHP memory all at once.

## Version history

### Reimagined Release v1.0.4, 2026-08-19

- Added server-side heartbeat status for browser and cron feed generation
- Added scanned, written, skipped, total, percentage, memory, elapsed-time, and current-stage reporting
- Added independent admin polling that continues after the original browser request ends
- Added failed and unresponsive run reporting
- Refreshed lock timestamps while a feed is active and changed file locking to non-blocking mode
- Streamed gzip compression in one-megabyte chunks to reduce memory use on large feeds

### Reimagined Release v1.0.3, 2026-08-19

- Added Zen Cart 2.2.2 and PHP 8.5 compatibility
- Added independent installation controls for `material`, `age_group`, `color`, and `gender`
- Added the corresponding products-table columns and Zen Cart admin product-editor inputs
- Added the installed database values to XML and TXT feeds when populated
- Preserved higher-priority values supplied by product attributes or feed extensions

### Reimagined Release v1.0.2

- Added **Default Shipping Weight** for products with no positive catalog weight
- Applied the configured percentage increase after selecting either the product weight or default weight
- Omitted `shipping_weight` when no positive base weight exists

### Reimagined Release v1.0.1

- Added `shipping_weight` based on product weight plus a configurable percentage increase, defaulting to 3%
- Added a configurable products-table column for per-product Google product categories
- Added fallback to the store-wide default category when the product column is empty

### Reimagined Release v1.0.0

- Added tab-delimited TXT feed output alongside XML
- Added dynamic TXT headers and support for extension-provided attributes
- Added text-feed serialization for repeated and structured values
- Updated filenames, gzip output, and feed locking for the selected format

### Upstream foundation: Google Product Search Feeder II v1.0.5

- The reimagined release began with the upstream v1.0.5 codebase
- Earlier upstream history remains available from the original project and support resources

## Documentation and support

- [GPSF Wiki](https://github.com/lat9/gpsf/wiki)
- [Google Merchant Center product data specification](https://support.google.com/merchants/answer/6324350?hl=en&ref_topic=6324338)
- [Zen Cart support thread](https://www.zen-cart.com/showthread.php?229785-Google-Product-Search-Feeder-II-Support-Thread)
- [Zen Cart plugin download](https://www.zen-cart.com/downloads.php?do=file&id=2379)

## Credits

- Original Google Merchant Center Feeder by Numinix
- Red Headed Stepchild of Zen Cart® Google Product Search Feeder II update by lat9 and contributors
- Reimagined Releases v1.0.0-v1.0.4 developed by [PRO-Webs, Inc.](https://PRO-Webs.net), Melanie Prough

Zen Cart&reg; is a registered trademark of Zen Ventures, LLC. Google and Google Merchant Center are trademarks of Google LLC.

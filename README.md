# Zen Cart&reg; Google Product Search Feeder II, v1.0.7
An update to the Numinix version, now supporting Zen Carts 1.5.6b and above.  Validated on PHP versions 7.0 through 8.4.

For additional questions and documentation, please see the [GPSF Wiki](https://github.com/lat9/gpsf/wiki).

Google API Documentation: https://support.google.com/merchants/answer/6324350?hl=en&ref_topic=6324338&sjid=18306132101193605684-NA

Zen Cart Support Thread: https://www.zen-cart.com/showthread.php?229785-Google-Product-Search-Feeder-II-Support-Thread

Zen Cart Plugin Download Link: https://www.zen-cart.com/downloads.php?do=file&id=2379

## Feed formats

The **Feed Output Format** setting supports the existing Google RSS/XML feed and a
tab-delimited TXT feed. TXT output uses Google attribute names in the header row,
keeps extension-provided attributes, and flattens repeated or structured values
using Google Merchant Center's comma- and colon-delimited text-feed conventions.

## Product category and shipping weight

The feed can read a per-product Google category from a configurable column in the
products table and fall back to the configured default category when that column
is empty. The generated `shipping_weight` uses the catalog product weight plus a
configurable packaging allowance, defaulting to 3%.

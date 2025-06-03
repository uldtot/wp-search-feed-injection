# WP Search Feed Injection

A lightweight WordPress plugin that injects external XML feeds as custom post types and makes them searchable within WordPress.

## ✨ Features

- Register external feed items as Custom Post Types (`wsfi_feed_item`)
- Automatically include feed items in WordPress search results
- External links open in a new tab directly from search results
- Admin interface to manually edit the external URL of each feed item
- External URLs are stored as custom post meta
- Clean integration with WordPress hooks and filters
- Easy to extend and customize

## 🔧 How It Works

1. The plugin registers a custom post type called `Feed Items`.
2. Each feed item can store an `external_url` that points to the original source.
3. When users perform a search, feed items are included in the results.
4. If a feed item has an external URL, the search result will link directly to it and open in a new tab.

## 📥 Importing Feed Items

This plugin is designed to work **together with [WP All Import](https://www.wpallimport.com/)**.

Rather than building yet another XML feed importer, this plugin reuses a robust and proven solution. Use WP All Import to map your feed data into the `wsfi_feed_item` post type and store the external link as post meta using the key `external_url`.

✅ **Recommended Approach**:  
- Post Type: `wsfi_feed_item`  
- Custom Field: `external_url` → your target link

## 🚀 Getting Started

1. Upload the plugin folder to `/wp-content/plugins/` or install it via the WordPress dashboard.
2. Activate the plugin.
3. Use WP All Import to import feed data into the `wsfi_feed_item` post type.
4. The items will now appear in search results and link out to the external source.

## 🛠️ Developer Notes

- External URLs are stored as post meta with the key `external_url`
- Feed items are registered with limited support (`title`, `editor`, `excerpt`, `author`)
- REST API support is disabled by default for feed items
- The plugin is designed to be easily extended for scheduled imports or custom feed parsing

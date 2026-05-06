=== Page Specific Favicons ===
Contributors: jasoncox
Tags: favicon, page icon, post icon, site icon, upload favicon
Requires at least: 6.0
Tested up to: 6.8.1
Requires PHP: 7.4
Stable tag: 1.0.6
License: Proprietary

Add a unique favicon to any post or page in WordPress. Falls back to your site-wide favicon if none is set.

== Description ==

Page Specific Favicons lets you assign a different favicon (site icon) to each post or page on your WordPress site.

Once installed, a "Custom Favicon" box appears in the editor sidebar for posts and pages. Upload or paste the URL of your favicon image, and it will automatically appear in the browser tab for that page.

If no custom favicon is set, your global Site Icon (from Appearance > Customize > Site Identity) will be used instead.

Includes a preview of the selected icon and an easy "Remove" button.

== Features ==

* Add a unique favicon per page or post
* Uses the WordPress media uploader
* Instant preview of the uploaded favicon
* Easily remove favicon with a single click
* Automatically falls back to site-wide favicon
* Supports standard favicon formats (PNG, ICO, JPG)

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install from the Plugins menu.
2. Activate the plugin.
3. Edit any post or page and scroll to the "Custom Favicon" box in the sidebar.
4. Upload or paste your favicon URL.
5. Save the post. Your custom favicon is now active.

== Usage ==

* The meta box named "Custom Favicon" appears for `post` and `page` types.
* Use the Upload button to choose an image from the Media Library. The URL is saved to post meta `_custom_favicon`.
* The plugin outputs standard favicon links and an `apple-touch-icon` link in the document head.

== Screenshots ==

1. Upload your custom favicon using the media library.
2. See a live preview of the selected icon.
3. Remove the favicon and restore default with one click.

== Security / Notes ==

* Version: 1.0.6
* The save handler validates a nonce, prevents autosave/revision writes, and checks the current user's `edit_post` capability before updating post meta.
* Saved URLs are sanitized with `esc_url_raw()` and URL schemes are limited to `http`, `https`, and `data`.
* Admin preview is created via safe DOM methods to avoid raw HTML injection.

== Changelog ==

= 1.0.6 =
* Initialize Plugin Update Checker after WordPress finishes loading plugins to avoid activation-time fatal errors.
* Guard the plugin class declaration so installing beside an older copy does not trigger a duplicate class fatal error.

= 1.0.5 =
* Add Plugin Update Checker for automatic updates from the GitHub `main` branch.
* Add optional GitHub token support through the `PLUGIN_UPDATE_GITHUB_TOKEN` constant or environment variable.
* Restrict update detection to branch checks to avoid GitHub Releases endpoint errors.

= 1.0.4 =
* Security hardening: capability checks, revision/autosave guards, URL scheme validation, safer admin preview DOM updates.

= 1.0.3 =
* Fallback to global site-wide favicon when none is set.

= 1.0.2 =
* Add remove favicon button and clear preview.

= 1.0.1 =
* Add favicon preview after upload.
* Add plugin URI and metadata.

= 1.0.0 =
* Initial release.

== Frequently Asked Questions ==

= What file types are supported? =
Most standard image formats including `.ico`, `.png`, and `.jpg`.

= What happens if I do not set a custom favicon? =
The plugin will automatically fall back to your global Site Icon.

= Can I use this with custom post types? =
Not yet, but support can be added with a small modification.

== Support ==

If you run into issues, please open an issue with steps to reproduce and WordPress/PHP versions.

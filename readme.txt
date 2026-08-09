=== Block Injector ===
Contributors: pootlepress, shramee, jamie
Tags: gutenberg, gutenberg blocks, blocks, block injector, banners, woocommerce
Requires at least: 6.0
Tested up to: 7.0
WC Tested up to: 10.9
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Inject blocks and content anywhere in your posts, pages and WooCommerce templates.

== Changelog ==

= 1.2.0 =
* Security: the endpoint that lists your posts, pages and products for the targeting dropdown now requires a nonce and the capability to edit pages. Previously any logged-in user, including subscribers and customers, could list the titles of every post, page and product on the site — including drafts and private content.
* Security: fixed the Enable/Disable switch on the Block Injector list, which was checking the wrong value with the wrong operator and so never actually verified its nonce. It now verifies the nonce and the user's capability before changing an injector's status.
* Enabling or disabling an injector now goes through WordPress rather than writing straight to the database, so caches update correctly on sites with persistent object caching.
* Fixed six PHP warnings written to the error log every time a Block Injector was saved.
* Fixed a JavaScript error that stopped the rest of the page's scripts when an injector's target selector matched nothing on the page.
* Removed a stray console.log left in the front-end output.
* Updated the Freemius SDK to 2.13.4.
* Tested up to WordPress 7.0 and WooCommerce 10.9. Now requires WordPress 6.0 and PHP 7.4.

= 1.1.3 =
* Updated the Freemius SDK.

= 1.1.2 =
* Updated the Freemius SDK.

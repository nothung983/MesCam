=== FiboSearch - Ajax Search for WooCommerce  ===
Contributors: damian-gora, matczar
Tags: woocommerce search, ajax search, search by sku, product search, woocommerce
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.29.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The most popular WooCommerce product search plugin. Gives your users a well-designed advanced AJAX search bar with live search suggestions.

== Description ==

The most popular **WooCommerce product search plugin**. It gives your users a well-designed advanced AJAX search bar with live search suggestions.

By default, WooCommerce provides a very simple search solution, without live product search or even SKU search. FiboSearch (formerly Ajax Search for WooCommerce) provides advanced search with live suggestions.

Who doesn’t love instant, as-you-type suggestions? In 2023, customers expect smart product search. Baymard Institute’s latest UX research reveals that search autocomplete, auto-suggest, or an instant search feature **is now offered on 96% of major e-commerce sites**. It's a must-have feature for every online business that can’t afford to lose customers. Why? FiboSearch helps users save time and makes shopping easier. As a result, Fibo really boosts sales.

= Features =
&#9989; **Search by product title, long and short description**
&#9989; **Search by SKU**
&#9989; Show **product image** in live search results
&#9989; Show **product price** in live search results
&#9989; Show **product description** in live search results
&#9989; Show **SKU** in live search results
&#9989; **Mobile first** – special mobile search mode for better UX
&#9989; **Details panels** with extended information – **“add to cart” button** with a **quantity field** and **extended product** data displayed on hovering over the live suggestion
&#9989; **Easy implementation** in your theme - embed the plugin using a **shortcode**, as a **menu item** or as a **widget**
&#9989; **Terms search** – search for product categories and tags
&#9989; **Search history** – the current search history is presented when the user clicked/taped on the search bar, but hasn't yet typed the query.
&#9989; **Limit** displayed suggestions – the number is customizable
&#9989; **The minimum number of characters** required to display suggestions – the number is customizable
&#9989; **Better ordering** – a smart algorithm ensures that the displayed results are as accurate as possible
&#9989; **Support for WooCommerce search results page** - after typing enter, users get the same results as in FiboSearch bar
&#9989; **Grouping instant search results by type** – displaying e.g. first matching categories, then matching products
&#9989; **Google Analytics** support
&#9989; Multilingual support including **WPML**, **Polylang** and **qTranslate-XT**
&#9989; **Personalization** of search bar and autocomplete suggestions - labels, colors, preloader, image and more

= Try the PRO version =
FiboSearch also comes in a Pro version, with a modern, inverted index-based search engine. FiboSearch Pro works up to **10× faster** than the Free version or other popular search solutions for WooCommerce.

[Upgrade to PRO and boost your sales!](https://fibosearch.com/pricing/?utm_source=readme&utm_medium=referral&utm_content=pricing&utm_campaign=asfw)

= PRO features =

&#9989; **Ultra-fast search engine** based on the inverted index – works very fast, even with 100,000+ products
&#9989; **Fuzzy search** – works even with minor typos
&#9989; **Search in custom fields** with dedicated support for ACF
&#9989; **Search in attributes**
&#9989; **Search in categories**. Supports category thumbnails.
&#9989; **Search in tags**
&#9989; **Search in brands** (We support WooCommerce Brands, Perfect Brands for WooCommerce, Brands for WooCommerce, YITH WooCommerce Brands). Supports brand thumbnails.
&#9989; **Search by variation product SKU** – also shows variable products in live search after typing in the exact matching SKU
&#9989; **Search for posts** – also shows matching posts in live search
&#9989; **Search for pages** – also shows matching posts in live search
&#9989; **Synonyms**
&#9989; **Conditional exclusion of products**
&#9989; **TranslatePress** compatible
&#9989; Professional and fast **help with embedding** or replacing the search bar in your theme
&#9989; and more...
&#9989; SEE ALL PRO [FEATURES](https://fibosearch.com/pro-vs-free/?utm_source=readme&utm_medium=referral&utm_content=features&utm_campaign=asfw)!

= Showcase =
See how it works for others: [Showcase](https://fibosearch.com/showcase/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=showcase&utm_gen=utmdc).

= Feedback =
Any suggestions or comments are welcome. Feel free to contact us via the [contact form](https://fibosearch.com/contact/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=contact&utm_gen=utmdc).

== Installation ==

1. Install the plugin from within the Dashboard or upload the directory `ajax-search-for-woocommerce` and all its contents to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to `WooCommerce → FiboSearch` and set your preferences.
4. Embed the search bar in your theme.

== Frequently Asked Questions ==

= How do I embed the search bar in my theme? =
There are many easy ways to display the FiboSearch bar in your theme:

– **Replacing the existing search bar with one click** - it is possible for dozens of popular themes
– **As a menu item** - in your WordPress admin panel, go to `Appearance → Menu` and add `FiboSearch bar` as a menu item
– **Using a shortcode**

`[fibosearch]`

– **As a widget** - in your WordPress admin panel, go to `Appearance → Widgets` and choose `FiboSearch`
– **As a block** - [learn how to use blocks](https://fibosearch.com/documentation/get-started/how-to-add-fibosearch-to-your-website/#add-fibosearch-with-the-dedicated-fibosearch-block) and FiboSearch together
– **Using PHP**

`<?php echo do_shortcode('[fibosearch]'); ?>`

– **We will do it for you!** - we offer free search bar implementation for Pro users. Become one now!

Or insert this function inside a PHP file (often, it is used to insert a form inside page template files):

= How do I replace the existing search bar in my theme with FiboSearch? =
We have prepared a one-click replacement of the search bar for the following themes:

*  Storefront
*  Divi
*  Flatsome
*  OceanWP
*  Astra
*  Avada
*  Sailent
*  and 46 more... See a complete list of integrated themes on [our documentation](https://fibosearch.com/documentation/themes-integrations/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=theme-integrations).


If you want to replace your search bar in another theme, please [contact our support team](https://fibosearch.com/contact/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=contact&utm_gen=utmdc).
We will assist with replacing the search bar in your theme for free after you upgrade to the Pro version.

= Can I add the search bar as a WordPress menu item? =
**Yes, you can!** Go to `Appearance → Menu`. You will see a new menu item called “FiboSearch”. Select it and click “Add to menu”. Done!

= How can I ask a question? =
You can submit a ticket on the plugin [website](https://fibosearch.com/contact/?utm_source=readme&utm_medium=referral&utm_campaign=asfw&utm_content=contact&utm_gen=utmdc) and the support team will get in touch with you shortly. We also answer questions on the [WordPress Support Forum](https://wordpress.org/support/plugin/ajax-search-for-woocommerce/).

= Do you offer customization support? =
Depending on the theme you use, sometimes the search bar requires minor improvements in appearance. We guarantee fast CSS corrections for all Pro plugin users, but we also help Free plugin users.

= Where can I find plugin settings? =
In your WordPress dashboard, go to `WooCommerce → FiboSearch`. The FiboSearch settings page is a submenu of the WooCommerce menu.

= Who is the Pro plugin version for? =
The Pro plugin version is for all online sellers looking to **increase sales** by providing an ultra-fast smart search engine to their clients.

The main difference between the Pro and Free versions is search speed and search scope. The Pro version has a new fast smart search engine. For some online stores that offer a lot of products for sale, search speed can be increased **up to 10×**, providing a whole new experience to end users.

All in all, the Pro version is dedicated to all WooCommerce shops where autocomplete suggestions work too slowly.

You can read more and compare Pro and Free features here: [Full comparison - Pro vs Free](https://fibosearch.com/pro-vs-free/).

== Screenshots ==

1. Search suggestions with a details panel
2. Search suggestions
3. Search suggestions with a details panel
4. Search suggestions (Pirx search style)
5. Search suggestions (Pirx compact search style)
6. Sample settings page (Starting tab)
7. Sample settings page (Search bar tab)
8. Sample settings page (Autocomplete tab)
9. Sample settings page (Search Analytics tab)

== Changelog ==

= 1.29.0, October 29, 2024 =
* ADDED: Integration with the **Listeo theme**
* ADDED: New troubleshooting test – warning when “**Ajax Search Lite**” or “**Ajax Search Pro**” plugins are active
* ADDED: **WCAG improvements** – possibility to select and open a search icon using the keyboard
* ADDED: Search icon preloader. In some rare cases, a user can wait longer to display the search icon or search bar. Instead of an empty place, a placeholder is displayed
* FIXED: **Generatepress theme** integration – incorrect mobile overlay on the checkout in the Generatepress theme
* FIXED: A better way of calculating window width when the breakpoint is checked
* TWEAK: Hiding the **XStore** theme documentation button on the settings page
* UPDATED: The `.pot` file.
* UPDATED: Freemius SDK

= 1.28.1, June 28, 2024 =
* FIXED: Removed the phrase “**polyfill.io**” from the JavaScript code comment. FiboSearch has never linked to this compromised library, but some security tools recognize this JavaScript comment as a potential link to malware. All reports are **false positive**.
* FIXED: PHP deprecated notice in `\DgoraWcas\Helpers::keyIsValid`
* FIXED: Unnecessary display of warning in **Troubleshooting** when products are displayed using a widget from “**JetSmartFilters**”

= 1.28.0, May 27, 2024 =
* ADDED: New search bar style - a compact version of a “Pirx” style
* ADDED: Integration with the “Cartzilla theme”
* ADDED: Integration with the “Rey theme”
* ADDED: Placeholders to display custom content for new suggestion types like taxonomy, posts, pages, and product variation
* FIXED: “Woodmart theme” - unable to close the mobile menu after exiting the mobile search overlay
* FIXED: “Flatsome theme” - disappearing search bar on mobile phones
* FIXED: “Flatsome” - cannot change search bar style to Pirx
* FIXED: “Divi theme” - shortcodes are not rendered in the description in the Details Panel for pages
* FIXED: “XStore theme” - the integration doesn't replace all search forms
* FIXED: Force disabling transition effect on search input to avoid unexpected layout bouncing
* FIXED: Allowing to calculate score including one and two-character words
* FIXED: Better recognition of iOS
* FIXED: Uncode theme - the search icon doesn't show on the header
* TWEAK: Removed OPcache invalidation for the shortcode template file
* UPDATED: The .pot file
* UPDATED: Freemius SDK to v2.7.2


= 1.27.0, January 31, 2024 =
* ADDED: Integration with the “Betheme theme”
* ADDED: Highlight words in search results with Greek letters regardless of accent
* ADDED: Support for “Full-width Search” in the “XStore theme”
* FIXED: Multiple search containers on mobile in the “Astra theme” integration
* FIXED: No focus on search input for mobile devices in the “Astra theme” integration
* FIXED: Allow an HTML `&lt;i&gt;` tag in suggestion titles and headlines
* FIXED: Multilingual support is active even for one language
* FIXED: Overriding the search icon and form in the header was not working properly in the “WoodMart integration”
* FIXED: Missing filters from “Advanced AJAX Product Filters” plugin in the “Divi theme”
* FIXED: Replace `&#37` for more stable format `%%` in a `sprintf` function
* FIXED: An unwanted modal after closing the search overlay on mobile in the “Flatsome theme”
* FIXED: Missing colors after updating the “Bloksy theme” to 2.x
* FIXED: Incorrect calculation of a product's position in search results when it contains Greek letters
* FIXED: Incorrect term language detection in the WPML plugin. Replacing `term_id` with `term_taxonomy_id`
* FIXED: Unwanted ampersand entity in the product description of search results
* UPDATED: Requires PHP: 7.4
* UPDATED: The `.pot` file
* UPDATED: Polish translation
* UPDATED: Freemius SDK v2.6.2

= 1.26.1, October 19, 2023 =
* FIXED: Details panel - wrong HTML format of stock status element 

= 1.26.0, October 17, 2023 =
* ADDED: Integration with “Bricks builder”
* ADDED: Integration with “Brizy builder”
* FIXED: Calc score by comparing every word of the search phrase instead of all search phrase
* FIXED: WooCommerce Wholesale Prices plugin - invalid search results e.g. not hidden products and categories in the search results
* FIXED: Flatsome - when there are more search icons, only one is replaced
* FIXED: WPRocket - in some cases search fields/icons are not replaced immediately after the page load
* FIXED: Highlight matched words instead of the whole search phrase
* TWEAK: Allowing access to the `Personalization` class via `DGWT_WCAS()` function
* TWEAK: HUSKY - Products Filter Professional for WooCommerce plugin - disable the test in the Troubleshooting module for newer versions of this plugin
* REFACTOR: Replace `.click()` with `trigger('click')`, `.focus()` with `trigger('focus')`, `.blur()` with `trigger('blur')`
* REFACTOR: Replace `jQuery.fn.mouseup()` with `$(document).on('mouseup')`
* REFACTOR: Replace `jQuery.isFunction()` with `typeof fn === 'function'`
* UPDATED: Freemius SDK v2.5.12

= 1.25.0, July 06, 2023 =
* ADDED: Possibility to search for taxonomy terms regardless of accents in a phrase or term name
* ADDED: Added some new filters to change URLs of results in autocomplete and details panel
* FIXED: Warnings due to `open_basedir` restrictions
* FIXED: Integration with the Impreza theme - broken AJAX pagination for Grid element
* FIXED: Integration with the TheGem theme - missing search results when the “Layout Type” option is set to “Products Grid”
* FIXED: Integration with the Divi theme - mobile overlay not showing up
* FIXED: Stronger sanitization of the details panel output
* UPDATED: Freemius SDK v2.5.10
* UPDATED: Polish translation

= 1.24.0, May 25, 2023 =
* ADDED: Integration with the “Minimog” theme
* ADDED: Posts, pages, and taxonomy terms are included in the FiboSearch Analytics module
* ADDED: Taking into account a new feature of the dark theme in the Nave theme
* ADDED: Possibility to change the color of a search bar underlay. Only for the Pirx style
* ADDED: New search widget and extended search results for Elementor
* ADDED: TheGem theme - “Header Builder” support
* FIXED: Wrong position of search icons in the history search module
* FIXED: Broken suggestions layout and detailed panel visibility when the “Minimum characters” option is set to less than 1
* FIXED: Compatibility with PHP 8.1
* FIXED: Hide unnecessary modules when constant `DGWT_WCAS_ANALYTICS_ONLY_CRITICAL` is set to true in the FiboSearch Analytics module
* FIXED: Incorrect display of information about constants on the debug page
* FIXED: Other minor bugs in the FiboSearch Analytics module
* FIXED: Integration with the Astra theme - support for version 4.1.0 of the Astra Addon
* FIXED: Integration with the Minimog theme - wrong position of the search history wrapper
* FIXED: Integration with the Enfold theme - the search engine icon disappears when the page finishes loading
* FIXED: A HTML tag `<br>` was unnecessarily stripped in the description in the details panel
* FIXED: The voice search feature - overlapping icons and disabling functionality on Safari
* UPDATED: French translation
* UPDATED: Freemius SDK v2.5.8
* TESTS: Two integration tests that check saving phrases in a database table
* TESTS: Fix assertion in “Analytics/Critical searches without result”
* REFACTOR: Change order if set settings defaults. Now the defaults are set after calling the `dgwt/wcas/settings` filter
* SECURITY: Added escaping for a “Search input placeholder” option

= 1.23.0, April 05, 2023 =
* ADDED: Integration with the “Blocksy” theme
* ADDED: Integration with the “Qwery” theme
* ADDED: Integration with the “StoreBiz” theme
* ADDED: Allows the `Shop manager` role to manage the plugin settings by adding a constant to the `wp.config.php` file
* ADDED: Allows creating HTML templates instead of displaying simple “No results” message
* IMPROVED: Blocks calculating score if the phrase contains a single character
* FIXED: More accurate calculation of the order of products in search results. The extra score for an exact match of a sequence of words
* FIXED: Storefront theme - not working focus event while using a mobile overlay for iPhone devices
* FIXED: Mobile overlay on iPhone devices - didn't hide search results on a scroll event or after clicking the “done” button
* FIXED: iPhone devices - annoying auto zoom in search input on focus
* FIXED: Search icon mode and search history - a search bar was needlessly concealed on clicking the “Clear” button
* FIXED: Freemius SDK - added submenu slug
* FIXED: Flatsome theme - detecting incompatible settings and disappearing search form on hover
* FIXED: Layout option - hidden triangle icon when a layout is “icon” and style is “Pirx”
* FIXED: Unnecessary AJAX query on the settings page
* TWEAK: Replacing empty href tag with `#` in Storefront integration because of SEO
* TWEAK: Trivial CSS changes
* UPDATED: Freemius SDK to v2.5.6
* REFACTOR: Forcing mobile overlay breakpoint and in layout breakpoint in theme integrations
* REFACTOR: Variables names in the method `Helpers::calcScore()`

= 1.22.3, January 30, 2023 =
* FIXED: Some prices were not aligned properly

= 1.22.0, January 30, 2023 =
* ADDED: New feature - Search history. The current search history is presented when the user clicked/taped on the search bar, but hasn't yet typed the query.
* ADDED: FiboSearch Analytics - New widget in WordPress Dashboard with critical searches without result
* ADDED: Integration with Essentials theme
* ADDED: Make UI_FIXER object as global object
* ADDED: Ability to search for vendors by description and city
* ADDED: Ability to exclude critical phrases in the Analytics module
* ADDED: Custom JavaScript events during the search process
* ADDED: Ability to export search analytics data as CSV files
* FIXED: Integration with Flatsome theme - focus event didn't work with a search bar
* FIXED: Integration with WooCommerce Product Filter by WooBeWoo - “Undefined array key 'query'” notice
* FIXED: Integration with Jet Smart Menu - repair duplicated search bars IDs
* FIXED: Integration with Astra theme - support for version 4.0.0
* FIXED: Integration with Astra theme - cannot change the number of products on the cart page
* FIXED: Integration with XStore theme - support for search icon in mobile panel
* FIXED: Compatibility with PHP 8.1
* FIXED: RWD for FiboSearch Settings views including Analytics views
* FIXED: Search bar CSS, especially when Pirx style and Voice Search work together
* FIXED: A user with permission to edit plugin settings cannot see search analytics
* CHANGE: Updated French translation
* CHANGE: Hide the Voice Search icon when a user starts typing
* CHANGE: Updated Freemius SDK to v2.5.3
* CHANGE: Remove information that Analytics is a beta feature
* CHANGE: Remove information that Darkened Background is a beta feature
* CHANGE: Set "Pirx" as a default search bar style



= 1.21.0, November 21, 2022 =
* ADDED: Integration with Product GTIN (EAN, UPC, ISBN) for WooCommerce plugin
* ADDED: Integration with EAN for WooCommerce plugin
* ADDED: Troubleshooting - checks if products thumbnails need to be regenerated
* FIXED: Missing translation domain in some texts
* FIXED: Support variants of &lt;br&gt; tag in product names in autocomplete
* FIXED: Unable to embed search bar as a widget
* FIXED: Disable voice search for Chrome on iPhone or iPad
* FIXED: Integration with the Astra theme - unclosed  &lt;div&gt; tag
* FIXED: Exclude save phrases to analyze when the phrase is 'fibotests' or the user has a specific role.
* FIXED: UI_FIXER: check if event listeners were correctly added to search inputs. If no, reinitiate the search instance
* FIXED: UI_FIXER: rebuild all search bars without correct JS events
* FIXED: Redundant DB queries related to the existence of plugin tables
* CHANGE: Updated Freemius SDK to v2.5.2



= 1.20.0, September 13, 2022 =
* ADDED: Integration with Woostify theme
* ADDED: Integration with Neve theme
* ADDED: Integration with WP Rocket
* ADDED: Include block sources in the plugin package
* ADDED: Possibility to reset search statistics from the settings page
* ADDED: Support for &lt;sub&gt; element in autocomplete suggestions
* FIXED: Incorrect display of styles with personalization of the search
* FIXED: Wrong settings index in Impreza and Enfold theme
* FIXED: Removed of unnecessary language files
* FIXED: Always set cursor at the end of the input
* FIXED: Incorrect verification if the browser supports speech recognition
* FIXED: FiboSearch Analytics - not working “check” buttons of the latest loading list
* FIXED: Unnecessary options and transients after uninstalling plugin

[See changelog for all versions](https://fibosearch.com/changelog/).

===Spreadshop===
Contributors: tassoman
Donate link: http://blog.tassoman.com/wordpress-plugins
Tags: admin, plugin, sidebar, post, page, posts, pages, spreadshirt, shop, gadgets, t-shirts, 3rd-parties
Requires at least: 2.2.2
Tested up to: 2.2.2
Stable tag: trunk

Put a free shop into your blog with Spreadshirt and start selling your personalized gadgets and t-shirts!

==Description==

Spreadshirt is a online gadget service, with it you can easily sell online your personal t-shirts and gadgets with a cool and fast Macromedia Flash creator interface. Items quality seem excelent but prices aren't so cheap.

This plugin is already quite stable. I have to praise all active users who posted bugs in the forum and helped with developement.

==Screenshots==

Any Screen provided till now.

==Translations==

If you're good with [PoEdit](http://www.poedit.org "Download PoEdit") you'll find the .pot template into the file archive distribution.

Please contact me and send me your translation. I'll be pleased to embed it on next releases.

If you're not yet good, learn [how to localize WordPress plugins](http://codex.wordpress.org/Writing_a_Plugin#Localizing_Plugins "how to localize WordPress plugins").

== Installation ==

*	First of all you should register an account at [Spreashirt.net](http://www.spreadshirt.net "Spreadshirt.net") if your business is european, or [Spreadshirt.com](http://www.spreadshirt.com "Spreadshirt.com") if you'll sell in U.S.A. Doing this, you'll avoid transatlantic shippings.
*	Customize your shop settings, and add some cool products to your shop (don't forget to write down your **SHOP ID**)
*	Download Spreadshirt plugin for WordPress
*	Extract the spreadshop directory into your FTP *wp-content/plugins* directory
*	Go to administration panel and visit the plugin page, then activate the Spreadshop plugin
*	Go to *Manage » Spreadshop* and fill the options as you want. You'll need the **SHOP ID** you wrote down before.
*	Once you'll submit the option page, the plugin will fetch your items data from Spreadshirt website. So remember to submit that form every time you'll add new products

== Frequently Asked Questions ==

= How can I get the shop embed into my blog? =

To get entire the shop embed into your blog, you must push it in a normal page, or a post. So create that page/post and give it a pretty title, then fill the content area only with the following string: <code><!--spreadshop--></code>, then save the page and your **Spreadshop** will just appear online like a sharme.

= How can I embed only a single product in a post? =

If you want only to embed a single product into a post or a page you should use the following code syntax into the content: <code><!--spreadarticle=3811625--></code>.

The number must be an existent **article id**. You can find that number in the URL of its details page.

The column separator is an optional and selects the size of thumbnail. It's used to force a specific image size between: **small,medium,big,huge**. <code><!--spreadarticle=3811625:huge--></code>

= How can I do to show a random product into my sidebar? =

If you want to show a random product from your shop, you must edit your theme. Edit _sidebar.php_ and put in this code: <code><?php randomSpreadArticle(); ?></code>

Also here you can manage the size of the thumbnail. Simply add into parenthesis the word between two single quotes.

==Changelog==
*	0.1 An idea was born.
*	0.2 Admin interface, xml parsing, and page integration.
*	0.3 Some debugging
*	1.0 Added support for shops registered in USA (spreadshirt.com)
*	1.1 DO'H I can't remember sorry... maybe debugging
*	1.2 Multi language gettext support.
*	1.3 Added currency support ($ for US and € for EU), and Italian translation
*	1.4 Added random article for sidebar
*	1.5 Added German translation
*	1.6 Added French translation
*	1.7 SpreadArticle functionality added
*	1.8 XML fetching bug solved
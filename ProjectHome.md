# Spreadshop Wordpress Plugin #

### This plugin it's tested on WordPress 2.3.1 ###

~~**Nowadays I'm writing the goPHP5 version 2.0 that will use a slim SOAP client instead of the huge XML parser.**~~

_Ok guys I'm sorry. This project is abandoned. If someone wants to join, is welcome._

Spreadshirt is a online gadget service, with it you can easily sell online your personal t-shirts and gadgets with a cool and fast Macromedia Flash creator interface. Items quality seem excelent but prices aren't so cheap.

This plugin is already quite stable. I have to praise all active users who posted bugs in the forum and helped with developement.

## Installation ##
  1. First of all you should register an account at [Spreadshirt.net](http://www.spreadshirt.net) if you're European, or [Spreadshirt.com](http://www.spreadshirt.com) if you're American. So you'll avoid transatlantic shipping
  1. Customize your shop settings, and add some cool products to your shop (don't forget to write down your **SHOP ID**)
  1. [/files/spreadshop.tar.gz Download] Spreadshirt plugin for WordPress
  1. Extract the spreadshop directory into your FTP _wp-content/plugins_ directory
  1. Go to administration panel and visit the plugin page, then activate the Spreadshop plugin
  1. Go to _Manage » Spreadshop_ and fill the options as you want. You'll need the **SHOP ID** you wrote down before.
  1. Once you'll submit the option page, the plugin will fetch your items data from Spreadshirt website. So remember to submit that form every time you'll add new products

## Usage ##
To get entire the shop embed into your blog, you must push it in a normal page.
Create that page, and give it a pretty title, then fill the content with this string only:

```
<!--spreadshop-->
```

Now save the page and your Spreadshop will just appear online like a sharme.

If you want to embed a single product into a post, or a page, you should use the following code syntax into your posts:

```
<!--spreadarticle=3811625-->
```

The number must be an existent article id, you can find that number in the URL of details page.

The column separator is optional and size string too, it's used to force a specific image size:

```
<!--spreadarticle=3811625:huge-->
```

If you want to show a random product from your shop, you must edit the theme. Probably you'll want it into the sidebar, so edit _sidebar.php_ and put in this code:

```
<?php randomSpreadArticle(); ?>
```

You can manage the size of the random thumbnail. Simply add into parenthesis one of this words between two single or double quotes: **small, medium, big, huge**.

Size reference stands in the administration page. You can find it in a drop down list, with pixel dimensions.

## Upgrading ##
  1. Go to administration and visit plugins page, then disable Spreadshop plugin
  1. Delete from FTP _wp-content/plugins/spreadshop_ directory
  1. Follow installation instructions, your options are not lost. You'll get them back once you re-activate the new version into plugin page.

## Changelog ##
  * 0.1 An idea was born.
  * 0.2 Admin interface, xml parsing, and page integration.
  * 0.3 Some debugging
  * 1.0 Added support for shops registered in USA (spreadshirt.com)
  * 1.1 DO'H I can't remember sorry... maybe debugging
  * 1.2 Multi language gettext support.
  * 1.3 Added currency support ($ for US and € for EU), and Italian translation
  * 1.4 Added random article for sidebar
  * 1.5 Added German translation
  * 1.6 Added French translation
  * 1.7 SpreadArticle functionality added
  * 1.8 XML fetching bug solved

## Translations ##
If you're good with [PoEdit](http://www.poedit.org) you'll find the .pot template into the file archive distribution.

Please contact me and send me your translation. I'll be pleased to embed it on next releases.

If you're not yet good, learn [how to localize WordPress plugins](http://codex.wordpress.org/Writing_a_Plugin#Localizing_Plugins).
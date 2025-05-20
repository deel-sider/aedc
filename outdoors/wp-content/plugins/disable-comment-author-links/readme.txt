=== Disable Comment Author Links ===

Contributors: salzano
Donate link: http://www.tacticaltechnique.com/donate/
Tags: comment author, get_comment_author_link, author link
Requires at least: 2.0.2
Tested up to: 5.8.3
Stable tag: 1.0.0

Prevents comment author names from linking to external websites.

== Description ==

This plugin removes all hyperlinks from comment author user names. Commenters can provide a home page (or any) URL along with their comment on a lot of themes. This plugin will strip all links from commenter names wherever they may appear throughout your site's theme.

== Installation ==

1. Upload the `disable-comment-author-links` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What does this plugin do? =

This plugin modifies the behavior of get_author_comment_link, a function in comment-template.php. It strips all HTML tags from the output, so commenter names never link to the website URLs they may provide when leaving comments.

= Where can I see the change? =

Anywhere comment author names are displayed on your website.

== Changelog ==

= 1.0.0 =

* [Changed] Adopts semantic version numbers
* [Changed] Changes tested up to version number to 5.8.3

= 0.110826 =

* First build
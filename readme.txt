=== WP Webhooks ===
Author URI: https://ironikus.com/
Plugin URI: https://ironikus.com/downloads/wp-webhooks/
Contributors: ironikus
Donate link: https://paypal.me/ironikus
Tags: edd, webhooks, automation, ironikus, webhook
Requires at least: 4.7
Tested up to: 5.1.1
Stable Tag: 1.0.4
License: GNU Version 3 or Any Later Version

Extend your website with the most powerful webhook system.

== Description ==

If you want to do certain actions on your WordPress site from somewhere else, this is your plugin! It will turn your website into an optimized webhook system so that you can connect your third party apps via Zapier, automate.io or other third-party services to your WordPress website.
It allows you to receive data from other services to, for example, create a user or a post on your WordPress website, as well as it can send data for you on certain actions.
It's time to automate your WordPress website on a whole new level!

= Usage examples =
* Create a WordPress user as soon as a new signup happens on Teachable
* Create a WordPress post using Alexa (Voice Control)
* Create WordPress users from an Excel list
* Send data to intercom when a user logs into your WordPress website

= Features =

* Create users and posts via external webhooks on your website (Custom post types supported)
* Send data on login, register, update, new post, update post and more
* Send data on custom WordPress hook calls
* Add multiple Webhooks for each trigger and also for the actions
* Test all of the available triggers with a single click
* Test all of the available actions within the plugin
* Advanced settings for each webhook trigger url
* Fully translatable and ready for multilingual sites
* Full WPML Support
* Advanced Developer Hooks
* Optimized settings page for more control
* Supports XML, JSON, plain text/HTML and form urlencode
* Supports Zapier, automate.io and more

= The best Pro version you have ever seen! =

Sounds like a catchy title, right? Actually, it really is the truth. Why? We will show you!

* A lot of awesome plugin features (Details down below)
* Live-test the plugin on your website through our chatbot assistant on ironikus.com
* In-plugin support
* Advanced security features
* Completely free premium extensions

Our premium features for [WP Webhooks Pro](https://ironikus.com/products/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro)

* Create users with user meta
* Update users and user meta
* Delete users
* Create posts with post meta
* Update posts with post meta
* Delete posts
* IP Whitelist feature for enhanced security
* In-plugin support

Our free premium extensions for [WP Webhooks Pro](https://ironikus.com/products/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro)

* Create Blog Post Via Email: Yes, it will allow you to create WordPress posts via Email
* Execute PHP Code: It is as massive as it sounds. It allows you to run php scripts through webhooks on your WordPress site
* Remote File Control: Manage your local files on the server via webhooks. You can also create new local files from a given URL
* Manage Media Files: Create WordPress attachments from local or remote files, delete them and much more
* Code Trigger: This is a next-level extension. You can run code through a webhook everytime WordPress get's called.

= Questions? =

In case you have questions, feel free to reach out to us at any time. We also offer conculting in case you want to archive a bigger project with our plugin.

= For devs =

We offer you a very awesome hook system to customize everything based on your needs! Feel free to message us in case you want special features - We love to help!

== Installation ==

1. Activate the plugin
2. Go to Settings > WP Webhooks and start automating


== Changelog ==

= 1.0.4: April 24, 2019 =
* Feature: Introduce new webhook trigger settings - You can now set custom rules for each of your webhook triggers
* Feature: Confirm action before deleting a trigger webhook
* Feature: Reset WP Webhook data via the settings
* Feature: Added a new webhook trigger that fires after a custom WordPress action hook was called. ( Send Data On Custom Action )
* Feature: Introduce new default settings for the following webhooks: Send Data On New Post, Send Data On Post Update, Send Data On Post Deletion
* Feature: Introduce new settings to fire a trigger only on certain post types for the following webhooks: Send Data On New Post, Send Data On Post Update, Send Data On Post Deletion
* Tweak: Add post data and post meta data to the post_delete trigger
* Tweak: Optimize process for generated webhook trigger id's
* Tweak: Change post_delete trigger from after_delete_post to delete_post
* Tweak: Optimize response for custom action after certain webhooks
* Tweak: Optimize phpDocs
* Tweak: Optimize Send Data tab
* Tweak: Improve the displayed values for single webhook trigger responses
* Fix: Fix issue of not visible whitelist and log tabs after saving the settings
* Dev: Introduce optimized handler for posting data to a webhook. You can now also parse the whole webhook array construct
* Dev: Add new webhook default settings api
* Dev: Add new webhook settings api
* Dev: Introduce new update function for updating webhook data

= 1.0.3: April 13, 2019 =
* Feature: Optimized headers for "Send Data" triggers
* Feature: Add Signature for "Send Data" triggers through new settings option

= 1.0.2: March 23, 2019 =
* Feature: Synced code with the pro version for a better data handling

= 1.0.1: March 20, 2019 =
* Feature: Test webhook actions directly out of the plugin

= 1.0.0: March 13, 2019 =
* Birthday of WP Webhooks

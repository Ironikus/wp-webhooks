=== WP Webhooks ===
Author URI: https://ironikus.com/
Plugin URI: https://ironikus.com/downloads/wp-webhooks/
Contributors: ironikus
Donate link: https://paypal.me/ironikus
Tags: webhooks, automation, ironikus, webhook, api, web hooks, hooks, automating, automate, connect, third-party
Requires at least: 4.7
Tested up to: 5.3.1
Stable Tag: 1.1.5
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

* Create, Delete, Search and Retrieve users via external webhooks on your website
* Create, Delete, Search and Retrieve posts via external webhooks on your website (Custom post types supported)
* Send data on login, register, update and deletion
* Send data on new post, update post and delete post
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

= Free Extensions =
On wordpress.org you will also find more free extensions to equip your favorite plugins via webhooks.

* [Contact Form 7 Integration](https://wordpress.org/plugins/wpwh-contact-form-7/)
* [Manage Taxonomy Terms](https://wordpress.org/plugins/wp-webhooks-manage-taxonomy-terms/)
* [WP Reset Integration](https://wordpress.org/plugins/wpwh-wp-reset-webhook-integration/)

= The best Pro version you have ever seen! =

Sounds like a catchy title, right? Actually, it really is the truth. Why? We will show you!

* A lot of awesome plugin features (Details down below)
* Live-test the plugin on your website through our chatbot assistant on ironikus.com
* In-plugin support
* Advanced security features
* Completely free premium extensions

[Compare WP Webhooks and WP Webhooks Pro now!](https://ironikus.com/compare-wp-webhooks-pro/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro%20Compare)

Our premium features for [WP Webhooks Pro](https://ironikus.com/downloads/wp-webhooks-pro/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro)

* Create users with user meta
* Update users and user meta
* Delete users
* Add and/or remove multiple user roles
* Create posts with post meta
* Update posts with post meta
* Delete posts
* IP Whitelist feature for enhanced security
* In-plugin support

Our free premium extensions for [WP Webhooks Pro](https://ironikus.com/downloads/wp-webhooks-pro/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro)

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

= 1.1.5: December 17, 2019 =
* Feature: Display new table field for only the API key
* Feature: Added new webhook trigger that fires after a user was deleted
* Tweak: Better support for our new Zapier App 2.0.0

= 1.1.4: November 27, 2019 =
* Feature: Send post taxonomies along with post creade and update trigger
* Tweak: Clear input fields after adding new trigger

= 1.1.3: November 15, 2019 =
* Feature: Activate/Deactivate single webbhook triggers
* Feature: Post-delay webhook triggers. (Triggers are fired before PHP shuts down to catch plugin changes)
* Feature: Post-delay setting to deactivate the functionality
* Tweak: Change certain triggers to fire with the whole webhook object

= 1.1.2: November 06, 2019 =
* Feature: Add webhook name field (slug) to the webhook trigger URL's
* Feature: Add webhook name to the webhook trigger headers
* Tweak: Add additional parameters to the authorization hook
* Tweak: Optimize webhook description for "get_user" action
* Fix: Get user response gave success back if no user was found
* Dev: Adjust WordPress hook priority for incoming data from 10 to 100 

= 1.1.1: October 21, 2019 =
* Feature: Introduce exclusive Zapier extension (Early access)
* Feature: Introduce new polling feature for next-level Zapier triggers
* EARLY ACCESS FOR OUR ZAPIER APP: https://ironikus.com/docs/knowledge-base/integrate-zapier-extension-with-wp-webhooks/

= 1.1.0: October 12, 2019 =
* Feature: Deactivate and Activate webhook action URL's
* Feature: New webhook actions to search/retrieve post(s) within a third party services
* Feature: New webhook actions to search/retrieve user(s) within a third party services
* Teak: Optimized and simplified backend layout
* Tweak: Add webhook name for action and triggers to the webhook settings as data itself (This allows better targeting of webhook manipulations)
* Tweak: Include fallback logic for non-working JSON contructs that include unicode characters
* Tweak: Optimize packend docs and WordPress code standards
* Fix: Remove unncessary var_dump()-calls within our backend tabs

= 1.0.9: August 31, 2019 =
* Feature: Support Woocommerce post status on default post status features like sending a trigger on post creation with a certain status
* Tweak: Made action_delete_user function public
* Fix: Fixed bug with non-working do_action parameter on create/update user action
* Fix: Issue with non working "Send data on user login" due to wrong interpreted user parameter
* Dev: New filter for webhook trigger data: wpwhpro/admin/webhooks/webhook_data

= 1.0.8: August 10, 2019 =
* Feature: Trigger create_post webhook if the initial status of the post changes
* Tweak: Optimize test webhook description
* Fix: Correct "Trigger from frontend only" description within the webhook settings
* Fix: Non-working action testing forms in case https was active (Only in special cases)
* Dev: New helper function for safe-redirecting the home url
* Dev: Optimize WordPress coding standards

= 1.0.7: July 26, 2019 =
* Feature: Add webhook action to delete users (Also from multisite networks)
* Feature: Add webhook action to delete posts
* Fix: Undefined notice while sending triggers

= 1.0.6: June 30, 2019 =
* Feature: Webhook actions are ajax ready
* Feature: Security question before deleting an action or trigger
* Feature: Settings Engine for webhook actions
* Fix: When using update_user action in combination with create_if_none, the user was not aadded
* Fix: Fix text bugs
* Fix: Debug warning if json data is parsed as an array and not as a string
* Fix: Fix issue with not correctly applied text domain for translation functions
* Fix: Non existent translation within the Send Data Tab for the "Add button"
* Dev: New filter wpwhpro/helpers/request_return_value
* Dev: New filter wpwhpro/settings/required_action_settings

= 1.0.5: May 25, 2019 =
* Feature: Send your triggers in different content types. Supported types: JSON (Default), XML, X-WWW-FORM-URLENCODE
* Fix: Correct menu item name from "Recieve Data" to "Receive Data"
* Fix: Remove sanitation from parsed user password to not change it at all (create_user and update_user trigger)
* Dev: New filter to strip slashes on responses: wpwhpro/helpers/request_values_stripslashes
* Dev: New filter for the new convert_to_xml function to change the prefix: wpwhpro/helpers/convert_to_xml_int_prefix
* Dev: Filter for manipulating the required webhook trigger settings: wpwhpro/settings/required_trigger_settings
* Dev: Filter to change the simplexml data: wpwhpro/admin/webhooks/simplexml_data

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

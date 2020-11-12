=== WP Webhooks ===
Author URI: https://ironikus.com/
Plugin URI: https://ironikus.com/downloads/wp-webhooks/
Contributors: ironikus
Donate link: https://paypal.me/ironikus
Tags: webhooks, automation, ironikus, webhook, api, web hooks, hooks, automating, automate, connect, third-party
Requires at least: 4.7
Tested up to: 5.5.3
Stable Tag: 2.1.2
License: GNU Version 3 or Any Later Version

Extend your website with the most powerful webhook system.

== Description ==

If you want to do certain actions on your WordPress site from somewhere else, this is your plugin! It will turn your website into an optimized webhook system so that you can connect your third party apps manually, via Zapier, automate.io or other third-party services to your WordPress website.
It allows you to receive data from other services to, for example, create a user or a post on your WordPress website, as well as it can send data for you on certain actions.
It's time to automate your WordPress website on a whole new level!

= Usage examples =
* Create a WordPress user as soon as a new signup happens on Teachable
* Create a WordPress post using Alexa (Voice Control)
* Create WordPress users from an Excel list
* Send data to intercom when a user logs into your WordPress website
* Fire your own PHP code based on incoming data

= Features =

* Create, Delete, Search and Retrieve users via external webhooks on your website
* Create, Delete, Search and Retrieve posts via external webhooks on your website (Custom post types supported)
* Receive data to a custom webhook action (Do whatever you want with the incoming data)
* Send data on login, register, update and deletion
* Send data on new post, update post and delete post
* Send data on custom WordPress hook calls
* Authenticate every "Send data" trigger. Supported are: API Key, Bearer Token and Basic Auth
* Add multiple Webhooks for each trigger and also for the actions
* Test all of the available triggers with a single click
* Test all of the available actions within the plugin
* Advanced settings for each webhook trigger url
* Manage all of our extensions within the plugin
* Fully translatable and ready for multilingual sites
* Full WPML Support
* Advanced Developer Hooks
* Optimized settings page for more control
* Supports XML, JSON, plain text/HTML and form urlencode
* Supports the following request methods: POST (Default), GET, HEAD, PUT, DELETE, TRACE, OPTIONS, PATCH
* Supports Zapier, Integromat, automate.io and more

= Free Extensions =
On wordpress.org you will also find more free extensions to equip your favorite plugins via webhooks.

* [WP Webhooks – Easy Digital Downloads](https://wordpress.org/plugins/wp-webhooks-easy-digital-downloads/)
* [Contact Form 7 Integration](https://wordpress.org/plugins/wpwh-contact-form-7/)
* [Manage Taxonomy Terms](https://wordpress.org/plugins/wp-webhooks-manage-taxonomy-terms/)
* [WP Reset Integration](https://wordpress.org/plugins/wpwh-wp-reset-webhook-integration/)
* [WP Webhook Comments](https://wordpress.org/plugins/wp-webhooks-comments/)
* [WP Webhooks – Email integration](https://wordpress.org/plugins/wp-webhooks-email-integration/)

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
* Data Mapping engine to revalidate your incoming/outgoing data
* Whitelabel feature (see comparison table)
* Log feature for easier debugging
* IP Whitelist feature for enhanced security
* Access token feature for enhanced security
* Webhook URL action whitelist
* In-plugin assistant

Our free premium extensions for [WP Webhooks Pro](https://ironikus.com/downloads/wp-webhooks-pro/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro)

* Woocommerce integration: This extension allows you to do certain Woocommerce action on your website
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

= 2.1.2: November 13, 2020 =
* Fix: Receive Data tab was not showing due to a non available file

= 2.1.1: November 12, 2020 =
* Fix: Missing description for deleted_user webhook trigger $user object

= 2.1.0: November 06, 2020 =
* Feature: Add post meta data to get_posts webbhook action as a separate argument (load_meta)
* Tweak: As of the WP 5.5 release, the $user object is sent over with the deleted_user hook: https://developer.wordpress.org/reference/hooks/deleted_user/ - We made the new variable compatible
* Tweak: Correct spelling mistake with "Receive" as "Recieve"
* Tweak: Optimize plugin naming
* Tweak: correct webhook response grammar mistakes
* Tweak: Allow serialized args to be an empty JSON of the get_posts webhook action
* Fix: issue with wrongly assigned nickname variable check
* Fix: Fix issue with undefined variable
* Fix: PHP warning: SimpleXMLElement::addChild() expects parameter 2 to be string, object given - It occured within the convert_to_xml() function and is now fixed
* Dev: New helper class to check if a given plugin is active ( is_plugin_active() )

= 2.0.7: September 22, 2020 =
* Tweak: Add import_id to the create_post webhook action
* Tweak: Added permalink to the following triggers: post_create, post_update, post_delete and post_trash
* Tweak: Optimize the functionality on the create_post action setting for firing on the initial post status change
* Fix: The create_post webhook action in combination with firing on the post status change, caused the post not to be triggered on a post update
* Dev: Introduced new handler function echo_action_data() to centralize the output of a webhook action
* Dev: Extend the wpwhpro/webhooks/response_response_type filter by a new argument: $args (https://ironikus.com/docs/knowledge-base/filter-response-type/)
* Dev: The echo_response_data() function now returns the validated data as well

= 2.0.6: August 12, 2020 =
* Tweak: Ready for WordPress 5.5
* Tweak optimize text
* Tweak: Optimized layout
* Tweak: Correct comments
* Fix: Issue with wrong naming for the "Send Data" setup on the demo user payload for the user_meta key
* Dev: get_response_body() helper now supports manual data entry as a payload and content type
* Dev: Add plugin URL to admin ajax

= 2.0.5: July 02, 2020 =
* Feature: Add user meta to the get_user webhook action
* Feature: The custom_action trigger got a rework and now uses apply_filters() instead of do_action - this allows you to also catch the response (the logic is backwards compatible, so you can still continue to use your existing logic)
* Feature: Allow modification of the http arguments within the custom_action webhook action (separate variable for the apply_filters() call)
* Feature: Allow the percentage character within webhook trigger URLs
* Feature: Add user meta data to the get_users webhook action response
* Feature: New trigger setting to allow unsafe looking URLs (By default, URL's like asiufgasvflhsf.siugsf.com are prevented from being sent for your security)
* Feature: New trigger setting to allow unverified SSL connections (In case you have a self-signed URL, you can prevent the default SSL check for each webhook)
* Tweak: Optimize PHPDocs
* Fix: The same webhook names for different triggers broke the settings popup
* Fix: the delete_post webhook action contained a wrongly formatted error message
* Fix: Prevalidate json within the is_json() helper function to prevent notices within the debug.log file
* Dev: Added the trigger group slug to the wpwhpro/admin/settings/webhook/page_capability filter (currently the trigger was only sent by its name which is not unique without the trigger group)
* Dev: Added new handler function for generating the API keys
* Dev: New filter to manipulate the API key: wpwhpro/admin/webhooks/generate_api_key (https://ironikus.com/docs/knowledge-base/filter-generated-api-key-for-action-urls/)


= 2.0.4: May 11, 2020 =
* Feature: New webhook trigger that sends data on trashing a post (custom post types supported)
* Feature: The tax_input argument for the create_post action now supports JSON formatted strings
* Feature: Added taxnomies as well to post_delete trigger
* Feature: Added full post thumbnail URL to the post_create and post_delete trigger
* Feature: Extend demo data for post_create and post_delete trigger
* Tweak: Added the already existing parameters to the parameter description of the post_delete trigger
* Tweak: Optimized all webhook descriptions and texts
* Tweak: Optimize layout for the webhook action argument list
* Fix: Taxonomies haven't been sent over on post_create and post_update trigger

= 2.0.3: March 17, 2020 =
* Feature: EXTENSION MANAGEMENT - You can now manage all extensions for WP Webhooks and WP Webhooks Pro within our plugin via a new tab (Install, Activate, Deactivate, Upgrade, Delete)
* Feature: the arguments post_date and post_date_gmt on the create_post webhook actions accept now any kind of date format (we automatically convert it to Y-m-d H:i:s)
* Feature: Introducton to a new settings item called "Activate Debug Mode" - It will provide further debug.log information about malformed data structures and more
* Tweak: Remove post_modified and post_modified_gmt parameter from the create_post webhook action since they are non-functional (https://core.trac.wordpress.org/ticket/49767)
* Tweak: Reposition fetching of the action parameter for incoming webhook requests
* Tweak: Optimized layout for the plugin admin area
* Tweak: Optimize webhook action response text in case there wasn't any action defined
* Dev: Add new helper function to check if a plugin is installed

= 2.0.2: March 29, 2020 =
* Feature: Fully reworked webhook descriptions (You WILL love them!)
* Feature: Add user data and user meta as well to the deleted_user trigger
* Tweak: Optimized tab descriptions
* Tweak: Optimized stylings
* Tweak: Add post details + meta as well for attachment deletion triggers
* Fix: Post details + meta haven't been available on the post_delete trigger
* Dev: Add the $user variable to the do_action argument for the get_user webhook action
* Dev: Add the $return_args variable to the do_action argument for the create_post webhook action

= 2.0.1: March 08, 2020 =
* Feature: New webhook trigger setting to change the request method. Supported: POST (Default), GET, HEAD, PUT, DELETE, TRACE, OPTIONS, PATCH
* Tweak: Optimize certain layout parts 
* Tweak: Display webhook name and technical name within the Settings popup
* Fix: On reset of WP Webhooks Pro, the authentication data was not removed
* Dev: Deprecated trigger secret (Can be set only with WordPress hooks) - Why? Due to confusion and too specific usecases

= 2.0.0: February 17, 2020 =
* Feature: THIS VERSION IS FULLY BACKWARDS COMPATIBLE
* Feature: Completely refactored any optimized layout
* Feature: GET parameters are now accepted as well as action arguments (Only in real GET calls)
* Feature: New authentication engine: You can now authenticate every webhook trigger for external APIS using API Key, Bearer Token or Basic Auth
* Feature: New webhook action called "custom_action", which allows you to handle every incoming data within a WordPress add_action() hook
* Feature: Change the webhook URL you want to use for testing actions within the "Receive Data" page
* Feature: Add pre-post data to the "Send Data on Post Update" trigger
* Feature: Add additional roles while creating a user
* Feature: You can now set the post_author for create_post actions as well using the email address instead of the ID (If the email address is known within your WP installation)
* Tweak: Added the action argument as well the the argument list within the "Receive Data" tab
* Tweak: Added the action argument as well to the testing form for webhook actions within the "Receive Data" tab
* Tweak: Completely refactored settings saving process for a smooth UI experience
* Tweak: PHP Docs have been optimized
* Tweak: Placeholder logic was not integrated with dynamic settings fields for "Send Data" settings
* Tweak: The webhook triggers within the "Send Data" tab show now as well the internal webhook name (in brackets)
* Tweak: We changed all checkboxes through neat toggles for a better usability
* Tweak: Rearrange setting items
* Fix: On "Send Data on Post Update", attachments haven't been triggered
* Fix: API key field was missing after adding a new action URL
* Fix: Corrected certain typos
* Dev: Added new filter to manipulate post-delayed triggers: wpwhpro/post_delay/post_delay_triggers (Prevent webhook triggers from firing or add your own ones)
* Dev: Add multiple arguments to the post_to_webhook()-functions WordPress actions
* Dev: wpwhpro/admin/webhooks/webhook_http_args has now two more arguments: $webhook, $authentication_data
* Dev: wpwhpro/admin/webhooks/webhook_trigger_sent has now more arguments

= 1.1.8: January 27, 2020 =
* Fix: Throw 403 http error accordingly on authentications
* Tweak: Optimize error messages for authentication

= 1.1.7: January 27, 2020 =
* Feature: The webhook authentication process is now also fully JSON ready and returns a JSON as a response
* Tweak: A failed authentication now also returns a 200 error code instead of 403 
* Tweak: Settings layout is now better readable

= 1.1.6: January 17, 2020 =
* Feature: Allow the custom webhook trigger to send data only to certain webhooks using the secondary $webhook_names variable: do_action( 'wp_webhooks_send_to_webhook', $custom_data, $webhook_names );
* Tweak: Optimize webhook descriptions for certain triggers and actions
* Fix: Correct password creation logic for creating a user
* Fix: Triggers didn't fire on creating an attachment
* Fix: The custom action trigger contained a custom action that was fired as well on post deletion

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
* Fix: Correct menu item name from "Receive Data" to "Receive Data"
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

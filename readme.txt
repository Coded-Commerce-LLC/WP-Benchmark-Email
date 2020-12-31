=== Benchmark Email Lite ===
Contributors: seanconklin, randywsandberg
Donate link: https://codedcommerce.com/donate
Tags: api, benchmark, benchmark email, campaign, campaigns, chinese, contact form, contact forms, contacts, email, email blast, emails, email marketing, form, forms, html, join, list, lists, mail, mailing, mailing list, mailing lists, mailings, newsletter, newsletters, portuguese, register, registration, responsive, responsive emails, sign up, signup form, sidebar, spanish, widget, widgets, woocommerce
Requires at least: 4.9.16
Requires PHP: 7.2
Tested up to: 5.6
Stable tag: 3.3
License: GPLv2 (or later)

Your Wordpress Site and Email Marketing all in one place!


== Description ==

[youtube https://www.youtube.com/watch?v=O-bawo9m-MM&feature=youtu.be]

=== What is Benchmark Email Lite? ===

Benchmark Email Lite is a WordPress plugin that saves you time by giving you access to your email marketing account directly in your WordPress dashboard.

* Easily convert blog posts into email campaigns to increase the reach of your content
* Turn site visitors into subscribers by creating signup forms and pop-up modals that will automatically adapt to your WordPress theme
* Streamline your workflows by creating and scheduling any email campaign directly from your WordPress dashboard

=== Send More Targeted Email Campaigns with Website Tracking ===

When you install Benchmark Email Lite on your WordPress site, it will automatically install the Automation Pro website tracker.

* Follow-up with email campaigns based on the products, services or content that subscribers viewed on your website
* Boost your sales by automating emails that further convince a subscriber of the value of a product or service they showed interest in by visiting a page on your site
* A site visitor viewing a specific page can trigger an email with additional information on a product or service

=== Access Your Whole Email Marketing Account from Your WordPress Dashboard ===

Say goodbye to at least one extra tab on your browser.

* Discover the blog content that is most popular with your subscribers by viewing your open and click reports directly in your WordPress dashboard—then create more content just like it!
* Create content and promote it all from the same place

=== Easily Grow Your Most Valuable Marketing Asset: Your Email List ===

No coding necessary!

With the Benchmark Email Lite plugin, you can quickly and easily place a signup form anywhere you want on your site. It’s also super simple to customize your forms.


== Installation ==

= Installing the plugin =

1. In your WordPress admin panel, go to Plugins > New Plugin, search for Benchmark Email Lite and click "Install now".
2. Alternatively, download the plugin and upload the contents of benchmark-email-lite.zip to your plugins directory, which usually is /wp-content/plugins/.
3. Activate the plugin.

= Initial setup =

1. If you are creating a new Benchmark Email account, [get a FREE account here](https://ui.benchmarkemail.com/register?p=68907 "get a FREE Benchmark Email account!").
2. In your WordPress admin panel, go to Benchmark > Settings.
3. Click "Connect to Benchmark".
4. Enter your Benchmark Email username and click "OK".
5. Enter your Benchmark Email password and click "OK".


== Frequently Asked Questions ==

= Q1: Where do I go for help with any issues? =

Please call Benchmark Email at (800) 430-4095.

= Q2: The Benchmark Interface panel is not authenticating. =

Most browsers block the authentication cookies between the main window (WordPress) and the inline frame window (Benchmark) for security (CORS). This can be overcome by clicking the link we provide to open in a new tab. Following that, if you quit your browser and log back into your WordPress site the frame should load properly from then point onward. If you continue to have problems, try logging into [Benchmark UI](https://ui.benchmarkemail.com) directly, then quit your browser and log back into WordPress site. Taking these steps should return the Interface to an authenticated state.

= Q3: What is an RSS email campaign? How to use? =

RSS campaigns send out a digest of blog posts on a schedule using your RSS feed URL. Further details can be found here: https://kb.benchmarkemail.com/how-do-i-create-an-rss-to-email-campaign/

Inside your WordPress Admin area, select Benchmark > Interface. Navigate to Emails > Create > RSS Email > Drag/Drop.

Enter your WordPress URL, which is your website followed by /feed. You may wish to filter the posts by category or tag, for example: https://codedcommerce.com/category/marketing-tips/feed. You may also use category IDs and filter nagatively to exclude a comma-separated list of them, for example: https://codedcommerce.com/?cat=-52047&feed=rss2

= Q4: Can I use Contact Form 7 (Gravity Forms, etc.) with this plugin? =

Yes. The plugin does not directly use any form frameworks other than native Benchmark Email signup forms. As with most WordPress projects, you can bridge the two together with custom code. We've assembled an example based on Contact Form 7. Install the [Code Snippets plugin](https://wordpress.org/plugins/code-snippets/), import the [sample code file](https://www.dropbox.com/s/hvgfsl7nttjrx3s/connect-contact-form-7-form-with-benchmark-email-contact-list.code-snippets.json?dl=0), edit the code snippet to change the list ID mappings within the code and tailor the form field names to match your form (or visa-versa), then enable the code snippet and test.

= Q5: Can I programmatically filter the HTML email campaigns? =

Yes. In v3.3 we've added new hooks you can use in your custom functions. The new hooks are:

1. `add_filter( 'wpbme_post_title', function( $value, $post ) { return $value; }, 10, 2 );`
2. `add_filter( 'wpbme_post_content', function( $value, $post ) { return $value; }, 10, 2 );`
3. `add_filter( 'wpbme_email_type', function( $value, $post ) { return $value; }, 10, 2 );`
4. `add_filter( 'wpbme_email_html', function( $value, $post ) { return $value; }, 10, 2 );`

These filter the blog post title, the content body, the email type and generated HTML body. The email type is `DD` for the drop-down email editor and can be changed to `Custom` when doing raw HTML.


== Screenshots ==

1. This is the plugin settings panel.
2. This is the version 3.x Benchmark admin interface.
3. This is the sidebar widget admin panel.
4. This is the shortcodes admin panel.
5. This is a sample signup form on a page sidebar with TwentyNinteen theme.
6. This is a sample pop-up signup form being used on a home page. 


== Changelog ==

= 3.4 on TBA =

* Added: new Bulk Action feature to be able to send multiple selected posts in one campaign.
* Fixed: saving of signup form transient when data from API is an error.

= 3.3 on 2020-08-14 =

* Added: new filters for P2C email body title, content, and generated HTML.
* Added: new filter for switching the email type from drop-down (DD) to Custom.
* Added: Readme FAQs for the new filters and RSS campaigns.
* Added: settings descriptions for the less common settings.
* Updated: Switched the Interface iFrame over to a JS pop-up due to a conflict with browser security blocks.
* Updated: post-to-campaign endpoint.
* Updated: Benchmark connection settings to cleaner, non AJAX, links to toggle key fields.
* Fixed: admin Connect button bad username or password handling.
* Fixed: Removed UI auth redirection when endpoint contains querystring to workaround a URL encoding API bug.

= 3.2 on 2020-03-12 =

* Fixed: error trap for missing Sample Contact List when creating email campaigns.
* Updated: developer analytics cache-busting for improved results.

= 3.1 on 2020-02-06 =

* Fixed: WP hook improvement to prevent a PHP warning during upgrading.
* Fixed: added silence-is-golden security to two files that were missing it.
* Updated: cleaned-up developer analytics tracking post-to-campaign email send.
* Updated: developer analytics tracker widget moved from widget-load to widget-save.

= 3.0 on 2019-08-05 =

* Added: Optional user submission tracker for Automation Pro workflows.
* Added: Optional admin usage tracker to help drive our development roadmap.
* Added: Post to email campaigns template that enables the drag/drop editor for improved design.
* Updated: Migrates legacy v2.x plugin signup forms to Benchmark signup form entities to maximize the design and configuration options and enables the pop-up style signup forms.
* Updated: Easier initial plugin configuration steps with connect/reconnect wizard.
* Updated: A cleaner admin interface.
* Updated: Switched from the XML-RPC API over to the newer and faster REST API.

= 2.11 on 2019-01-06 =

* Fixed: Classic Editor metabox datepicker and slider are back in operation by adding jQuery UI theme.
* Fixed: Gutenberg metabox success/error responses by adding AJAX based submission.
* Fixed: Gutenberg metabox not processing SEND button on post-to-campaign unless you also clicked UPDATE button.

= 2.10 on 2018-11-29 =

* Fixed: function call outside of plugins page
* Fixed: invalid function call is_plugin_inactive
* Fixed: API key URL, update plugin author name and link

= 2.9 on 2018-10-29 =
* Added: Admin dashboard notice of sister product availability if WooCommerce is active.
* Removed: WooCommerce customers list as this has moved to our free sister product with enhancements.

= 2.8 on 2018-09-21 =
* Added: Optional privacy policy checkbox field to subscription forms for GDPR.
* Updated: Newsletter sign up form paragraphs for better spacing in most themes.
* Fixed: One deprecated notice for admin page.
* Fixed: A few missing translation text wrappers.

= 2.7 on 2018-03-01 =

* Added: WooCommerce customer to contact list feature.
* Updated: Common libraries with the Joomla! plugin.
* Updated: Author URLs removed `www.` for consistency.
* Fixed: JS error when entering the theme customizer.
* Fixed: Ability to replace BODY_HERE with EXCERPT in email template.
* Fixed: Newsletter signup form validation code improvements from @talgat in support forum.

= 2.6 on 2016-06-25 =

* Added: new email template anchors for categories, excerpt, featured image, permalink, tags thanks to a suggestion from Pedro on Forum.
* Added: communication logging and admin area UI tab displaying last 25 communications for diagnostic use.
* Added: cron queue viewer to Comm Log tab.
* Updated: Benchmark Email API server version from 1.0 to 1.3
* Updated: post metabox JS form validation to place multiple errors into a single pop-up message to match the front end.
* Updated: general code cleanups.
* Updated: removed transient caching of email report level 2 as it was unnecessary and was somewhat conflicting with level 1 cache.
* Updated: Increased jQuery UI version to the latest stable 1.11.4 minified version.
* Updated: Increased commection timeout default to 20 seconds to reflect latest testing.
* Updated: subscription logic to fix a few issues, addresses user sometimes receiving "Failed to add subscription" error message and a problem where sign up forms with multiple lists were only updating the first list.
* Fixed: widget constructor deprecated notice since WP 4.3.0 when DEBUG mode is ON.
* Fixed: vendor handshake to set all API keys instead of just the first one, and to refresh every 30 days.
* Fixed: escaping of values during transmission of subscriptions for characters like backslash, single quote, double quote.

= 2.5 on 2015-04-13 =

* Added: Support for widget signup form based subscriptions.
* Added: Localization logic with embedded translation files for Spanish, Portuguese/BR, and Chinese/Traditional/TW regions.
* Added: Pre-population of Metabox Admin email name and from name fields.
* Updated: Consolidated loading graphic to WP core image. Replaced subscribe form LIs with DIVs.

= 2.4.6 on 2014-06-17 =

* Added: jQuery UI slider and datepicker elements for date and time selections.
* Added: Method in API class for downloading the list of signup forms, future use.
* Updated: Misc code cleanup and optimizations.
* Fixed: A PHP warning in admin_init hook widget upgrade code when no tokens are configured and debug mode is on. Credit to frenchbob for the bug report.
* Fixed: A caching problem with reports not clickable a second time during 5min cache interval, removed tokenindex from reports area.

= 2.4.5 on 2014-01-04 =

* Added: Email Template tab with instructions for customizing the email template colors, fonts, and logo, with button to reset template to default values.
* Added: HTML processing library to normalize the email template and body HTML and convert WP core classes to embedded CSS for GMail.
* Added: Overall report of all links clicked and by whom, in addition to the click performance report and link detail report.
* Added: Link to flush/refresh transients on reports level 1 and 2 screens now that we use the 5min caching.
* Added: Auto flushing of report transients/cache at new campaign creation.
* Added: Email template EMAIL_MD5_HERE anchor to bring in the Gravatar site admin email hash for logo sample purposes.
* Added: Settings saved message to settings panels.
* Added: Hook to prepopulate with the default values when setting is new on an installation, logic that resets the template to default to always run except when template contains BODY_HERE to ensure that no faulty template not containing the page body can be saved.
* Updated: Default template HTML a bit and adjusted descriptive text on the template page editor as well as target=_blank linking.
* Updated: JS error alert box linebreaks for FireFox compatibility. Put metabox date/time fields line break to separate them a bit.
* Updated: Reports WP transients to store email list for 5mins (level 1) and campaign summaries (level 2) for 5mins. Campaign details (level 3) continue to be pulled in realtime. Improves performance when clicking around.
* Updated: Pie chart background for transaprency for WP38 admin theming.
* Updated: Widget and shortcode submission handling to return the form upon errors. Trac references #2103.
* Updated: Metabox time drop down to display in North American standard.
* Updated: Gettext i18n echoing for cleaner code.
* Updated: Email rendering code to pull from the new admin setting for Email Template HTML prior to using the previous template file.
* Updated: Settings API structuring to accomodate the second settings page.
* Fixed: Bug in the code that deactivates widgets of disabled API keys. Thanks to kris0499 for the issue report.
* Fixed: Repopulation of widget after submission errors occur. Trac references #2104
* Fixed: FaultCode detection to level 1 reports page. Trac references #2101

= 2.4.4 on 2013-12-14 =

* Added: static function declarations for WP3.6 strict standards debug mode compatibility.
* Updated: Tab name from Reports to Emails to make room for changes to the email listings format.
* Updated: Moved some methods around for optimal OOP mojo.
* Updated: Page anchor for the timezone selection on BME UI, as linked from the metabox timezone paragraph.
* Updated: Date selector to show dates up to 365 days into the future, the limit of the BME interface as well.
* Updated: Reports area table column widths to be more uniform and consistent. Set campaign caching timeout to 1 hour to expire old data.
* Updated: URL to WP core arrow images for widget admin side due to 3.8 no longer having the arrows-vs.png file. Changed to arrows.png file that exists with both admin themes pre 3.8 and post 3.8. Trac references #2083
* Removed: DIV wrapper of plugin as it was causing duplicate IDs and in T14 the response body was invisible. Trac references #2085
* Fixed: Widget instances used within page shortcodes to prevent PHP strict standards notice about calling the widget class statically.
* Fixed: PHP warning 'division by zero' for campaigns with 0 opens on the main campaign summary screens.
* Fixed: Typo in the settings script.
* Fixed: Printing of extra widget wrapper on forms and responses. Added code to track if widget is in shortcode mode. Trac references #2084 #2086
* Fixed: Widget pre title html echo to be before title. Trac references #2081 and #2082
* Fixed: Widget API key deletion to inactive sidebar disablement code to prevent action from recurring after the widget was disabled. Trac references #2080

= 2.4.3 on 2012-12-05 =

* Added: Filter 'benchmarkemaillite_compile_email_theme' to allow external email template customization. See FAQ.
* Added: Automatic radio button selection of sending type on the metabox when sub elements are chosen.
* Added: Message when a widgets of deleted API keys are being deactivated.
* Updated: Feedback responses on the post metabox, especially for the successful sending of test email campaigns.
* Updated: Renamed some function names to match the WP hook they relate to.
* Updated: Changed message on sent test email campaigns to say 'sent' instead of 'created'.
* Updated: Updated WP admin notice box contents to look more normal, yellow or red based on status of message as error or update message.
* Updated: Minor enhancement to settings links code.
* Fixed: Bug regarding the Please Select option for widget administration.
* Fixed: Bug on defaulting of the list selection, by deactivating widgets of deleted API keys.
* Fixed: Bug with field validation when the same widget exists on a page in both a shortcode and a sidebar widget.
* Fixed: Bug with the missing API key error persisting during the first refresh after putting in an API key.

= 2.4.2 on 2012-11-07 =

* Added: Looping of detailed report data requests, to present full table of data (over 100 limit).
* Added: Line number counter to detailed reports.
* Added: Connection timeout warning atop the settings and reports interfaces with button to reconnect.
* Added: Descriptive text on the individual email detail reports.
* Updated: Moved report under Links Clicked to the email campaign page as Click Performance to more closely mirror the BME interface.
* Updated: Split email links report into parent and child reports per URL being referenced.
* Removed: Lower limit on timeout. Was set at a minimum of 10 seconds.
* Fixed: Changed the Opens By Country report on the main email campaign page to only show when there has been at least 1 recorded open.

= 2.4.1 on 2012-10-17 =

* Added: Ability to send test email copies to up to 5 addresses.
* Added: Administration sidebar menu flyouts to match tabs.
* Updated: Organized library, markup, and JS files into folders.
* Updated: Reorganized reports code for better optimization.
* Updated: Reports tab reporting when there are no reports, or no reports for specific keys.
* Updated: Adjusted reports table column widths.
* Updated: Set maxlength settings for posts/pages metabox text input fields.
* Fixed: Error message window was persisting for 5 seconds, when it should just display once.
* Fixed: Email template height 100% was causing extra space in footer of test emails.

= 2.4 on 2012-08-15 =

* Added: Shortcode that allows any sign up form widget to be used within pages and posts.

= 2.3.1 on 2012-07-18 =

* Fixed: Minor bug with the subtitle display during widget administration.

= 2.3 on 2012-06-04 =

* Added: Full email campaign reporting capabilities.
* Updated: Moved menu item from the Settings section to a top level item with icon.

= 2.2.1 on 2012-03-28 =

* Added: Setting for connection timeout to be customized for diagnostic purposes. See FAQ.
* Fixed: Removed Extra 1 and Extra 2 widget field options due to newer defaults. See FAQ. 

= 2.2 on 2012-03-26 =

* Added: Scheduling capabilities for post to campaigns feature.
* Added: Prevents admin area slowdowns by detecting API server connections over 5 seconds and disabling communications for 5 minutes.

= 2.1 on 2012-03-12 =

* Added: All additional fields supported by BME onto widget signup form administration.
* Added: Upgrade procedure from v2.0.x to 2.1 saved widgets.
* Fixed: Notices showing up upon page/post deletion.

= 2.0.2 on 2012-02-20 =

* Fixed: Queue unloading was failing when the API connection goes down and subscriptions come in.

= 2.0.1 on 2012-02-13 =

* Added: Green and red indicators adjacent to entered API keys on the Benchmark Email Lite settings page, showing status upon save.
* Added: Benchmark Email response code into error message after creating an email campaign failure.
* Fixed: Includes silent updates made to the v2.0 release concerning automatic upgrading of saved widget settings from earlier v1.x.
* Fixed: v3.3.0 compatibility when no widgets preexisted from earlier version caused a warning at the top of the screen.
* Fixed: After deleting API key, the warnings about the need to have an API key weren't being fired anymore.
* Fixed: Bad API key(s) were triggering an error "Unable to connect" that wasn't very helpful.
* Fixed: Bad API key(s) were causing the good API keys to not be considered and utilized.

= 2.0 on 2012-01-31 =

* Added: Ability to create Benchmark Email campaigns from WordPress pages and posts.
* Added: Ability to send post campaigns immediately to either a test address or a selected Benchmark Email list.
* Added: Plugin settings page for global API Key(s) and campaign settings.
* Updated: Moved API key setting from individual widgets to a new Plugin settings panel.
* Updated: Split PHP functions among several classes/files for organization and growth.
* Updated: Added warranty disclaimer text in the main Plugin file header.
* Fixed: W3C validation error on strict mode, caused by two hidden input fields.
* Fixed: Localizations (language) weren't being loaded properly for International support.

= 1.1 on 2011-12-07 =

* Added: Prepopulation of the fields for logged in users.
* Added: Ability to toggle first and last name fields off.
* Added: Optional text to display to your readers.
* Added: Ability to change the text of the subscribe button.
* Added: Added widget title display in widget subtitle bar area.
* Added: New link on widget admin panel to view list names on BME.
* Updated: Expanded widget administration panel width to 400 pixels.
* Updated: Moved widget back end and front end HTML markup code to separate files.
* Updated: Added text next to the API Key field regarding FAQ #1.
* Updated: Sanitization functions adjusted.
* Removed: Output of widget set-up instructions on website before the widget is configured.
* Fixed: Bug when extra spaces existed between words in the list name admin vs front end behavior differed.

= 1.0.5 on 2011-08-18 =

* Updated: Moved the subscriptions queueing in the event of API failure to WP cron instead of being triggered upon subsequent subscription.
* Updated: Moved the subscriptions queueing in the event of API failure storage from a CSV file type storage to storage in the WordPress database. Prevents filesystem permissions issues.
* Updated: Cleaned-up some code with unnecessary referencing of widget IDs during subscriptions processing.
* Updated: Renamed "cache" to "queue" for clarification about the failover handling support.
* Fixed: Added a new button on the widget configuration panel to "Verify API Key and List Name" as the previous tab-off event wasn't always being executed if users click Save without first tabbing off the elements to be tested.

= 1.0.4 on 2011-06-20 =

* Added: AJAX feedback mechanism in widget administration that checks the API key and list name fields against the Benchmark Email database and reports status.
* Updated: Subscription to utilize Benchmark Email's double opt-in method. This prevents the problem when somebody who wants to re-subscribe can't get out of the Master Unsubscribe List.

= 1.0.3 on 2011-05-23 =

* Added: BME API key to failover CSV temporary buffer file.
* Added: Spinner icon appearance upon front end form submission.
* Updated: The response texts to proper case.
* Updated: The CSV buffer file processing logic and combined with main process.
* Fixed: PHP notices showing up when debug mode is turned on in `wp-config.php`.
* Removed: Display of the front end form upon successful submission.

= 1.0.2 on 2011-05-18 =

* Added: Failover handling. If the API becomes unavailable the Plugin will dump subscriptions into a CSV buffering file in the Plugin folder that will attempt to post to the API and clear the file upon each subsequent subscription submission.
* Updated: The first name and last name field titles from "firstname" to "First Name" per the spec of the newly released API.
* Fixed: Bug when multiple widgets exist on a page and sometimes aren't being keyed properly, causing the processor to not always know which widget is being submitted.

= 1.0.1 on 2011-05-14 =

* Added: Support for international language translation/localization.
* Added: Anchor `#benchmark-email-lite` into URL so that after form submission it puts the user on the proper screen position to view the server response.
* Updated: Admin area widget field sanitization method to `sanitize_text_field()` function requiring v2.9.0.
* Updated: Title for the Benchmark Email Token to the term "API Key" to match what Benchmark Email is calling it on their website.
* Updated: The server response to clear out the submitted values upon successful form submission.
* Fixed: Bug in first name, last name, and email address submitted data sanitizing to be compatible with international symbols or anything that WordPress considers safe for data validation purposes. Reference: `sanitize_email()` and `sanitize_text_field()` functions on WordPress Codex.
* Fixed: Bug when the widget is installed multiple times on a single page leading to only one form pre-populating the entered data and some CSS conflicts. Multiple instances per page are now supported!

= 1.0 on 2011-05-12 =

* Added: Initial Plugin release.


== Upgrade Notice ==

= 3.2 =

* Bug fix release.

= 3.1 =

* Bug fix release.

= 3.0 =

* Major feature release migrating legacy signup forms to Benchmark signup form entities to maximize the design options and enable pop-up type forms, new tracking capabilities, post to email campaigns template featuring drag/drop editor, easier initial plugin configuration and overall cleaner interface.

= 2.11 =

* Gutenberg related bug fix release.

= 2.10 =

* Bug fix release.

= 2.9 =

* Sister product notification regarding WooCommerce and updates.

= 2.8 =

* GDPR update and fixes.

= 2.7 =

* Various fixes and improvements.

= 2.6 =

* New email template variables, communication log, fixes and enhancements.

= 2.5 =

* Added support for sign up form lists and several geographic localizations.

= 2.4.6 =

* Added UI improvements to pages/posts metabox, a few misc updates and fixes.

= 2.4.5 =

* This adds an email template editing interface and contains several other updates and fixes.

= 2.4.4 =

* This is a bugfix release to bring us current to WP 3.8

= 2.4.3 =

* This is a bugfix release that also adds the ability to programmatically customize the email template.

= 2.4.2 =

* Several more updates to the reports tab, and better handling of connection timeout situations.

= 2.4.1 =

* Added ability to send to multiple test addresses. Many small updates included!

= 2.4 =

* Added signup form shortcodes.

= 2.3.1 =

* Minor bug fix with the subtitle display during widget administration.

= 2.3 =

* Added full email campaign reporting capabilities.

= 2.2.1 =

* Fixes for connection timeout setting, and Extra 1 and Extra 2 widget fields.

= 2.2 =

* Added scheduling capabilities for post to campaign feature. Also added API server speed detection/maneuvering.

= 2.1 =

* Added additional fields to the signup form widget administration.

= 2.0.2 =

* Fixed a single bug related to subscription queueing.

= 2.0.1 =

* Several minor fixes to API key settings and a couple minor related enhancements as well.

= 2.0 =

* New settings panel and post-to-campaign support. API keys are moved from widgets to settings panel on activation.

= 1.1 =

* Made name fields optional and several other enhancements. See changelog.

= 1.0.5 =

* New admin area verification button and utilizing WP cron system for failover queue (see changelog).

= 1.0.4 =

* New admin area verifications and use of douple opt-in (see changelog).

= 1.0.3 =

* Minor code updates and UI enhancements (see changelog).

= 1.0.2 =

* Added failover handling whe the API becomes unavailable and changed first name and last name field labels for their new API.

= 1.0.1 =

* Fixes a couple of bugs and adds a few minor features!

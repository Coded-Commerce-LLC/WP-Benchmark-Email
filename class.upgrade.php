<?php

/*

	// Transfer Primary API Key
	OLD: wp_options.benchmark-email-lite_group[1][0]
	NEW: wp_options.wpbme_key

	// Transfer All Widgets
	OLD: wp_options.widget_benchmarkemaillite_widget
		Array(
			[2] => Array(
				[fields] => Array(
					[1] => First Name
					[2] => Last Name
					[3] => Email
				)
				[fields_labels] => Array(
					[1] => First Name
					[2] => Last Name
					[3] => Email
				)
				[fields_required] => Array(
					[1] => 0
					[2] => 0
					[3] => 1
				)
				[button] => Subscribe
				[description] => Get the latest news and information direct from us to you!
				[filter] => 0
		CASE:	[list] => APIKEY|Sample Contact List|15838208
		CASE:	[list] => APIKEY|Sample Signup Form|1077728
				[page] => 0
				[title] => Subscribe to Newsletter
			)
			[_multiwidget] => 1
		)
	NEW: wp_options.widget_wpbme_widget[2][post_id]

	// Transfer All Shortcodes
	OLD: [benchmark-email-lite widget_id="2"]
	NEW: [benchmark-email-lite form_id="1077728"]

*/

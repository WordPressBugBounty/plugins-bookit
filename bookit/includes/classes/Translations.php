<?php

namespace Bookit\Classes;

class Translations {

	/**
	 * Frontend Translation Strings
	 * @return array
	 */
	public static function get_frontend_translations() {
		$translations = array(
			'choose_category'          => esc_html__( 'Choose Category', 'bookit' ),
			'choose_service'           => esc_html__( 'Choose Service', 'bookit' ),
			'from'                     => esc_html__( 'From', 'bookit' ),
			'no_services'              => esc_html__( 'No available Services!', 'bookit' ),
			'booking_appointment'      => esc_html__( 'Booking Appointment', 'bookit' ),
			'service'                  => esc_html__( 'Service', 'bookit' ),
			'employee'                 => esc_html__( 'Employee', 'bookit' ),
			'customer'                 => esc_html__( 'Customer', 'bookit' ),
			'date'                     => esc_html__( 'Date', 'bookit' ),
			'time'                     => esc_html__( 'Time', 'bookit' ),
			'price'                    => esc_html__( 'Price', 'bookit' ),
			'submit'                   => esc_html__( 'Submit', 'bookit' ),
			'please_choose_service'    => esc_html__( 'Please choose a Service!', 'bookit' ),
			'booking_details'          => esc_html__( 'Booking Details', 'bookit' ),
			'full_name'                => esc_html__( 'Full Name', 'bookit' ),
			'email'                    => esc_html__( 'Email', 'bookit' ),
			'phone'                    => esc_html__( 'Phone', 'bookit' ),
			'password'                 => esc_html__( 'Password', 'bookit' ),
			'password_confirmation'    => esc_html__( 'Password Confirmation', 'bookit' ),
			'locally'                  => esc_html__( 'I will pay locally', 'bookit' ),
			'paypal'                   => esc_html__( 'Pay now with PayPal', 'bookit' ),
			'stripe'                   => esc_html__( 'Pay now with Credit Card', 'bookit' ),
			'stripeConnect'                              => esc_html_x( 'Pay now with Credit Card', 'Stripe Connect pay with credit card.', 'bookit' ),
			'woocommerce'              => esc_html__( 'WooCommerce Checkout', 'bookit' ),
			'book_now'                 => esc_html__( 'Book Now', 'bookit' ),
			'success_booking'          => esc_html__( 'Your Appointment Request successfully sent!', 'bookit' ),
			'reservation_confirmation' => esc_html__( 'Reservation confirmation', 'bookit' ),
			'booking_email_sent'       => esc_html__( 'We have sent your booking information to your email address.', 'bookit' ),
			'you_will_be_redirected'   => esc_html__( 'You will be redirected to', 'bookit' ),
			'in'                       => esc_html__( 'in', 'bookit' ),
			'seconds'                  => esc_html__( 'seconds', 'bookit' ),
			'client_name'              => esc_html__( 'Client Name', 'bookit' ),
			'reservation_time'         => esc_html__( 'Reservation Time', 'bookit' ),
			'reservation_date'         => esc_html__( 'Reservation Date', 'bookit' ),
			'print_confirmation'       => esc_html__( 'Print Confirmation', 'bookit' ),
			'required_field'           => esc_html__( 'This field is required.', 'bookit' ),
			'invalid_email'            => esc_html__( 'Invalid Email address.', 'bookit' ),
			'confirmation_mismatched'  => esc_html__( 'Confirmation mismatched.', 'bookit' ),
			'not_available'            => esc_html__( 'Not Available', 'bookit' ),
			'no_appointments'          => esc_html__( 'No Booked Appointments', 'bookit' ),
			'status'                   => esc_html__( 'Status', 'bookit' ),
			'approve'                  => esc_html__( 'Approve', 'bookit' ),
			'reject'                   => esc_html__( 'Reject', 'bookit' ),
			'cancel'                   => esc_html__( 'Cancel', 'bookit' ),
			'something_went_wrong'     => esc_html__( 'Something went wrong', 'bookit' ),
			'not_authenticated_card'   => esc_html__( 'Your card was not authenticated, please try again', 'bookit' ),
			'available_employees'      => esc_html__( 'All available employees', 'bookit' ),
			'service_price'            => esc_html__( 'Service price', 'bookit' ),
			'select_date'              => esc_html__( 'Select date', 'bookit' ),
			'select_time'              => esc_html__( 'Select time', 'bookit' ),
			'error_staff_id'           => esc_html__( 'Select employee', 'bookit' ),
			'name_label'               => esc_html__( 'Name', 'bookit' ),
			'name_placeholder'         => esc_html__( 'Enter your Name', 'bookit' ),
			'print'                    => esc_html__( 'Print', 'bookit' ),
			'back'                     => esc_html__( 'Back', 'bookit' ),
			'new_booking'              => esc_html__( 'New Booking', 'bookit' ),
			'continue'                 => esc_html__( 'Continue', 'bookit' ),
			'add_to_calendar'          => esc_html__( 'Add to calendar', 'bookit' ),
			'service_note'             => esc_html__( 'Service note', 'bookit' ),
			'service_note_placeholder' => esc_html__( 'Your Message', 'bookit' ),
			'invalid_phone'            => esc_html__( 'Please enter a valid phone number', 'bookit' ),
			'no_email'                 => esc_html__( 'Please enter email', 'bookit' ),
			'full_name_wrong_length'   => esc_html__( 'Full name must be between 3 and 25 characters long', 'bookit' ),
			'total'                    => esc_html__( 'Total', 'bookit' ),
			'payment_method'           => esc_html__( 'Payment method', 'bookit' ),
			'google_calendar'          => esc_html__( 'Google Calendar', 'bookit' ),
			'i_cal'                    => esc_html__( 'iCal Export', 'bookit' ),
			'download_pdf'             => esc_html__( 'Download PDF', 'bookit' ),
			'error_date_timestamp'     => esc_html__( 'Please select date', 'bookit' ),
			'error_start_time'         => esc_html__( 'Please select time', 'bookit' ),
			'error_payment_method'     => esc_html__( 'Please select payment', 'bookit' ),
			'available_for_booking'    => esc_html__( 'Available for booking', 'bookit' ),
			'unavailable_for_booking'  => esc_html__( 'Unavailable for booking', 'bookit' ),
			'wrong_password'           => esc_html__( 'Wrong password', 'bookit' ),
		);

		return $translations;
	}

	/**
	 * Admin Translation Strings
	 * @return array
	 */
	public static function get_admin_translations() {
		$translations = array(
			'id'                            => esc_html__( 'id', 'bookit' ),
			'search'                        => esc_html__( 'Search', 'bookit' ),
			'add_new'                       => esc_html__( 'Add New', 'bookit' ),
			'categories'                    => esc_html__( 'Categories', 'bookit' ),
			'category'                      => esc_html__( 'Category', 'bookit' ),
			'no_results'                    => esc_html__( 'No Results', 'bookit' ),
			'appointment'                   => esc_html__( 'Appointment', 'bookit' ),
			'all_appointments'              => esc_html__( 'All Appointments', 'bookit' ),
			'appointment_info'              => esc_html__( 'Appointment Information', 'bookit' ),
			'add'                           => esc_html__( 'Add', 'bookit' ),
			'create'                        => esc_html__( 'Create', 'bookit' ),
			'edit'                          => esc_html__( 'Edit', 'bookit' ),
			'delete'                        => esc_html__( 'Delete', 'bookit' ),
			'save'                          => esc_html__( 'Save', 'bookit' ),
			'approve'                       => esc_html__( 'Approve', 'bookit' ),
			'reject'                        => esc_html__( 'Reject', 'bookit' ),
			'cancel'                        => esc_html__( 'Cancel', 'bookit' ),
			'past'                          => esc_html__( 'Past', 'bookit' ),
			'appointment_past'              => esc_html__( 'Appointment is past', 'bookit' ),
			'add_new_category'              => esc_html__( 'Add New Category', 'bookit' ),
			'enter_category_name'           => esc_html__( 'Enter Category Name', 'bookit' ),
			'all'                           => esc_html__( 'All', 'bookit' ),
			'pending'                       => esc_html__( 'Pending', 'bookit' ),
			'approved'                      => esc_html__( 'Approved', 'bookit' ),
			'cancelled'                     => esc_html__( 'Cancelled', 'bookit' ),
			'customer'                      => esc_html__( 'Customer', 'bookit' ),
			'customer_phone'                => esc_html__( 'Customer Phone', 'bookit' ),
			'staff'                         => esc_html__( 'staff', 'bookit' ),
			'service'                       => esc_html__( 'service', 'bookit' ),
			'services'                      => esc_html__( 'Services', 'bookit' ),
			'price'                         => esc_html__( 'Price', 'bookit' ),
			'date'                          => esc_html__( 'Date', 'bookit' ),
			'time'                          => esc_html__( 'Time', 'bookit' ),
			'payment'                       => esc_html__( 'Payment', 'bookit' ),
			'status'                        => esc_html__( 'Status', 'bookit' ),
			'actions'                       => esc_html__( 'Actions', 'bookit' ),
			'details'                       => esc_html__( 'Details', 'bookit' ),
			'payment_method'                => esc_html__( 'Payment method', 'bookit' ),
			'no_payment_method'             => esc_html__( 'No Payment Method', 'bookit' ),
			'payment_status'                => esc_html__( 'Payment status', 'bookit' ),
			'del_appointment_info'          => esc_html__( 'You are going to delete appointment ', 'bookit' ),
			'send_notification_to'          => esc_html__( 'Send Notification to:', 'bookit' ),
			'cancel_reason_title'           => esc_html__( 'Reason for cancellation ', 'bookit' ),
			'reason_placeholder'            => esc_html__( 'Reason message', 'bookit' ),
			'employee'                      => esc_html__( 'Employee', 'bookit' ),
			'confirm_service_edit_duration' => esc_html__( 'Changing of the service duration will not affect the existing appointments and will be applied only to new appointments', 'bookit' ),
			'full_name'                     => esc_html__( 'Full Name', 'bookit' ),
			'customer_name'                 => esc_html__( 'Customer name', 'bookit' ),
			'email'                         => esc_html__( 'Email', 'bookit' ),
			'phone'                         => esc_html__( 'Phone', 'bookit' ),
			'working_hours'                 => esc_html__( 'Working Hours', 'bookit' ),
			'lunch_break'                   => esc_html__( 'Lunch Break', 'bookit' ),
			'confirm_edit_staff_wh'         => esc_html__( 'Changing of the Staff working hours will not affect the existing appointments and will be applied only to new appointments', 'bookit' ),
			'currency_error'                => esc_html__( 'Wrong currency value', 'bookit' ),
			'appointment_id'                => esc_html__( 'Appointment ID', 'bookit' ),
			'filter_by_service'             => esc_html__( 'All Service', 'bookit' ),
			'filter_by_staff'               => esc_html__( 'All Staff', 'bookit' ),
			'selected'                      => esc_html__( 'Selected', 'bookit' ),
			'select'                        => esc_html__( 'Select', 'bookit' ),
			'autorefresh'                   => esc_html__( 'Auto Refresh', 'bookit' ),
			'detailed_view'                 => esc_html__( 'Detailed view', 'bookit' ),
			'disabled'                      => esc_html__( 'disabled', 'bookit' ),
			'total'                         => esc_html__( 'Total', 'bookit' ),
			'icon'                          => esc_html__( 'Icon', 'bookit' ),
			'duration'                      => esc_html__( 'Duration', 'bookit' ),
			'title'                         => esc_html__( 'Title', 'bookit' ),
			'confirm_service_delete_title'  => esc_html__( 'You have {appointments} active appointments and {staff} staff associated with this service.', 'bookit' ),
			'confirm_service_delete'        => esc_html__( 'If you delete the service,  all appointments associated with it, will be deleted as well.  You can edit the service instead.', 'bookit' ),
			'confirm_staff_delete_title'    => esc_html__( 'You have {appointments} active appointments and {services} services associated with this staff member.', 'bookit' ),
			'confirm_staff_delete'          => esc_html__( 'If you remove the employee, all related services will remain without staff, and all appointments will be deleted.', 'bookit' ),
			'confirm_customer_delete_title' => esc_html__( 'You have {appointments} active appointments associated with this customer.', 'bookit' ),
			'confirm_customer_delete'       => esc_html__( 'If you remove the customer, all appointments associated with customer will be deleted.', 'bookit' ),
			'confirm_category_delete_title' => esc_html__( 'You have {services} active services, {appointments} active appointments and {staff} staff associated with this category.', 'bookit' ),
			'confirm_category_delete'       => esc_html__( 'If you delete the category,  all service and appointments associated with it, will be deleted as well.  You can edit the category instead.', 'bookit' ),
			'edit_appointments'             => esc_html__( 'Edit Appointments', 'bookit' ),
			'time_slot_duration_title'      => esc_html__( 'The duration of the time slot', 'bookit' ),
			'woocommerce_alert'             => esc_html__( 'To use this feature you need to install WooCommerce Plugin.', 'bookit' ),
			'comment'                       => esc_html__( 'Comment', 'bookit' ),
			'comment_placeholder'           => esc_html__( 'Enter your comment', 'bookit' ),
			'new_customer'                  => esc_html__( 'New customer', 'bookit' ),
			'search_placeholder'            => esc_html__( 'Type to search', 'bookit' ),
			'password'                      => esc_html__( 'Password', 'bookit' ),
			'password_confirmation'         => esc_html__( 'Password Confirmation', 'bookit' ),
			'required_field'                => esc_html__( 'This field is required.', 'bookit' ),
			'invalid_email'                 => esc_html__( 'Invalid Email address.', 'bookit' ),
			'confirmation_mismatched'       => esc_html__( 'Confirmation mismatched.', 'bookit' ),
			'not_available'                 => esc_html__( 'Not Available', 'bookit' ),
			'password_placeholder'          => esc_html__( 'Enter password', 'bookit' ),
			'repeat_password_placeholder'   => esc_html__( 'Repeat password', 'bookit' ),
			'settings'                      => esc_html__( 'Settings', 'bookit' ),
			'sender_name'                   => esc_html__( 'Sender Name', 'bookit' ),
			'sender_email'                  => esc_html__( 'Sender Email', 'bookit' ),
			'no_user'                       => esc_html__( 'No user', 'bookit' ),
			'wp_user'                       => esc_html__( 'WP User', 'bookit' ),
			'new'                           => esc_html__( 'New', 'bookit' ),
			'clean_all_on_delete_title'     => esc_html__( 'Clean all on delete', 'bookit' ),
			'clean_all_on_delete_help'      => esc_html__( 'Removes all settings and tables from the database used for the plugin when uninstalling the plugin', 'bookit' ),
			'setup_settings'                => esc_html__( 'Setup settings', 'bookit' ),
			'pay_locally'                   => esc_html__( 'Pay Locally', 'bookit' ),
			'buy_now'                       => esc_html__( 'Buy Now', 'bookit' ),
			'no'                            => esc_html__( 'No', 'bookit' ),
			'addon_feature'                 => esc_html__( 'This feature is part of "%s" Add-on!', 'bookit' ),
			/** Settings page */
			'calendar_view'                 => esc_html__( 'Calendar View', 'bookit' ),
			'email_settings'                => esc_html__( 'Email Settings', 'bookit' ),
			'theme_and_style'               => esc_html__( 'Template & Style', 'bookit' ),
			'calendar_theme'                => esc_html__( 'Calendar Template', 'bookit' ),
			'theme'                         => esc_html__( 'Template', 'bookit' ),
			'calendar_view_default'         => esc_html__( 'Default', 'bookit' ),
			'calendar_view_step_by_step'    => esc_html__( 'Step by step', 'bookit' ),
			'booking_type'                  => esc_html__( 'Booking Type', 'bookit' ),
			'booking_type_registered'       => esc_html__( 'Registered Booking', 'bookit' ),
			'booking_type_guest'            => esc_html__( 'Guest Booking', 'bookit' ),
			'use_custom_colors'             => esc_html__( 'Use Custom Colors', 'bookit' ),
			'hide_calendar_header_titles'   => esc_html__( 'Hide Calendar Header Titles', 'bookit' ),
			'hide_from_for_service_price'   => esc_html__( 'Disable the word “From:” for service with one price', 'bookit' ),

			/** appointment and payment statuses */
			'pending'                       => esc_html__( 'pending', 'bookit' ),
			'approved'                      => esc_html__( 'approved', 'bookit' ),
			'cancelled'                     => esc_html__( 'cancelled', 'bookit' ),
			'complete'                      => esc_html__( 'complete', 'bookit' ),
			'locally'                       => esc_html__( 'locally', 'bookit' ),
			'free'                          => esc_html__( 'free', 'bookit' ),
			'rejected'                      => esc_html__( 'rejected', 'bookit' ),
			'activate_addon'                => esc_html__( 'Please activate addon', 'bookit' ),
			'appointment_payment_notice'    => esc_html__( 'Please choose the customer, staff and service first', 'bookit' ),
			'copy'                          => esc_html__( 'Copy', 'bookit' ),
			'url_copied'                    => esc_html__( 'link has been copied', 'bookit' ),
			'load'                          => esc_html__( 'Load', 'bookit' ),
			'new_appointment'               => esc_html__( 'New Appointment', 'bookit' ),
			'appointment_status_change'     => esc_html__( 'Appointment Status Changed', 'bookit' ),
			'payment_complete'              => esc_html__( 'Payment Complete', 'bookit' ),
			'appointment_updated'           => esc_html__( 'Appointment Updated', 'bookit' ),
			'delete_appointment'            => esc_html__( 'Delete Appointment', 'bookit' ),
			'customer_notification'         => esc_html__( 'Customer Notification', 'bookit' ),
			'admin_notification'            => esc_html__( 'Admin Notification', 'bookit' ),
			'staff_notification'            => esc_html__( 'Staff Notification', 'bookit' ),
			'day'                           => esc_html__( 'Day', 'bookit' ),
			'week'                          => esc_html__( 'Week', 'bookit' ),
			'month'                         => esc_html__( 'Month', 'bookit' ),
			'today'                         => esc_html__( 'Today', 'bookit' ),
			'feedback'                      => esc_html__( 'Feedback', 'bookit' ),
			'documentation'                 => esc_html__( 'Documentation', 'bookit' ),
			'no_category'                   => esc_html__( 'No Category', 'bookit' ),
			'day_off'                       => esc_html__( 'Day Off', 'bookit' ),
			'general'                       => esc_html__( 'General', 'bookit' ),
			'currency'                      => esc_html__( 'Currency', 'bookit' ),
			'payments'                      => esc_html__( 'Payments', 'bookit' ),
			'email_templates'               => esc_html__( 'Email Templates', 'bookit' ),
			'shortcode_generator'           => esc_html__( 'Shortcode Generator', 'bookit' ),
			'import_export'                 => esc_html__( 'Import/Export', 'bookit' ),
			'main_settings'                 => esc_html__( 'Main settings', 'bookit' ),
			'base_color'                    => esc_html__( 'Base Color', 'bookit' ),
			'base_bg_color'                 => esc_html__( 'Base Background Color', 'bookit' ),
			'highligth_color'               => esc_html__( 'Highlight Color', 'bookit' ),
			'white_color'                   => esc_html__( 'White Color', 'bookit' ),
			'dark_color'                    => esc_html__( 'Dark Color', 'bookit' ),
			'currency_symbol'               => esc_html__( 'Currency symbol', 'bookit' ),
			'currency_position'             => esc_html__( 'Currency Position', 'bookit' ),
			'left'                          => esc_html__( 'Left', 'bookit' ),
			'right'                         => esc_html__( 'Right', 'bookit' ),
			'thousands_separator'           => esc_html__( 'Thousands Separator', 'bookit' ),
			'decimal_separator'             => esc_html__( 'Decimals Separator', 'bookit' ),
			'number_of_decimals'            => esc_html__( 'Number of decimals', 'bookit' ),
			'enabled'                       => esc_html__( 'Enabled', 'bookit' ),
			'subject'                       => esc_html__( 'Subject', 'bookit' ),
			'message_body'                  => esc_html__( 'Message Body', 'bookit' ),
			'recipients'                    => esc_html__( 'Recipients', 'bookit' ),
			'import_demo_content'           => esc_html__( 'Import demo content', 'bookit' ),
			'import_demo_content_info'      => esc_html__( 'Import the demo content for understanding how it works', 'bookit' ),
			'demo_import'                   => esc_html__( 'Demo Import', 'bookit' ),
			'importing'                     => esc_html__( 'Importing', 'bookit' ),
			'import'                        => esc_html__( 'Import', 'bookit' ),
			'import_info'                   => esc_html__( 'Import Bookit data previously saved as in JSON format as txt file', 'bookit' ),
			'select_file'                   => esc_html__( 'Select file', 'bookit' ),
			'import_json'                   => esc_html__( 'Import Json', 'bookit' ),
			'export'                        => esc_html__( 'Export', 'bookit' ),
			'export_info'                   => esc_html__( 'Export the current Services, Staff, Categories and Settings in JSON format as txt file', 'bookit' ),
			'export_json'                   => esc_html__( 'Export Json', 'bookit' ),
			'export_appointments_info'      => esc_html__( 'Export the current Appointments as Excel file', 'bookit' ),
			'export_excel'                  => esc_html__( 'Export Excel', 'bookit' ),
			'15_minutes'                    => esc_html__( '15 minutes', 'bookit' ),
			'20_minutes'                    => esc_html__( '20 minutes', 'bookit' ),
			'30_minutes'                    => esc_html__( '30 minutes', 'bookit' ),
			'45_minutes'                    => esc_html__( '45 minutes', 'bookit' ),
			'1_hour'                        => esc_html__( '1 hour', 'bookit' ),
			'to'                            => esc_html__( 'to', 'bookit' ),
			'no_break'                      => esc_html__( 'No Break', 'bookit' ),
			'add_icon'                      => esc_html__( 'Add Icon', 'bookit' ),
			'replace_icon'                  => esc_html__( 'Replace Icon', 'bookit' ),
			'remove_icon'                   => esc_html__( 'Remove Icon', 'bookit' ),
			'reset'                         => esc_html__( 'Reset', 'bookit' ),
			'import_completed'              => esc_html__( 'Import completed', 'bookit' ),
			'import_started'                => esc_html__( 'Import Data started', 'bookit' ),
			'google_calendar'               => esc_html_x( 'Google Calendar', 'Name for the Google Calendar setting tab', 'bookit' ),
		);

		return $translations;
	}

	//TODO get by loop
	public static function get_addon_translations() {
		$translations = array();
		$translations = array_merge( self::google_calendar_translations(), self::payments_translations() );
		return $translations;
	}

	public static function google_calendar_translations() {
		$google_data_link = self::get_google_data_link();

		$google_app_data = sprintf(
		    esc_html_x(
		        'BookIt for Google Calendar will ensure that any data received from Google APIs is handled in accordance with the %1$sGoogle API Services User Data Policy%2$s, including compliance with the Limited Use requirements.',
		        'BookIt for Google calendar data disclaimer text.',
		        'bookit'
		    ),
		    '<a href="' . esc_url( $google_data_link ) . '" target="_blank">',
		    '</a>'
		);

		$translations = array(
			'main_settings'            => esc_html__( 'Main settings', 'bookit' ),
			'enabled'                  => esc_html__( 'Enabled', 'bookit' ),
			'client_id'                => esc_html__( 'Client ID', 'bookit' ),
			'client_id_help'           => esc_html__( 'Client ID, derived from the Google Developers Console Credentials', 'bookit' ),
			'client_secret'            => esc_html__( 'Client Secret', 'bookit' ),
			'client_secret_help'       => esc_html__( 'Client Secret, derived from the Google Developers Console Credentials', 'bookit' ),
			'redirect_uri'             => esc_html__( 'Redirect URI', 'bookit' ),
			'send_pending'             => esc_html__( 'Syncronization with pending appointments', 'bookit' ),
			'send_pending_help'        => esc_html__( "New unapproved appointments created by customers will be also added to the staff's Google Calendar.", 'bookit' ),
			'rm_busy_slots_title'      => esc_html__( 'Consider Google calendar busy slots for customers', 'bookit' ),
			'rm_busy_slots_help'       => esc_html__( "Google Calendar time slots will be checked before displaying free time slots for new appointments. Customers cannot make appointments if the time slot is busy in the staff's Google Сalendar account.", 'bookit' ),
			'customer_as_attendees'    => esc_html__( 'Add customer as event attendees', 'bookit' ),
			'customer_attendees_help'  => esc_html__( 'By creating the event on Google Calendar, the email of the client will be added to the event as an attendee.', 'bookit' ),
			'export_appointments'      => esc_html__( 'Export appointments', 'bookit' ),
			'export_appointments_help' => esc_html__( 'Synchronization of all the upcoming events with Google Calendar of the staff.', 'bookit' ),
			'template'                 => esc_html__( 'Template', 'bookit' ),
			'body'                     => esc_html__( 'Body', 'bookit' ),
			'connect'                  => esc_html__( 'Connect', 'bookit' ),
			're_connect'               => esc_html__( 'Re-Connect', 'bookit' ),
			'connected'                => esc_html__( 'Connected', 'bookit' ),
			'disconnect'               => esc_html__( 'Disconnect', 'bookit' ),
			'gc_busy_event_info'       => esc_html__( 'On selected period exist google calendar event', 'bookit' ),
			'timezone_label'           => esc_html_x( 'Timezone', 'Label for timezone setting.', 'bookit' ),
			'timezone_select'          => esc_html_x( 'Select a Timezone', 'Label for timezone select placeholder.', 'bookit' ),
			'google_app_data'          => $google_app_data,
		);

		return $translations;
	}

	public static function payments_translations() {
		$translations = array(
			'paypal_ipn_setup'         => esc_html__( 'PayPal IPN Setup', 'bookit' ),
			'paypal_ipn_pre_link'      => esc_html__( 'You need to use this URL for your', 'bookit' ),
			'paypal_ipn_link_txt'      => esc_html__( 'IPN Listener Settings:', 'bookit' ),
			'paypal_email'             => esc_html__( 'PayPal Email', 'bookit' ),
			'paypal_mode'              => esc_html__( 'PayPal Mode', 'bookit' ),
			'paypal_mode_live'         => esc_html__( 'Live', 'bookit' ),
			'paypal_mode_sandbox'      => esc_html__( 'Sandbox', 'bookit' ),
			'paypal_validation_failed' => esc_html_x( 'PayPal settings validation failed. Please check the fields in the Payments tab.', 'Validation message for saving PayPal legacy settings.', 'bookit' ),
			'paypal_email_required'    => esc_html_x( 'PayPal email is required', 'Validation message for saving PayPal legacy settings.', 'bookit' ),
			'paypal_mode_required'     => esc_html_x( 'PayPal mode is required', 'Validation message for saving PayPal legacy settings.', 'bookit' ),
			'stripe_publish_key'       => esc_html__( 'Stripe Publish Key', 'bookit' ),
			'stripe_secret_key'        => esc_html__( 'Stripe Secret Key', 'bookit' ),
			'woocommerce_product'      => esc_html__( 'WooCommerce Product', 'bookit' ),
			'woocommerce_title'        => esc_html__( 'WooCommerce Custom Title', 'bookit' ),
			'woocommerce_icon'         => esc_html__( 'WooCommerce Custom Icon', 'bookit' ),
			'woocommerce_icon_error'   => esc_html__( 'WooCommerce Custom Icon must be image', 'bookit' ),
			'choose_icon'              => esc_html__( 'Choose icon', 'bookit' ),

			// Stripe Connect.
			'stripe_connect_name'                        => esc_html_x( 'Stripe Connect', 'Stripe Connect singular name.', 'bookit' ),
			'stripe_connect_title'                       => esc_html_x( 'Accept online payments with Stripe!', 'Stripe Connect intro title.', 'bookit' ),
			'stripe_connect_start_selling'               => esc_html_x( 'Start having customers pay for appointments today with Stripe integration for Bookit Stripe Connect.', 'Stripe Connect intro text.', 'bookit' ),
			'stripe_connect_get_connected'               => esc_html_x( 'Get connected with', 'Stripe Connect connect account link text.', 'bookit' ),
			'stripe_connect_get_connected_name'          => esc_html_x( 'Stripe', 'Stripe Connect connect account link text service name.', 'bookit' ),
			'stripe_connect_get_troubleshoot_help'       => esc_html_x( 'Get troubleshooting help', 'Stripe Connect intro text for button to authorize.', 'bookit' ),
			'stripe_connect_marketing_1'                 => esc_html_x( 'Credit, debit card payments and more!', 'Stripe Connect marketing information line 1.', 'bookit' ),
			'stripe_connect_marketing_2'                 => esc_html_x( 'Easy, streamlined connection', 'Stripe Connect marketing information line 2.', 'bookit' ),
			'stripe_connect_marketing_3'                 => esc_html_x( 'Accept payments from around the world', 'Stripe Connect marketing information line 3.', 'bookit' ),
			'stripe_connect_marketing_4'                 => esc_html_x( 'Supports 3D Secure payments', 'Stripe Connect marketing information line 4.', 'bookit' ),
			'stripe_connect_connected_title'             => esc_html_x( "You are now connected to Stripe! What's next?", 'Stripe Connect after connected title.', 'bookit' ),
			'stripe_connect_active_message'              => esc_html_x( 'Your connection is active, but Bookit Stripe Connect is set to test mode. While in test mode no live transactions are processed.', 'Stripe Connect is active message.', 'bookit' ),
			'stripe_connect_test_mode_label'             => esc_html_x( 'Enables Test Mode', 'Stripe Connect test mode label.', 'bookit' ),
			'stripe_connect_test_mode_description'       => esc_html_x( 'Enables Test mode for testing payments. Any payments made will be done on "sandbox" accounts.', 'Stripe Connect test mode message.', 'bookit' ),
			'stripe_connect_currency_header'             => esc_html_x( 'Currency', 'Stripe Connect currency matching header.', 'bookit' ),
			'stripe_connect_currency_text'               => esc_html_x( 'Be sure that your Stripe currency matches the currency you have configured for Bookit Stripe Connect, to avoid any issues or unexpected conversion fees.', 'Stripe Connect currency matching text.', 'bookit' ),
			'stripe_connect_payment_methods_header'      => esc_html_x( 'Payment methods', 'Stripe Connect payment methods header.', 'bookit' ),
			'stripe_connect_payment_methods_text'        => esc_html_x( 'You will have to confirm that the payments methods you have selected to sell appointments are enabled on the', 'Stripe Connect payment methods text.', 'bookit' ),
			'stripe_connect_payment_methods_link'        => esc_html_x( 'Stripe payment methods section', 'Stripe Connect payment methods link text.', 'bookit' ),
			'stripe_connect_webhooks_header'             => esc_html_x( 'Webhooks', 'Stripe Connect webhooks header.', 'bookit' ),
			'stripe_connect_webhooks_text'               => esc_html_x( 'Your webhook is now automatically configured! A webhook is required for appointment payments to be marked as complete for some payment methods on your Stripe gateway for your Bookit site. If you would like to read more on how we set up the webhook or if you are facing issues with it there are resources', 'Stripe Connect webhooks text.', 'bookit' ),
			'stripe_connect_webhooks_link'               => esc_html_x( 'here', 'Stripe Connect webhooks link text.', 'bookit' ),
			'stripe_connect_pci_compliance_header'       => esc_html_x( 'PCI Compliance', 'Stripe Connect PCI Compliance header.', 'bookit' ),
			'stripe_connect_pci_compliance_text'         => esc_html_x( 'Stripe allows you to accept credit or debit cards directly on your website. Because of this, your site needs to maintain', 'Stripe Connect PCI Compliance text.', 'bookit' ),
			'stripe_connect_pci_compliance_link'         => esc_html_x( 'PCI-DSS compliance', 'Stripe Connect PCI Compliance link text.', 'bookit' ),
			'stripe_connect_connected_close_button_text' => esc_html_x( 'Got it, thanks!', 'Stripe Connect connected message button close text.', 'bookit' ),
			'stripe_connect_connected_as'                => esc_html_x( 'Connected as', 'Stripe Connect connected as id message.', 'bookit' ),
			'stripe_connect_disconnect_button_text'      => esc_html_x( 'Disconnect', 'Stripe Connect connected disconnect button text.', 'bookit' ),
			'stripe_connect_payment_status_title'        => esc_html_x( 'Payments status', 'Stripe Connect connected payment_status title.', 'bookit' ),
			'stripe_connect_currency_title'              => esc_html_x( 'Stripe currency', 'Stripe Connect currency title.', 'bookit' ),
			'stripe_connect_currency_message'            => esc_html_x( 'Please be sure to enable all the payment methods you want to use for this currency on your', 'Stripe Connect currency information message title.', 'bookit' ),
			// Translators: %1$s is the Stripe currency, %2$s is the Bookit currency symbol.
			'stripe_connect_currency_not_match_message'  => esc_html_x( 'Your Stripe account is set to %1$s, but your site is set to %2$s. Using different currencies for Bookit and Stripe may not be supported by all payment methods available in %2$s, and may result in exchange rates and conversions from %2$s to %1$s being handled by Stripe.', 'Stripe Connect currency not matching message.', 'bookit' ),

			'stripe_connect_currency_message_link_text' => esc_html_x( 'Stripe dashboard', 'Stripe Connect dashboard link text.', 'bookit' ),
			'stripe_connect_settings_link_text'         => esc_html_x( 'Edit Your Stripe Settings', 'Stripe Connect settings link text.', 'bookit' ),
			'stripe_connect_configured_link_text'       => esc_html_x( 'Learn more about configuring Stripe payments', 'Stripe Connect settings link text.', 'bookit' ),
		);
		return $translations;
	}

	/**
	 * Admin Translation Strings
	 * @return array
	 */
	public static function get_addons_page_translations() {
		$translations = array(
			'addons_title'       => esc_html__( 'Add-ons', 'bookit' ),
			'addons_description' => esc_html__( 'This is a universal tool that works with any WordPress theme and performs greatly on any business website.', 'bookit' ),
			'version'            => esc_html__( 'Version', 'bookit' ),
			'buy'                => esc_html__( 'Buy', 'bookit' ),
			'view_changelog'     => esc_html__( 'View Changelog', 'bookit' ),
			'popular'            => esc_html__( 'Popular', 'bookit' ),
			'license_purchased'  => esc_html__( '%s %s license already purchased', 'bookit' ), // phpcs:ignore WordPress.WP.I18n.UnorderedPlaceholdersText
			'upgrade'            => esc_html__( 'Upgrade', 'bookit' ),
			'active_license'     => esc_html__( 'Lifetime license %s already active', 'bookit' ),
			'active'             => esc_html__( 'Active', 'bookit' ),
			'per_year'           => esc_html__( 'per year', 'bookit' ),
			'learn_more'         => esc_html__( 'Learn More', 'bookit' ),
			'lifetime'           => esc_html__( 'lifetime', 'bookit' ),
		);
		return $translations;
	}

	/**
	 * Get Google Data Link
	 *
	 * @since 2.5.0
	 *
	 * @return string Filtered Google Data Link
	 */
	public static function get_google_data_link(): string {
	    $google_data_link = 'https://developers.google.com/terms/api-services-user-data-policy#additional_requirements_for_specific_api_scopes';

	    /**
	     * Filter the Google Data Link.
	     *
	     * @since 2.5.0
	     *
	     * @param string $google_data_link The Google Data Link URL.
	     */
	    return apply_filters( 'bookit_filter_google_data_link', $google_data_link );
	}
}

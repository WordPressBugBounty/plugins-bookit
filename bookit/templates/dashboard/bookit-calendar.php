<div id="bookit-dashboard-app" class="admin">
	<h2><?php echo esc_html__( 'Calendar', 'bookit' ); ?></h2>
	<bookit
		:categories="<?php echo bookit_vue_prop( $categories ); ?>"
		:filter_services="<?php echo bookit_vue_prop( $filter_services ); ?>"
		:services="<?php echo bookit_vue_prop( $services ); ?>"
		:staff="<?php echo bookit_vue_prop( $staff ); ?>"
		:customers="<?php echo bookit_vue_prop( $customers ); ?>"
		language="<?php echo esc_attr( $language ); ?>"
		:settings="<?php echo bookit_vue_prop( $settings ); ?>"
		:time_slot_list="<?php echo bookit_vue_prop( $time_slot_list ); ?>"
		:payment_methods="<?php echo bookit_vue_prop( $payment_methods ); ?>"
		:appointment_statuses="<?php echo bookit_vue_prop( $appointment_statuses ); ?>"
		:autorefresh_list="<?php echo bookit_vue_prop( $autorefresh_list ); ?>"
	></bookit>
</div>

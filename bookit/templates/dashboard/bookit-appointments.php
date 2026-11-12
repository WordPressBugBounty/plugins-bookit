<div id="bookit-dashboard-app">
	<h2><?php echo esc_html( $page ); ?></h2>
	<bookit-appointments
		:services="<?php echo bookit_vue_prop( $services ); ?>"
		:staff="<?php echo bookit_vue_prop( $staff ); ?>"
		:customers="<?php echo bookit_vue_prop( $customers ); ?>"
		:settings="<?php echo bookit_vue_prop( $settings ); ?>"
		time_format="<?php echo esc_attr( $time_format ); ?>"
		:time_slot_list="<?php echo bookit_vue_prop( $time_slot_list ); ?>"
		:payment_methods="<?php echo bookit_vue_prop( $payment_methods ); ?>"
		:appointment_statuses="<?php echo bookit_vue_prop( $appointment_statuses ); ?>"
	></bookit-appointments>
</div>

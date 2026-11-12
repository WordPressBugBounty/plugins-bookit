<div id="bookit-dashboard-app">
	<h2><?php esc_html_e( 'Settings', 'bookit' ); ?></h2>
	<bookit-settings
		:addons="<?php echo bookit_vue_prop( $addons ); ?>"
		:settings="<?php echo bookit_vue_prop( $settings ); ?>"
		:gateways="<?php echo bookit_vue_prop( $gateways ); ?>"
		:pro_disabled="<?php echo esc_attr( $pro_disabled ); ?>"
		:pro_installed="<?php echo esc_attr( $pro_installed ); ?>"
		:woocommerce_products="<?php echo bookit_vue_prop( $woocommerce_products ); ?>"
		:woocommerce_enabled="<?php echo esc_attr( $woocommerce_enabled ); ?>"
		:categories="<?php echo bookit_vue_prop( $categories ); ?>"
		:services="<?php echo bookit_vue_prop( $services ); ?>"
		:staff="<?php echo bookit_vue_prop( $staff ); ?>"
		:currencies="<?php echo bookit_vue_prop( $currencies ); ?>"
		:time_slot_options="<?php echo bookit_vue_prop( $time_slot_options ); ?>"
		:calendar_view_options="<?php echo bookit_vue_prop( $calendar_view_options ); ?>"
	></bookit-settings>
</div>

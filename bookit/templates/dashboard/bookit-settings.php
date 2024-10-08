<div id="bookit-dashboard-app">
	<h2><?php esc_html_e( 'Settings', 'bookit' ); ?></h2>
	<bookit-settings
		:addons="<?php echo esc_attr( json_encode( $addons, JSON_UNESCAPED_UNICODE ) ); ?>"
		:settings="<?php echo esc_attr( json_encode( $settings, JSON_UNESCAPED_UNICODE ) ); ?>"
		:gateways="<?php echo esc_attr( json_encode( $gateways, JSON_UNESCAPED_UNICODE ) ); ?>"
		:pro_disabled="<?php echo esc_attr( $pro_disabled ); ?>"
		:pro_installed="<?php echo esc_attr( $pro_installed ); ?>"
		:woocommerce_products="<?php echo esc_attr( json_encode( $woocommerce_products, JSON_UNESCAPED_UNICODE ) ); ?>"
		:woocommerce_enabled="<?php echo esc_attr( $woocommerce_enabled ); ?>"
		:categories="<?php echo esc_attr( json_encode( $categories, JSON_UNESCAPED_UNICODE ) ); ?>"
		:services="<?php echo esc_attr( json_encode( $services, JSON_UNESCAPED_UNICODE ) ); ?>"
		:staff="<?php echo esc_attr( json_encode( $staff, JSON_UNESCAPED_UNICODE ) ); ?>"
		:currencies="<?php echo esc_attr( json_encode( $currencies, JSON_UNESCAPED_UNICODE ) ); ?>"
		:time_slot_options="<?php echo esc_attr( json_encode( $time_slot_options, JSON_UNESCAPED_UNICODE ) ); ?>"
		:calendar_view_options="<?php echo esc_attr( json_encode( $calendar_view_options, JSON_UNESCAPED_UNICODE ) ); ?>"
	></bookit-settings>
</div>

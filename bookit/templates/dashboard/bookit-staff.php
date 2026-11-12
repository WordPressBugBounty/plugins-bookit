<div id="bookit-dashboard-app">
	<h2><?php echo esc_html( $page ); ?></h2>
	<bookit-staff
		:addons="<?php echo bookit_vue_prop( $addons ); ?>"
		:answer="<?php echo bookit_vue_prop( $answer ); ?>"
		:staff="<?php echo bookit_vue_prop( $staff ); ?>"
		:services="<?php echo bookit_vue_prop( $services ); ?>"
		:wp_users="<?php echo bookit_vue_prop( $wp_users ); ?>"
	></bookit-staff>
</div>

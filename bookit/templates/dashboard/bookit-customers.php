<div id="bookit-dashboard-app">
	<h2><?php echo esc_html( $page ); ?></h2>
	<bookit-customers
		:wp_users="<?php echo bookit_vue_prop( $wp_users ); ?>"
	></bookit-customers>
</div>

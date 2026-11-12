<div id="bookit-dashboard-app">
	<h2><?php esc_html_e( 'Services', 'bookit' ); ?></h2>
	<bookit-services
		:rows="<?php echo bookit_vue_prop( $services ); ?>"
		:categories="<?php echo bookit_vue_prop( $categories ); ?>"
	></bookit-services>
</div>

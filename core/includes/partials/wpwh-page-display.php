<?php
/**
 * Main Template
 */

$heading = '';
$current_content = '';
/**
 * Filter the menu tab items. You can extend here your very own tabs
 * as well.
 * Our default endpoints are declared in
 * core/includes/classes/class-wp-webhooks-run.php
 */
$menu_endpoints = apply_filters( 'wpwh/admin/settings/menu_data', array() );

if( isset( $_GET['wpwhvrs'] ) && $_GET['wpwhvrs'] != 'home' ){

    $active_val = sanitize_title( $_GET['wpwhvrs'] );
    /**
     * Filter the global plugin admin capability again to create an
     * independent capability possibility system for the element settings
     */
    if( current_user_can( apply_filters( 'wpwh/admin/settings/menu/page_capability', WPWH()->settings->get_admin_cap( 'wpwh-page-settings' ), $active_val ) ) ){
        /**
         * The following hook gives you the possibility to
         * output custom content on the specified page with the filter
         *
         * @hook  wpwh/admin/settings/menu_data
         */

        //Buffer for avoiding errors
        ob_start();
            do_action( 'wpwh/admin/settings/menu/place_content', $active_val );
        $current_content = ob_get_clean();

        /**
         * Possibility to filter the content after
         * creating its output
         */
        $current_content = apply_filters( 'wpwh/admin/settings/menu/filter_content', $current_content, $active_val );
    }

} else {
	$active_val      = 'home';

	ob_start();
	do_action( 'wpwh/admin/settings/menu/place_content', $active_val );
	$current_content = ob_get_clean();

	$current_content = ! empty( $current_content ) ? $current_content : WPWH()->helpers->translate( 'Welcome to WP Webhook Pro! Currently we are not able to show you the newest informations.', 'admin-backend' ) ;
}

if( is_array( $menu_endpoints ) ){
	foreach( $menu_endpoints as $hook_name => $title ){
		/**
		 * Filter the global plugin admin capability again to create an
		 * independend capability possibility system for the element settings
		 */
		if( current_user_can( apply_filters( 'wpwh/admin/settings/menu/page_capability', WPWH()->settings->get_admin_cap( 'wpwh-page-settings' ), $active_val ) ) ){

			/**
			 * Hook for Filterinng the title of a specified plugin file
			 */
			$title = apply_filters( 'wpwh/admin/settings/element/filter_title', $title, $hook_name );

			if( $active_val == $hook_name ){
				$heading .= '<a class="ironikus-setting-single-tab active ' . $hook_name . '">' . $title . '</a> | ';
			} else {
				$heading .= '<a class="ironikus-setting-single-tab ' . $hook_name . '" href="?page=' . $this->page_name . '&wpwhvrs=' . $hook_name . '">' . $title . '</a> | ';
			}
		}

	}
} else {
	$heading = '<a class="ironikus-setting-single-tab" href="?page=' . $this->page_name . '">' . WPWH()->helpers->translate( $subs_origin['home'], 'admin-backend' ) . '</a>';
}

?>
<div class="wrap ironikus-wrap">

    <h1><?php echo WPWH()->helpers->translate( $this->page_title, 'admin-add-page-title' ); ?></h1>
    <div class='wp-webhooks-action-links'>
	    <?php echo trim( $heading, ' | ' ); ?>
    </div>
    <hr/>

	<div class="ironikus-setting-content">
        <?php echo $current_content; ?>
    </div>
</div>
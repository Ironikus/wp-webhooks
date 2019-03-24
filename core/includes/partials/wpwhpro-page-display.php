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
 * core/includes/classes/class-wp-webhooks-pro-run.php
 */
$menu_endpoints = apply_filters( 'wpwhpro/admin/settings/menu_data', array() );

if( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] != 'home' ){

    $active_val = sanitize_title( $_GET['wpwhprovrs'] );
    /**
     * Filter the global plugin admin capability again to create an
     * independent capability possibility system for the element settings
     */
    if( current_user_can( apply_filters( 'wpwhpro/admin/settings/menu/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-settings' ), $active_val ) ) ){
        /**
         * The following hook gives you the possibility to
         * output custom content on the specified page with the filter
         *
         * @hook  wpwhpro/admin/settings/menu_data
         */

        //Buffer for avoiding errors
        ob_start();
            do_action( 'wpwhpro/admin/settings/menu/place_content', $active_val );
        $current_content = ob_get_clean();

        /**
         * Possibility to filter the content after
         * creating its output
         */
        $current_content = apply_filters( 'wpwhpro/admin/settings/menu/filter_content', $current_content, $active_val );
    }

} else {
	$active_val      = 'home';

	ob_start();
	do_action( 'wpwhpro/admin/settings/menu/place_content', $active_val );
	$current_content = ob_get_clean();

	$current_content = ! empty( $current_content ) ? $current_content : WPWHPRO()->helpers->translate( 'Welcome to WP Webhook! Currently we are not able to show you the newest informations.', 'admin-backend' ) ;
}

if( is_array( $menu_endpoints ) ){
	foreach( $menu_endpoints as $hook_name => $title ){
		/**
		 * Filter the global plugin admin capability again to create an
		 * independend capability possibility system for the element settings
		 */
		if( current_user_can( apply_filters( 'wpwhpro/admin/settings/menu/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-settings' ), $active_val ) ) ){

			/**
			 * Hook for Filterinng the title of a specified plugin file
			 */
			$title = apply_filters( 'wpwhpro/admin/settings/element/filter_title', $title, $hook_name );

			if( $active_val == $hook_name ){
				$heading .= '<a class="ironikus-setting-single-tab active ' . $hook_name . '">' . $title . '</a> | ';
			} else {
				$heading .= '<a class="ironikus-setting-single-tab ' . $hook_name . '" href="?page=' . $this->page_name . '&wpwhprovrs=' . $hook_name . '">' . $title . '</a> | ';
			}
		}

	}
} else {
	$heading = '<a class="ironikus-setting-single-tab" href="?page=' . $this->page_name . '">' . WPWHPRO()->helpers->translate( $subs_origin['home'], 'admin-backend' ) . '</a>';
}

?>
<script src="https://static.landbot.io/landbot-widget/landbot-widget-1.0.0.js"></script>
<script>
    var myLandbotLivechat = new LandbotLivechat({
        index: 'https://landbot.io/u/H-142887-M4JIJTD1B15FB8PE/index.html?ncknme=<?php echo urlencode( WPWHPRO()->helpers->validate_local_tags( '%user_name%' ) ); ?>&admnurl=<?php echo urlencode( WPWHPRO()->helpers->validate_local_tags( '%admin_url%' ) ); ?>',
    });
</script>
<style>
    #wpfooter{
        display:none;
    }
</style>

<div class="wrap ironikus-wrap">

    <h1><?php echo WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-page-title' ); ?></h1>
    <div class='wp-webhooks-pro-action-links'>
	    <?php echo trim( $heading, ' | ' ); ?>
    </div>
    <hr/>

	<div class="ironikus-setting-content">
        <?php echo $current_content; ?>
    </div>
</div>
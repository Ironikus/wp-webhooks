<?php
/**
 * Main Template
 */

$heading = '';
$current_content = '';
$plugin_name = WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-page-title' );
$plugin_name = str_replace( 'Pro', '<span class="golden">Pro</span>', $plugin_name );

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

    /**
     * Possibility to filter the content after
     * creating its output
     */
    $current_content = apply_filters( 'wpwhpro/admin/settings/menu/filter_content', $current_content, $active_val );

    $current_content = WPWHPRO()->helpers->validate_local_tags( $current_content );
    
}

if( is_array( $menu_endpoints ) ){
	foreach( $menu_endpoints as $hook_name => $data ){

        if( is_array( $data ) ){

            if( isset( $data['label'] ) ){
                $title = $data['label'];
            } else {
                $title = WPWHPRO()->helpers->translate( 'Undefined', 'admin-backend' );
            }

        } else {
            $title = $data;
        }

		/**
		 * Filter the global plugin admin capability again to create an
		 * independend capability possibility system for the element settings
		 */
		if( current_user_can( apply_filters( 'wpwhpro/admin/settings/menu/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-settings' ), $active_val ) ) ){

			/**
			 * Hook for Filterinng the title of a specified plugin file
			 */
			$title = apply_filters( 'wpwhpro/admin/settings/element/filter_title', $title, $hook_name );

            $has_dropdown = false;

            if(
                (
                    is_array( $data ) 
                    && isset( $data['items'] ) 
                    && is_array( $data['items'] ) 
                    && count( $data['items'] ) > 1
                )
                ||
                (
                    is_array( $data ) 
                    && isset( $data['items'] ) 
                    && is_array( $data['items'] ) 
                    && count( $data['items'] ) <= 1
                    && ! isset( $data['items'][ $hook_name ] )
                )
            ){
                $has_dropdown = true;
            }

            $dd_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                <defs></defs>
                <path stroke="#264653" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7"></path>
            </svg>';

			if( $active_val == $hook_name ){
				$heading .= '<li class="active' . ( $has_dropdown ? ' has-dropdown' : '' ) . '"><a class="ironikus-setting-single-tab active ' . $hook_name . '">' . $title . ( $has_dropdown ? ' ' . $dd_svg : '' ) . '</a>';
			} else {
				$heading .= '<li' . ( $has_dropdown ? ' class="has-dropdown"' : '' ) . '><a class="ironikus-setting-single-tab ' . $hook_name . '" href="?page=' . $this->page_name . '&wpwhprovrs=' . $hook_name . '">' . $title . ( $has_dropdown ? ' ' . $dd_svg : '' ) . '</a>';
            }

            if( $has_dropdown ){

                $heading .= '<ul>';

                foreach( $data['items'] as $sub_menu_name => $sub_menu_title ){

                    if( $active_val == $sub_menu_name ){
                        $heading .= '<li class="active"><a class="ironikus-setting-single-tab active ' . $sub_menu_name . '">' . $sub_menu_title . '</a></li>';
                    } else {
                        $heading .= '<li><a class="ironikus-setting-single-tab ' . $sub_menu_name . '" href="?page=' . $this->page_name . '&wpwhprovrs=' . $sub_menu_name . '">' . $sub_menu_title . '</a></li>';
                    }

                }

                $heading .= '</ul>';

            }

            $heading .= '</li>';
		}

	}
} else {
	$heading = '<li class="active"><a class="ironikus-setting-single-tab" href="?page=' . $this->page_name . '">' . WPWHPRO()->helpers->translate( $subs_origin['home'], 'admin-backend' ) . '</a></li>';
}

?>

<div class="wpwh">
    <div class="wpwh-header">
        <div class="wpwh-container">
            <span class="wpwh-header__logo-text"><?php echo $this->page_title; ?></span>
        </div>
    </div>
    <!-- ./wpwh-header -->
    <div class="wpwh-menu">
        <div class="wpwh-container">
            <ul class="wpwh-menu__nav">
                <?php echo $heading; ?>
            </ul>
        </div>
    </div>
    <!-- /.wpwh-menu -->

    <div class="wpwh-main">
        <?php echo $current_content; ?>
    </div>

</div>
<!--
<div class="ironikus-wrap">
    <nav class="navbar ironikus-navbar navbar-expand-lg navbar-dark">
        <div class="navbar-brand"><?php #echo $plugin_name; ?></div>
        <button class="navbar-toggler justify-content-end" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <?php #echo $heading; ?>
            </ul>
        </div>
    </nav>

	<div class="ironikus-setting-content">
        <?php #echo $current_content; ?>
    </div>
</div> -->
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

<style>
    #wpfooter{
        display:none;
    }
</style>

<div class="wpwh">
    <div class="wpwh-header">
        <div class="wpwh-container">
            <svg width="208" height="20" viewBox="0 0 208 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.864 18L0.912 0.912H2.4L7.872 16.944H7.152L12.72 0.912L13.896 0.935999L19.416 16.944H18.696L24.192 0.912H25.68L19.728 18H18.432L13.032 2.544H13.536L8.136 18H6.864ZM28.8336 18V0.912H35.3856C37.0976 0.912 38.4416 1.328 39.4176 2.16C40.3936 2.992 40.8816 4.136 40.8816 5.592C40.8816 7.048 40.3936 8.2 39.4176 9.048C38.4416 9.896 37.0976 10.32 35.3856 10.32H30.2976V18H28.8336ZM30.2976 9.024H35.2896C36.6496 9.024 37.6656 8.728 38.3376 8.136C39.0256 7.544 39.3696 6.696 39.3696 5.592C39.3696 4.504 39.0256 3.664 38.3376 3.072C37.6656 2.48 36.6496 2.184 35.2896 2.184H30.2976V9.024ZM55.0281 18L49.0761 0.912H50.5641L56.0361 16.944H55.3161L60.8841 0.912L62.0601 0.935999L67.5801 16.944H66.8601L72.3561 0.912H73.8441L67.8921 18H66.5961L61.1961 2.544H61.7001L56.3001 18H55.0281ZM85.0697 16.512C84.5577 17.024 83.8937 17.432 83.0777 17.736C82.2617 18.024 81.4217 18.168 80.5577 18.168C79.3417 18.168 78.2937 17.92 77.4137 17.424C76.5337 16.928 75.8537 16.216 75.3737 15.288C74.8937 14.36 74.6537 13.248 74.6537 11.952C74.6537 10.704 74.8857 9.608 75.3497 8.664C75.8297 7.72 76.4937 6.992 77.3417 6.48C78.1897 5.952 79.1657 5.688 80.2697 5.688C81.3097 5.688 82.2057 5.92 82.9577 6.384C83.7097 6.832 84.2857 7.488 84.6857 8.352C85.0857 9.216 85.2857 10.264 85.2857 11.496V11.808H75.9497L75.9257 10.704H84.4937L83.9417 11.64C83.9897 10.104 83.6937 8.928 83.0537 8.112C82.4297 7.296 81.5017 6.888 80.2697 6.888C79.0057 6.888 78.0057 7.336 77.2697 8.232C76.5497 9.112 76.1897 10.328 76.1897 11.88C76.1897 13.512 76.5577 14.752 77.2937 15.6C78.0457 16.448 79.1257 16.872 80.5337 16.872C81.2697 16.872 81.9577 16.752 82.5977 16.512C83.2537 16.272 83.8937 15.896 84.5177 15.384L85.0697 16.512ZM88.4295 18V0.335999H89.8935V9.144L89.6295 8.952C89.9015 7.912 90.4215 7.112 91.1895 6.552C91.9735 5.976 92.9255 5.688 94.0455 5.688C95.1175 5.688 96.0535 5.944 96.8535 6.456C97.6535 6.952 98.2775 7.664 98.7255 8.592C99.1895 9.52 99.4215 10.616 99.4215 11.88C99.4215 13.144 99.1895 14.248 98.7255 15.192C98.2775 16.136 97.6455 16.872 96.8295 17.4C96.0295 17.912 95.1015 18.168 94.0455 18.168C92.9255 18.168 91.9735 17.888 91.1895 17.328C90.4215 16.752 89.9015 15.936 89.6295 14.88L89.8935 14.688V18H88.4295ZM93.8775 16.872C95.1255 16.872 96.1015 16.432 96.8055 15.552C97.5095 14.672 97.8615 13.448 97.8615 11.88C97.8615 10.312 97.5095 9.104 96.8055 8.256C96.1015 7.408 95.1255 6.984 93.8775 6.984C92.5975 6.984 91.6135 7.416 90.9255 8.28C90.2375 9.128 89.8935 10.344 89.8935 11.928C89.8935 13.496 90.2375 14.712 90.9255 15.576C91.6135 16.44 92.5975 16.872 93.8775 16.872ZM102.609 18V0.335999H104.073V8.4L103.785 8.736C104.089 7.728 104.641 6.968 105.441 6.456C106.241 5.944 107.161 5.688 108.201 5.688C111.097 5.688 112.545 7.24 112.545 10.344V18H111.081V10.44C111.081 9.24 110.841 8.36 110.361 7.8C109.881 7.24 109.113 6.96 108.057 6.96C106.857 6.96 105.889 7.328 105.153 8.064C104.433 8.8 104.073 9.792 104.073 11.04V18H102.609ZM121.213 18.168C120.109 18.168 119.141 17.912 118.309 17.4C117.493 16.888 116.853 16.168 116.389 15.24C115.925 14.296 115.693 13.184 115.693 11.904C115.693 10.656 115.925 9.568 116.389 8.64C116.853 7.696 117.493 6.968 118.309 6.456C119.141 5.944 120.109 5.688 121.213 5.688C122.333 5.688 123.301 5.944 124.117 6.456C124.949 6.968 125.589 7.696 126.037 8.64C126.501 9.568 126.733 10.656 126.733 11.904C126.733 13.184 126.501 14.296 126.037 15.24C125.589 16.168 124.949 16.888 124.117 17.4C123.301 17.912 122.333 18.168 121.213 18.168ZM121.213 16.872C122.493 16.872 123.477 16.448 124.165 15.6C124.869 14.752 125.221 13.528 125.221 11.928C125.221 10.36 124.869 9.144 124.165 8.28C123.461 7.416 122.485 6.984 121.237 6.984C119.973 6.984 118.981 7.416 118.261 8.28C117.557 9.144 117.205 10.36 117.205 11.928C117.205 13.528 117.549 14.752 118.237 15.6C118.941 16.448 119.933 16.872 121.213 16.872ZM134.689 18.168C133.585 18.168 132.617 17.912 131.785 17.4C130.969 16.888 130.329 16.168 129.865 15.24C129.401 14.296 129.169 13.184 129.169 11.904C129.169 10.656 129.401 9.568 129.865 8.64C130.329 7.696 130.969 6.968 131.785 6.456C132.617 5.944 133.585 5.688 134.689 5.688C135.809 5.688 136.777 5.944 137.593 6.456C138.425 6.968 139.065 7.696 139.513 8.64C139.977 9.568 140.209 10.656 140.209 11.904C140.209 13.184 139.977 14.296 139.513 15.24C139.065 16.168 138.425 16.888 137.593 17.4C136.777 17.912 135.809 18.168 134.689 18.168ZM134.689 16.872C135.969 16.872 136.953 16.448 137.641 15.6C138.345 14.752 138.697 13.528 138.697 11.928C138.697 10.36 138.345 9.144 137.641 8.28C136.937 7.416 135.961 6.984 134.713 6.984C133.449 6.984 132.457 7.416 131.737 8.28C131.033 9.144 130.681 10.36 130.681 11.928C130.681 13.528 131.025 14.752 131.713 15.6C132.417 16.448 133.409 16.872 134.689 16.872ZM151.478 18L144.566 11.856L150.878 5.976H152.822L145.886 12.312L145.934 11.232L153.47 18H151.478ZM143.414 18V0.335999H144.878V18H143.414ZM159.045 18.168C157.093 18.168 155.549 17.632 154.413 16.56L154.965 15.432C155.605 15.944 156.245 16.32 156.885 16.56C157.525 16.784 158.269 16.896 159.117 16.896C160.125 16.896 160.885 16.728 161.397 16.392C161.909 16.04 162.165 15.536 162.165 14.88C162.165 14.352 161.989 13.928 161.637 13.608C161.301 13.288 160.741 13.032 159.957 12.84L157.917 12.36C156.957 12.152 156.197 11.76 155.637 11.184C155.093 10.608 154.821 9.936 154.821 9.168C154.821 8.464 155.005 7.856 155.373 7.344C155.741 6.816 156.253 6.408 156.909 6.12C157.581 5.832 158.365 5.688 159.261 5.688C160.093 5.688 160.877 5.824 161.613 6.096C162.349 6.368 162.957 6.76 163.437 7.272L162.885 8.4C162.325 7.92 161.749 7.56 161.157 7.32C160.581 7.08 159.949 6.96 159.261 6.96C158.333 6.96 157.613 7.144 157.101 7.512C156.589 7.88 156.333 8.384 156.333 9.024C156.333 9.584 156.493 10.032 156.813 10.368C157.133 10.688 157.645 10.936 158.349 11.112L160.389 11.616C161.493 11.856 162.309 12.248 162.837 12.792C163.365 13.32 163.629 13.992 163.629 14.808C163.629 15.816 163.213 16.632 162.381 17.256C161.565 17.864 160.453 18.168 159.045 18.168ZM176.205 14.016C183.885 14.88 190.101 11.28 190.101 6.168C190.101 3.192 188.445 1.272 182.493 1.272C180.333 1.272 178.125 1.512 176.037 2.112C175.365 2.304 174.645 2.496 174.213 3.096C173.829 3.624 172.437 5.472 172.437 6.048C172.437 6.264 172.677 6.288 172.821 6.288C173.541 6.288 176.877 4.752 181.077 4.68L177.261 9.72C176.253 10.032 173.421 10.68 173.421 12.072C173.421 12.816 173.925 13.248 174.501 13.608C173.925 14.616 172.221 17.232 172.221 18.288C172.221 18.816 172.461 19.152 173.037 19.152C173.637 19.152 174.141 18.456 174.453 18.024C175.077 17.184 175.677 16.392 175.677 16.08C175.677 15.816 175.437 15.696 175.221 15.648L176.205 14.016ZM181.941 5.928C182.301 5.424 182.445 5.112 182.469 4.464C183.573 4.464 188.085 4.752 188.349 6.216C188.613 7.728 185.517 11.16 180.813 11.424C180.045 11.472 179.229 11.424 178.341 11.184L178.869 10.392C179.253 10.128 181.293 10.008 181.293 9.528C181.293 9.072 180.309 9.408 179.421 9.408L181.941 5.928ZM193.106 12.24L193.154 12.288C192.194 13.44 190.946 14.712 190.946 16.344C190.946 17.256 191.57 18.192 192.578 18.192C193.946 18.192 196.706 15.528 197.618 14.496L197.45 13.608C196.658 14.208 195.146 15.768 194.114 15.768C193.754 15.768 193.37 15.48 193.37 15.096C193.37 14.136 194.546 12.96 195.242 12.36C195.794 11.904 196.25 11.544 196.25 10.8C196.202 10.368 196.034 9.984 195.674 9.528C195.434 9.24 194.114 9.528 191.858 9.84C191.954 9.624 192.026 9.408 192.026 9.168C192.026 8.952 191.834 8.76 191.594 8.76C191.018 8.76 188.57 10.488 188.57 12.216C188.57 12.72 188.714 12.912 189.218 12.96C188.762 13.608 188.258 14.208 187.634 14.712L187.73 15.768C188.762 14.736 189.77 14.112 190.61 12.792L193.106 12.24ZM206.825 13.392C206.081 13.944 205.409 14.856 204.353 14.664C204.809 13.728 205.289 12.888 205.289 11.832C205.289 11.112 205.145 10.608 204.425 10.416C204.689 10.272 204.977 10.128 204.977 9.768C204.977 9.12 204.185 8.88 203.657 8.904C202.193 8.976 200.465 9.624 199.169 10.68C197.825 11.712 196.937 12.912 196.985 14.712C197.081 16.896 198.233 18.144 199.481 18.144C200.993 18.144 202.169 17.376 203.177 16.344C204.929 16.344 205.985 15.768 207.065 14.4L206.825 13.392ZM202.409 11.52C202.505 11.568 201.377 13.08 201.377 14.328C201.377 14.736 201.497 15.048 201.665 15.432C201.113 15.936 200.609 16.392 199.817 16.392C199.217 16.392 198.761 16.176 198.761 15.48C198.761 14.736 199.217 13.8 199.745 13.272C200.465 12.576 201.593 12 202.409 11.52Z" fill="white"/>
            </svg>
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
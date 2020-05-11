<?php

$all_plugins = get_plugins();
$active_plugins = get_option('active_plugins');
$extension_permission = get_option('wpwh_extension_permission');
$extensions_list = WPWHPRO()->api->get_extension_list();
$plugin_update_list = get_site_transient( 'update_plugins' );
if( isset( $plugin_update_list->response ) ){
    $plugin_update_list = $plugin_update_list->response;
}

if( isset( $_POST['wpwh_extension_permission'] ) ){
    if( current_user_can( WPWHPRO()->settings->get_admin_cap('admin-page-extensions') ) ){
        update_option( 'wpwh_extension_permission', 'yes' );
        $extension_permission = 'yes';
    }
}

?>
<h2><?php echo WPWHPRO()->helpers->translate( 'Extensions for', 'wpwhpro-page-logs' ) . ' ' . $this->page_title; ?> </h2>

<div>
<?php echo sprintf( WPWHPRO()->helpers->translate( 'This page contains all approved extensions for <strong>%1$s</strong>. You will be able to fully manage each of the extensions right within this plugin. In case you want to list your very own plugin here, feel free to <a title="Go to our contact form" target="_blank" href="%2$s">reach out to us</a>.', 'wpwhpro-page-logs' ), $this->page_title, 'https://ironikus.com/contact/' ); ?>
</div>

<?php if( empty( $extension_permission ) ) : ?>

    <div style="padding:50px;display:flex;align-items:center;flex-direction: column;">
        <div>
            <?php echo WPWHPRO()->helpers->translate( 'Your permission is required to use this features since we need to call our own API for fetching all available extensions.', 'wpwhpro-page-logs' ); ?>
        </div>
        <form method="post" style="margin-top:10px;">
            <input type="hidden" name="wpwh_extension_permission" value="yes" />
            <button type="submit" class="btn btn-primary"><?php echo WPWHPRO()->helpers->translate( 'Permission granted', 'wpwhpro-page-logs' ); ?></button>
        </form>
    </div>

<?php else : ?>

    <div class="wpwh-extensions-wrapper">

        <?php if( ! empty( $extensions_list ) ) : ?>
            <?php foreach( $extensions_list as $slug => $data ) : 
            
            $plugin_installed = WPWHPRO()->helpers->is_plugin_installed( $data['extension_plugin_slug'] );
            $plugin_active = ( in_array( $data['extension_plugin_slug'], $active_plugins ) ) ? true : false;
            $plugin_premium = ( $data['type'] === 'premium' ) ? true : false;

            $plugin_version = 0;
            if( isset( $all_plugins[ $data['extension_plugin_slug'] ] ) ){
                $plugin_version = $all_plugins[ $data['extension_plugin_slug'] ]['Version'];
            }

            //Make sure we only show the update button if the plugin is recognized already by the WP related logic
            $available_version = $plugin_version;
            if( isset( $plugin_update_list[ $data['extension_plugin_slug'] ] ) ){
                if( isset( $plugin_update_list[ $data['extension_plugin_slug'] ]->new_version ) ){
                    $available_version = $plugin_update_list[ $data['extension_plugin_slug'] ]->new_version;
                }
            }

            ?>
                <div class="card single-extension" style="width: 18rem;">
                    <img src="<?php echo esc_url( $data['thumbnail'] ); ?>" class="card-img-top" alt="<?php echo sanitize_text_field( $data['name'] ); ?>">

                    <div class="card-body">
                        <small>v<?php echo sanitize_text_field( $data['version'] ); ?></small>
                        <h5 class="card-title">
                            <?php if( $data['type'] === 'premium' ) : ?>
                                <span class="golden">Pro</span> 
                            <?php endif; ?>
                            <?php echo sanitize_text_field( $data['name'] ); ?>
                        </h5>
                        <p class="card-text">
                            <?php echo sanitize_text_field( $data['description'] ); ?>
                        </p>
                        <a href="<?php echo esc_url( $data['extension_info_url'] ); ?>" target="_blank" class="btn btn-info more-info">More info</a>

                        <?php if( $plugin_installed ) : ?>

                            <?php if( version_compare( (string) $available_version, (string) $plugin_version, '>') ) : ?>

                                <?php if( $plugin_premium ) : ?>
                                    <a class="btn btn-secondary h30 ironikus-extension-manage" href="<?php echo get_admin_url(); ?>options-general.php?page=wp-webhooks-pro&wpwhprovrs=pro" title="<?php echo WPWHPRO()->helpers->translate( 'Activate your licene first', 'wpwhpro-page-extensions' ); ?>">
                                        <?php echo WPWHPRO()->helpers->translate( 'License', 'wpwhpro-page-extensions' ); ?>
                                    </a>
                                <?php else : 
                                
                                $update_status = ( $plugin_active ) ? 'update_active' : 'update_deactive';
                                
                                ?>
                                    <p class="btn btn-dark h30 ironikus-extension-manage" title="<?php echo sprintf( WPWHPRO()->helpers->translate( 'Upgrade from your current version %1$s to version %2$s', 'wpwhpro-page-extensions' ), $plugin_version, $available_version ); ?>" webhook-extension-id="<?php echo intval( $data['item_id'] ); ?>" webhook-extension-version="<?php echo sanitize_text_field( $available_version ); ?>" webhook-extension-status="<?php echo $update_status; ?>" webhook-extension-slug="<?php echo sanitize_text_field( $data['extension_plugin_slug'] ); ?>" webhook-extension-dl="<?php echo sanitize_text_field( $data['extension_download_url'] ); ?>">
                                        <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Update', 'wpwhpro-page-extensions' ); ?></span>
                                        <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                    </p>
                                <?php endif; ?>

                            <?php else : ?>

                                <?php if( $plugin_active ) : ?>
                                    <p class="btn btn-warning h30 ironikus-extension-manage" webhook-extension-status="activated" webhook-extension-slug="<?php echo sanitize_text_field( $data['extension_plugin_slug'] ); ?>" webhook-extension-dl="<?php echo sanitize_text_field( $data['extension_download_url'] ); ?>">
                                        <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' ); ?></span>
                                        <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                    </p>
                                <?php else : ?>
                                    <p class="btn btn-success h30 ironikus-extension-manage" webhook-extension-status="deactivated" webhook-extension-slug="<?php echo sanitize_text_field( $data['extension_plugin_slug'] ); ?>" webhook-extension-dl="<?php echo sanitize_text_field( $data['extension_download_url'] ); ?>">
                                        <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' ); ?></span>
                                        <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                    </p>
                                <?php endif; ?>
                            
                            <?php endif; ?>
                            
                        <?php else : ?>

                            <?php if( $plugin_premium ) : ?>
                                <a class="btn btn-secondary h30 ironikus-extension-manage" href="<?php echo get_admin_url(); ?>options-general.php?page=wp-webhooks-pro&wpwhprovrs=pro" title="<?php echo WPWHPRO()->helpers->translate( 'Activate your licene first', 'wpwhpro-page-extensions' ); ?>">
                                    <?php echo WPWHPRO()->helpers->translate( 'License', 'wpwhpro-page-extensions' ); ?>
                                </a>
                            <?php else : ?>
                                <p class="btn btn-primary h30 ironikus-extension-manage" webhook-extension-id="<?php echo intval( $data['item_id'] ); ?>" webhook-extension-version="<?php echo sanitize_text_field( $data['version'] ); ?>" webhook-extension-status="uninstalled" webhook-extension-slug="<?php echo sanitize_text_field( $data['extension_plugin_slug'] ); ?>" webhook-extension-dl="<?php echo sanitize_text_field( $data['extension_download_url'] ); ?>">
                                    <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Install', 'wpwhpro-page-extensions' ); ?></span>
                                    <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="bottom-action-wrapper">
                            <?php if( $plugin_installed ) : ?>
                                    <div class="ironikus-extension-delete" webhook-extension-status="delete" webhook-extension-slug="<?php echo sanitize_text_field( $data['extension_plugin_slug'] ); ?>" webhook-extension-dl="<?php echo sanitize_text_field( $data['extension_download_url'] ); ?>">
                                        <small><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-extensions' ); ?></small>
                                    </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        Made by <a href="<?php echo esc_url( $data['vendor']['url'] ); ?>" title="Go to <?php echo sanitize_text_field( $data['vendor']['name'] ); ?>" target="_blank"><?php echo sanitize_text_field( $data['vendor']['name'] ); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="wpwhpro-empty">
                <?php echo WPWHPRO()->helpers->translate( 'There are currently no extensions available.', 'wpwhpro-page-extensions' ); ?>
            </div>
        <?php endif; ?>

    </div>

<?php endif; ?>
<?php

$webhooks = WPWHPRO()->webhook->get_hooks( 'action' ) ;
$current_url = WPWHPRO()->helpers->get_current_url(false);
$clear_form_url = WPWHPRO()->helpers->get_current_url();
$action_nonce_data = WPWHPRO()->settings->get_action_nonce();
$actions = WPWHPRO()->webhook->get_actions();

//Create new Wehook
if( isset( $_POST['ironikus_WP_Webhooks_Pro_webhook_new_ident'] ) ){
	if ( ! check_admin_referer( $action_nonce_data['action'], $action_nonce_data['arg'] ) ) {
		return;
	}

    $new_webhook = sanitize_title($_POST['ironikus_WP_Webhooks_Pro_webhook_new_ident']);
    if( ! isset( $webhooks[ $new_webhook ] ) ){
        WPWHPRO()->webhook->create( $new_webhook, 'action' );

        //Init webhooks again
	    $webhooks = WPWHPRO()->webhook->get_hooks( 'action' );
    }
}

//Delete Webhook
if( isset( $_GET['wpwhpro_delete'] ) ){
    $check = WPWHPRO()->webhook->unset_hooks( $_GET['wpwhpro_delete'], 'action' );
    if( $check ){
        echo WPWHPRO()->helpers->create_admin_notice( array( 'The following webhook was deleted successfully: %s', $_GET['wpwhpro_delete'] ), 'success', true );

        //Init webhooks again
	    $webhooks = WPWHPRO()->webhook->get_hooks( 'action' );
    }
    unset( $_GET[ 'wpwhpro_delete' ] );
	$clear_form_url = WPWHPRO()->helpers->built_url( $current_url, $_GET );
}

?>
<h2><?php echo WPWHPRO()->helpers->translate( 'Receive Data via WP Webhooks', 'wpwhpro-page-actions' ); ?></h2>

<p>
	<?php echo sprintf(WPWHPRO()->helpers->translate( 'Use the webhook url down below to connect your specified with your site. Please note, that deleting the default main webhook creates automatically a new one. If you need more information, check out the installation and documentation by clicking <a href="%s" target="_blank" >here</a>.', 'wpwhpro-page-actions' ), 'https://ironikus.com/docs/?utm_source=wp-webhooks-pro&utm_medium=notice-recieve-data-docs&utm_campaign=WP%20Webhooks%20Pro'); ?>
</p>

<table class="ironikus-webhook-table">
    <thead>
        <tr>
            <th style="width:20%">
                <?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-actions' ); ?>
            </th>
            <th style="width:35%">
                <?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-actions' ); ?>
            </th>
            <th style="width:10%">
		        <?php echo WPWHPRO()->helpers->translate( 'Action', 'wpwhpro-page-actions' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $webhooks as $webhook => $webhook_data ) : ?>
        <?php if( ! is_array( $webhook_data ) ) { continue; } ?>
        <?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-settings-action-data-webhook' ), $webhook ) ) ) { continue; } ?>
        <tr>
            <td>
                <?php echo $webhook; ?>
            </td>
            <td>
                <input class="ironikus-webhook-input" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>" readonly /><br>
            </td>
            <td>
                <div class="ironikus-element-actions">
                    <a href="<?php echo WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_delete' => $webhook, ) ) ); ?>" title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-actions' ); ?>" ><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-actions' ); ?></a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="ironikus-add-wehook">
    <form method="post" action="<?php echo $clear_form_url; ?>">
        <input class="ironikus-webhook-input-new h30" style="width:200px;" type='text' name='ironikus_WP_Webhooks_Pro_webhook_new_ident' placeholder="<?php echo WPWHPRO()->helpers->translate( 'Add Webhook Name here...', 'wpwhpro-page-actions' ); ?>" />
	    <?php wp_nonce_field( $action_nonce_data['action'], $action_nonce_data['arg'] ); ?>
        <?php submit_button( WPWHPRO()->helpers->translate( 'Add Webhook', 'wpwhpro-page-actions' ), 'button', 'submit', false ); ?>
    </form>
</div>

<div class="ironikus-webhook-actions">
    <h2><?php echo WPWHPRO()->helpers->translate( 'Available Webhook Actions', 'wpwhpro-page-actions' ); ?></h2>
    <p style="font-weight:normal;"><?php echo WPWHPRO()->helpers->translate( 'Below you will find a list of all available actions when sending data from your specified service to WordPress.', 'wpwhpro-page-actions' ); ?></p>

    <?php if( ! empty( $actions ) ) : ?>
        <?php foreach( $actions as $action ) : ?>
            <div class="accordion irnks-accordion">
                <div class="accordion__item irnks-accordion-item">
                    <div class="accordion-header irnks-accordion-header"><?php echo $action['action']; ?></div>
                    <div class="accordion-body irnks-accordion-body">
                        <div class="accordion-body__contents">
                            <?php echo $action['short_description']; ?>
                        </div>
                        <div class="accordion irnks-accordion">
                            <div class="accordion__item irnks-accordion-item">
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWHPRO()->helpers->translate( 'Accepted Arguments:', 'wpwhpro-page-actions'); ?></div>
                                <div class="accordion-body irnks-accordion-body">
                                    <div class="accordion-body__contents ironikus-arguments">
                                        <ul>
                                            <?php foreach( $action['parameter'] as $param => $param_data ) : ?>
                                                <li>
                                                    <div class="ironikus-attribute-wrapper">
                                                        <strong><?php echo $param; echo ( ! empty( $param_data['required'] ) ) ? '<span style="color:red;">*</span>' : '' ?></strong>
                                                        <?php if( isset( $param_data['short_description'] ) ) : ?>
                                                            <br>
                                                            <small><?php echo $param_data['short_description']; ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php if( ! empty( $action['returns'] ) || ! empty( $action['returns_code'] ) ) : ?>
                                <div class="accordion__item irnks-accordion-item">
                                    <div class="accordion-header irnks-accordion-header"><?php echo WPWHPRO()->helpers->translate( 'Return values:', 'wpwhpro-page-actions'); ?></div>
                                    <div class="accordion-body irnks-accordion-body">
                                        <div class="accordion-body__contents ironikus-arguments">
                                            <?php if( ! empty( $action['returns'] ) ) : ?>
                                                <ul>
                                                    <?php foreach( $action['returns'] as $param => $param_data ) : ?>
                                                        <li>
                                                            <div class="ironikus-attribute-wrapper">
                                                                <strong><?php echo $param; ?></strong>
                                                                <?php if( isset( $param_data['short_description'] ) ) : ?>
                                                                    <br>
                                                                    <small><?php echo $param_data['short_description']; ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>

	                                            <?php if( ! empty( $action['returns_code'] ) ) : ?>
                                                    <p>
	                                                    <?php echo WPWHPRO()->helpers->translate( 'Here is an example of all the available default fields. The fields may vary based on custom extensions or third party plugins.', 'wpwhpro-page-actions'); ?>
			                                            <?php echo $action['returns_code']; ?>
                                                    </p>
	                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="accordion__item irnks-accordion-item">
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-actions' ); ?></div>
                                <div class="accordion-body irnks-accordion-body">
                                    <div class="accordion-body__contents">
                                        <?php echo $action['description']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion__item irnks-accordion-item">
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWHPRO()->helpers->translate( 'Test action', 'wpwhpro-page-actions' ); ?></div>
                                <div class="accordion-body irnks-accordion-body">
                                    <div class="accordion-body__contents">
                                        <p>
	                                        <?php echo WPWHPRO()->helpers->translate( 'Here you can test the specified webhook. Please note, that this test is able to modify the data of your website (Depending on what action you test). Also you will see the response as any webservice recieves it.', 'wpwhpro-page-actions'); ?>
                                        </p>
                                        <form method="post" action="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ) . '&wpwhpro_direct_test=1'; ?>" target="_blank">

                                            <table class="wpwhpro-settings-table form-table">
                                                <tbody>

                                                <?php foreach( $action['parameter'] as $param => $param_data ) : ?>

                                                    <tr valign="top">
                                                        <td>
                                                            <input id="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>" type="text" name="<?php echo $param; ?>" placeholder="<?php echo ( ! empty( $param_data['required'] ) ) ? WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions') : '' ?>">
                                                        </td>
                                                        <td scope="row" valign="top">
                                                            <label for="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>">
                                                                <strong><?php echo $param; ?></strong>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <p class="description">
				                                                <?php echo $param_data['short_description']; ?>
                                                            </p>
                                                        </td>
                                                    </tr>

                                                <?php endforeach; ?>

                                                </tbody>
                                            </table>

                                            <input type="hidden" name="action" value="<?php echo $action['action']; ?>">
	                                        <?php submit_button( WPWHPRO()->helpers->translate( 'Test action', 'admin-settings' ) ); ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div
                </div>
            </div>
        <?php endforeach; ?>
	<?php else : ?>
        <div class="wpwhpro-empty">
		    <?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any actions activated. Please go to our settings tab and activate some.', 'wpwhpro-page-actions' ); ?>
        </div>
	<?php endif; ?>
</div>

<p>
    <small>* <?php echo WPWHPRO()->helpers->translate( 'Required fields.', 'wpwhpro-page-actions' ); ?></small>
</p>
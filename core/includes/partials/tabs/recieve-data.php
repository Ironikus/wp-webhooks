<?php

$webhooks = WPWH()->webhook->get_hooks( 'action' ) ;
$current_url = WPWH()->helpers->get_current_url(false);
$clear_form_url = WPWH()->helpers->get_current_url();
$action_nonce_data = WPWH()->settings->get_action_nonce();
$actions = WPWH()->webhook->get_actions();

//Create new Wehook
if( isset( $_POST['ironikus_WP_Webhooks_webhook_new_ident'] ) ){
	if ( ! check_admin_referer( $action_nonce_data['action'], $action_nonce_data['arg'] ) ) {
		return;
	}

    $new_webhook = sanitize_title($_POST['ironikus_WP_Webhooks_webhook_new_ident']);
    if( ! isset( $webhooks[ $new_webhook ] ) ){
        WPWH()->webhook->create( $new_webhook, 'action' );

        //Init webhooks again
	    $webhooks = WPWH()->webhook->get_hooks( 'action' );
    }
}

//Delete Webhook
if( isset( $_GET['wpwh_delete'] ) ){
    $check = WPWH()->webhook->unset_hooks( $_GET['wpwh_delete'], 'action' );
    if( $check ){
        echo WPWH()->helpers->create_admin_notice( array( 'The following webhook was deleted successfully: %s', $_GET['wpwh_delete'] ), 'success', true );

        //Init webhooks again
	    $webhooks = WPWH()->webhook->get_hooks( 'action' );
    }
    unset( $_GET[ 'wpwh_delete' ] );
	$clear_form_url = WPWH()->helpers->built_url( $current_url, $_GET );
}

?>
<h2><?php echo WPWH()->helpers->translate( 'Receive Data From Webhooks', 'wpwh-page-actions' ); ?></h2>

<p>
	<?php echo sprintf(WPWH()->helpers->translate( 'Use the webhook url down below to connect your specified with your site. Please note, that deleting the default main webhook creates automatically a new one. If you need more information, check out the installation and documentation by clicking <a href="%s" target="_blank" >here</a>.', 'wpwh-page-actions' ), 'https://ironikus.com/docs/?utm_source=wp-webhooks&utm_medium=notice-recieve-data-docs&utm_campaign=WP%20Webhooks%20Pro'); ?>
</p>

<table class="ironikus-webhook-table">
    <thead>
        <tr>
            <th style="width:20%">
                <?php echo WPWH()->helpers->translate( 'Webhook Name', 'wpwh-page-actions' ); ?>
            </th>
            <th style="width:35%">
                <?php echo WPWH()->helpers->translate( 'Webhook URL', 'wpwh-page-actions' ); ?>
            </th>
            <th style="width:10%">
		        <?php echo WPWH()->helpers->translate( 'Action', 'wpwh-page-actions' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $webhooks as $webhook => $webhook_data ) : ?>
        <?php if( ! is_array( $webhook_data ) ) { continue; } ?>
        <?php if( ! current_user_can( apply_filters( 'wpwh/admin/settings/webhook/page_capability', WPWH()->settings->get_admin_cap( 'wpwh-page-settings-action-data-webhook' ), $webhook ) ) ) { continue; } ?>
        <tr>
            <td>
                <?php echo $webhook; ?>
            </td>
            <td>
                <input class="ironikus-webhook-input" type='text' name='ironikus_wp_webhooks_webhook_url' value="<?php echo WPWH()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>" readonly /><br>
            </td>
            <td>
                <div class="ironikus-element-actions">
                    <a href="<?php echo WPWH()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwh_delete' => $webhook, ) ) ); ?>" title="<?php echo WPWH()->helpers->translate( 'Delete', 'wpwh-page-actions' ); ?>" ><?php echo WPWH()->helpers->translate( 'Delete', 'wpwh-page-actions' ); ?></a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="ironikus-add-wehook">
    <form method="post" action="<?php echo $clear_form_url; ?>">
        <input class="ironikus-webhook-input-new h30" style="width:200px;" type='text' name='ironikus_WP_Webhooks_webhook_new_ident' placeholder="<?php echo WPWH()->helpers->translate( 'Add Webhook Name here...', 'wpwh-page-actions' ); ?>" />
	    <?php wp_nonce_field( $action_nonce_data['action'], $action_nonce_data['arg'] ); ?>
        <?php submit_button( WPWH()->helpers->translate( 'Add Webhook', 'wpwh-page-actions' ), 'button', 'submit', false ); ?>
    </form>
</div>

<div class="ironikus-webhook-actions">
    <h2><?php echo WPWH()->helpers->translate( 'Available Webhook Actions', 'wpwh-page-actions' ); ?></h2>
    <p style="font-weight:normal;"><?php echo WPWH()->helpers->translate( 'Below you will find a list of all available actions when sending data from your specified service to WordPress.', 'wpwh-page-actions' ); ?></p>

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
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWH()->helpers->translate( 'Accepted Arguments:', 'wpwh-page-actions'); ?></div>
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
                                    <div class="accordion-header irnks-accordion-header"><?php echo WPWH()->helpers->translate( 'Return values:', 'wpwh-page-actions'); ?></div>
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
	                                                    <?php echo WPWH()->helpers->translate( 'Here is an example of all the available default fields. The fields may vary based on custom extensions or third party plugins.', 'wpwh-page-actions'); ?>
			                                            <?php echo $action['returns_code']; ?>
                                                    </p>
	                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="accordion__item irnks-accordion-item">
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWH()->helpers->translate( 'Description', 'wpwh-page-actions' ); ?></div>
                                <div class="accordion-body irnks-accordion-body">
                                    <div class="accordion-body__contents">
                                        <?php echo $action['description']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div
                </div>
            </div>
        <?php endforeach; ?>
	<?php else : ?>
        <div class="wpwh-empty">
		    <?php echo WPWH()->helpers->translate( 'You currently don\'t have any actions activated. Please go to our settings tab and activate some.', 'wpwh-page-actions' ); ?>
        </div>
	<?php endif; ?>
</div>

<p>
    <small>* <?php echo WPWH()->helpers->translate( 'Required fields.', 'wpwh-page-actions' ); ?></small>
</p>
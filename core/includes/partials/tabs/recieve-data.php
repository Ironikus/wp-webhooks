<?php

$webhooks = WPWHPRO()->webhook->get_hooks( 'action' ) ;
$current_url = WPWHPRO()->helpers->get_current_url(false);
$clear_form_url = WPWHPRO()->helpers->get_current_url();
$action_nonce_data = WPWHPRO()->settings->get_action_nonce();
$actions = WPWHPRO()->webhook->get_actions();

?>
<?php add_ThickBox(); ?>
<h2><?php echo WPWHPRO()->helpers->translate( 'Receive Data From WP Webhooks Pro', 'wpwhpro-page-actions' ); ?></h2>

<p>
	<?php echo sprintf(WPWHPRO()->helpers->translate( 'Use the webhook url down below to connect your specified with your site. Please note, that deleting the default main webhook creates automatically a new one. If you need more information, check out the installation and documentation by clicking <a href="%s" target="_blank" >here</a>.', 'wpwhpro-page-actions' ), 'https://ironikus.com/docs/?utm_source=wp-webhooks-pro&utm_medium=notice-recieve-data-docs&utm_campaign=WP%20Webhooks%20Pro'); ?>
</p>

<table class="ironikus-webhook-table ironikus-webhook-action-table">
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
    <?php foreach( $webhooks as $webhook => $webhook_data ) : 
        
        //Map default action_attributes if available
        $settings = array();
        if( ! empty( $webhook_data['settings'] ) ){

            if( isset( $webhook_data['settings']['data'] ) ){
                $settings = (array) $webhook_data['settings']['data'];
            }

            if( isset( $webhook_data['settings']['load_default_settings'] ) && $webhook_data['settings']['load_default_settings'] === true ){
                    $settings = array_merge( WPWHPRO()->settings->get_default_action_settings(), $settings );
            }

        }

        //Map dynamic data mapping settings
        $required_settings = WPWHPRO()->settings->get_required_action_settings();
        $settings = array_merge( $required_settings, $settings );

        ?>
        <?php if( ! is_array( $webhook_data ) ) { continue; } ?>
        <?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-settings-action-data-webhook' ), $webhook ) ) ) { continue; } ?>
        <tr id="webhook-action-<?php echo $webhook; ?>">
            <td>
                <?php echo $webhook; ?>
            </td>
            <td>
                <input class="ironikus-webhook-input" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>" readonly /><br>
            </td>
            <td>
                <div class="ironikus-element-actions">
                    <p class="ironikus-delete-action" ironikus-webhook-slug="<?php echo $webhook; ?>"><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-actions' ); ?></p>
                    <a class="thickbox ironikus-action-settings-wrapper" title="<?php echo $webhook; ?>" href="#TB_inline?height=330&width=800&inlineId=wpwhpro-action-settings-<?php echo $webhook; ?>">
                        <span class="ironikus-settings"><?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-actions' ); ?></span>
                    </a>

                    <div id="wpwhpro-action-settings-<?php echo $webhook; ?>" style="display:none;">
                        <div class="ironikus-tb-webhook-actions-wrapper">
                            <div class="ironikus-tb-webhook-url">
                                <strong>Webhook url:</strong>
                                <br>
                                <?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>
                            </div>
                            <div class="ironikus-tb-webhook-settings">
                                <?php if( $settings ) : ?>
                                    <form id="ironikus-webhook-action-form-<?php echo $webhook; ?>">
                                        <table class="wpwhpro-action-settings-table form-table">
                                            <tbody>

                                            <?php

                                            $settings_data = array();
                                            if( isset( $webhook_data['settings'] ) && ! empty( $webhook_data['settings'] ) ){
                                                $settings_data = $webhook_data['settings'];
                                            }

                                            foreach( $settings as $setting_name => $setting ) :

                                                $is_checked = ( $setting['type'] == 'checkbox' && $setting['default_value'] == 'yes' ) ? 'checked' : '';
                                                $value = ( $setting['type'] != 'checkbox' && isset( $setting['default_value'] ) ) ? $setting['default_value'] : '1';

                                                if( isset( $settings_data[ $setting_name ] ) ){
                                                    $value = $settings_data[ $setting_name ];
                                                    $is_checked = ( $setting['type'] == 'checkbox' && $value == 1 ) ? 'checked' : '';
                                                }

                                                ?>
                                                <tr valign="top">
                                                    <td>
                                                        <?php if( in_array( $setting['type'], array( 'text', 'checkbox' ) ) ) : ?>
                                                        <input id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
                                                        <?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
                                                            <select name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>>
                                                                <?php
                                                                    if( isset( $settings_data[ $setting_name ] ) ){
                                                                        $settings_data[ $setting_name ] = ( is_array( $settings_data[ $setting_name ] ) ) ? array_flip( $settings_data[ $setting_name ] ) : $settings_data[ $setting_name ];
                                                                    }
                                                                ?>
                                                                <?php foreach( $setting['choices'] as $choice_name => $choice_label ) : ?>
                                                                    <?php
                                                                        $selected = '';
                                                                        if( isset( $settings_data[ $setting_name ] ) ){

                                                                            if( is_array( $settings_data[ $setting_name ] ) ){
                                                                                if( isset( $settings_data[ $setting_name ][ $choice_name ] ) ){
                                                                                    $selected = 'selected="selected"';
                                                                                }
                                                                            } else {
                                                                                var_dump($choice_name);
                                                                                var_dump($settings_data[ $setting_name ]);
                                                                                if( (string) $settings_data[ $setting_name ] === (string) $choice_name ){
                                                                                    $selected = 'selected="selected"';
                                                                                }
                                                                            }

                                                                        }
                                                                    ?>
                                                                    <option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo WPWHPRO()->helpers->translate( $choice_label, 'wpwhpro-page-actions' ); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td scope="row" valign="top">
                                                        <label for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>">
                                                            <strong><?php echo $setting['label']; ?></strong>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <p class="description">
                                                            <?php echo $setting['description']; ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>

                                            </tbody>
                                        </table>
                                        <div class="ironikus-single-webhook-action-handler">
                                            <p class="h30 ironikus-actions-submit-data-mapping-form" id="<?php echo $webhook; ?>" webhook-id="<?php echo $webhook; ?>" >
                                                <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Save Settings', 'wpwhpro-page-actions' ); ?></span>
                                                <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                            </p>
                                        </div>
                                    </form>
                                <?php else : ?>
                                    <div class="wpwhpro-empty">
                                        <?php echo WPWHPRO()->helpers->translate( 'For your current webhook are no settings available.', 'wpwhpro-page-triggers' ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="ironikus-add-wehook-action-handler">
    <input id="ironikus-webhook-action-name" class="ironikus-webhook-input-new h30" type="text" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Include your wehook name here.', 'wpwhpro-page-actions' ); ?>" >
    <p class="ironikus-action-save h30" >
        <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Add Webhook', 'wpwhpro-page-actions' ); ?></span>
        <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
    </p>
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
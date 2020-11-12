<?php

$webhooks = WPWHPRO()->webhook->get_hooks( 'action' ) ;
$current_url = WPWHPRO()->helpers->get_current_url(false);
$clear_form_url = WPWHPRO()->helpers->get_current_url();
$action_nonce_data = WPWHPRO()->settings->get_action_nonce();
$actions = WPWHPRO()->webhook->get_actions();

?>
<?php add_ThickBox(); ?>
<h2><?php echo WPWHPRO()->helpers->translate( 'Receive Data From WP Webhooks', 'wpwhpro-page-actions' ); ?></h2>

<div>
<?php echo sprintf(WPWHPRO()->helpers->translate( 'Use the webhook URL down below to connect your external service with your site. This URL receives data from external endpoints and does certain actions on your WordPress site. Please note, that deleting the default main webhook creates automatically a new one. If you need more information, check out the installation and documentation by clicking <a href="%s" target="_blank" >here</a>.', 'wpwhpro-page-actions' ), 'https://ironikus.com/docs/?utm_source=wp-webhooks&utm_medium=notice-receive-data-docs&utm_campaign=WP%20Webhooks'); ?>
</div>

<table class="table ironikus-webhook-table ironikus-webhook-action-table">
    <thead class="thead-dark">
        <tr>
            <th style="width:20%">
                <?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-actions' ); ?>
            </th>
            <th style="width:45%">
                <?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-actions' ); ?>
            </th>
            <th style="width:25%">
                <?php echo WPWHPRO()->helpers->translate( 'Webhook API Key', 'wpwhpro-page-actions' ); ?>
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

        $status = 'active';
        $status_name = 'Deactivate';
        if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
            $status = 'inactive';
            $status_name = 'Activate';
        }

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
                <input class="ironikus-webhook-input" type='text' name='ironikus_wp_webhooks_pro_webhook_api_key' value="<?php echo $webhook_data['api_key']; ?>" readonly /><br>
            </td>
            <td>
                <div class="ironikus-element-actions">
                    <span class="ironikus-delete-action" ironikus-webhook-slug="<?php echo $webhook; ?>"><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-actions' ); ?></span>
                    <br>
                    <span class="ironikus-status-action <?php echo $status; ?>" ironikus-webhook-status="<?php echo $status; ?>" ironikus-webhook-slug="<?php echo $webhook; ?>"><?php echo WPWHPRO()->helpers->translate( $status_name, 'wpwhpro-page-actions' ); ?></span>
                    <br>
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
                                            <p class="btn btn-primary h30 ironikus-actions-submit-settings-form" id="<?php echo $webhook; ?>" webhook-id="<?php echo $webhook; ?>" >
                                                <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Save Settings', 'wpwhpro-page-actions' ); ?></span>
                                                <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                            </p>
                                        </div>
                                    </form>
                                <?php else : ?>
                                    <div class="wpwhpro-empty">
                                        <?php echo WPWHPRO()->helpers->translate( 'For your current webhook are no settings available.', 'wpwhpro-page-actions' ); ?>
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
    <div class="input-group mb-3">
        <label class="input-group-prepend" for="ironikus-webhook-action-name">
            <span class="input-group-text" id="input-group-webbhook-action-name"><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-actions' ); ?></span>
        </label>
        <input id="ironikus-webhook-action-name" class="form-control ironikus-webhook-input-new h30" type="text" aria-describedby="input-group-webbhook-action-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'my-webhook-name', 'wpwhpro-page-actions' ); ?>" >
    </div>
    <p class="btn btn-primary ironikus-action-save h30" >
        <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Add Webhook', 'wpwhpro-page-actions' ); ?></span>
        <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
    </p>
</div>

<div class="ironikus-webhook-actions">
    <h2><?php echo WPWHPRO()->helpers->translate( 'Available Webhook Actions', 'wpwhpro-page-actions' ); ?></h2>
    <div class="mb20" style="font-weight:normal;"><?php echo WPWHPRO()->helpers->translate( 'Below you will find a list of all available actions when sending data from your specified service to WordPress.', 'wpwhpro-page-actions' ); ?></div>

    <?php if( ! empty( $actions ) ) : ?>
        <div class="accordion" id="actionMainData">
            <?php foreach( $actions as $identkey => $action ) : ?>
                <div class="card">
                    <div class="card-header" id="headingactionMainData-<?php echo $identkey; ?>"  data-toggle="collapse" data-target="#collapseactionMainData-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapseactionMainData-<?php echo $identkey; ?>">
                        <button class="btn btn-link collapsed" type="button">
                            <?php echo $action['action']; ?>
                        </button>
                    </div>

                    <div id="collapseactionMainData-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingactionMainData-<?php echo $identkey; ?>" data-parent="#actionMainData">
                        <div class="card-body">
                            <div class="accordion-body__contents">
                                <?php echo $action['short_description']; ?>
                            </div>
                            <div class="accordion wpwh-action-arguments" id="actionArguments-<?php echo $identkey; ?>">
                                <div class="card">
                                    <div class="card-header" id="headingactionArgumentsSub-<?php echo $identkey; ?>"  data-toggle="collapse" data-target="#collapseactionArgumentsSub-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapseactionArgumentsSub-<?php echo $identkey; ?>">
                                        <button class="btn btn-link collapsed" type="button">
                                            <?php echo WPWHPRO()->helpers->translate( 'Accepted Arguments', 'wpwhpro-page-actions'); ?>
                                        </button>
                                    </div>

                                    <div id="collapseactionArgumentsSub-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingactionArgumentsSub-<?php echo $identkey; ?>" data-parent="#actionArguments-<?php echo $identkey; ?>">
                                        <div class="card-body">
                                            <ul>
                                                <li>
                                                    <div class="ironikus-attribute-wrapper">
                                                        <div class="ironikus-attribute-wrapper-heading required">
                                                            <strong><?php echo 'action'; echo '<span>' . WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions') . '</span>' ?></strong>
                                                        </div>
                                                        <div class="ironikus-attribute-wrapper-content">
                                                            <small><?php echo WPWHPRO()->helpers->translate( 'Always required. Determines which webhook action you want to target. (Alternatively, set this value as a query parameter within the URL) For this webhook action, please set it to ', 'wpwhpro-page-actions'); ?><strong><?php echo $action['action']; ?></strong></small>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php foreach( $action['parameter'] as $param => $param_data ) : ?>
                                                    <li>
                                                        <div class="ironikus-attribute-wrapper">
                                                            <div class="ironikus-attribute-wrapper-heading <?php echo ( ! empty( $param_data['required'] ) ) ? 'required' : '' ?>">
                                                                <strong><?php echo $param; echo ( ! empty( $param_data['required'] ) ) ? '<span>' . WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions') . '</span>' : '' ?></strong>
                                                            </div>
                                                            
                                                            <?php if( isset( $param_data['short_description'] ) ) : ?>
                                                                <div class="ironikus-attribute-wrapper-content">
                                                                    <small><?php echo $param_data['short_description']; ?></small>
                                                                </div>  
                                                            <?php endif; ?>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if( ! empty( $action['returns'] ) || ! empty( $action['returns_code'] ) ) : ?>
                                <div class="accordion" id="actionReturnValues-<?php echo $identkey; ?>">
                                    <div class="card">
                                        <div class="card-header" id="headingactionReturnValuesSub-<?php echo $identkey; ?>"  data-toggle="collapse" data-target="#collapseactionReturnValuesSub-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapseactionReturnValuesSub-<?php echo $identkey; ?>">
                                            <button class="btn btn-link collapsed" type="button">
                                                <?php echo WPWHPRO()->helpers->translate( 'Return values', 'wpwhpro-page-actions'); ?>
                                            </button>
                                        </div>

                                        <div id="collapseactionReturnValuesSub-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingactionReturnValuesSub-<?php echo $identkey; ?>" data-parent="#actionReturnValues-<?php echo $identkey; ?>">
                                            <div class="card-body">
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
                                                    <?php echo WPWHPRO()->helpers->translate( 'Here is an example of all the available default fields. The fields may vary based on custom extensions, third party plugins or different values.', 'wpwhpro-page-actions'); ?>
                                                        <?php echo $action['returns_code']; ?>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="accordion" id="actionDescription-<?php echo $identkey; ?>">
                                <div class="card">
                                    <div class="card-header" id="headingactionDescriptionSub-<?php echo $identkey; ?>"  data-toggle="collapse" data-target="#collapseactionDescriptionSub-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapseactionDescriptionSub-<?php echo $identkey; ?>">
                                        <button class="btn btn-link collapsed" type="button">
                                            <?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-actions'); ?>
                                        </button>
                                    </div>

                                    <div id="collapseactionDescriptionSub-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingactionDescriptionSub-<?php echo $identkey; ?>" data-parent="#actionDescription-<?php echo $identkey; ?>">
                                        <div class="card-body">
                                            <?php echo $action['description']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion" id="actionTesting-<?php echo $identkey; ?>">
                                <div class="card">
                                    <div class="card-header" id="headingactionTestingSub-<?php echo $identkey; ?>"  data-toggle="collapse" data-target="#collapseactionTestingSub-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapseactionTestingSub-<?php echo $identkey; ?>">
                                        <button class="btn btn-link collapsed" type="button">
                                        <?php echo WPWHPRO()->helpers->translate( 'Test action', 'wpwhpro-page-actions' ); ?>
                                        </button>
                                    </div>

                                    <div id="collapseactionTestingSub-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingactionTestingSub-<?php echo $identkey; ?>" data-parent="#actionTesting-<?php echo $identkey; ?>">
                                        <div class="card-body">
                                            <?php echo WPWHPRO()->helpers->translate( 'Here you can test the specified webhook. Please note, that this test can modify the data of your website (Depending on what action you test). Also, you will see the response as any web service receives it.', 'wpwhpro-page-actions'); ?>
                                            <br>
                                            <?php echo WPWHPRO()->helpers->translate( 'Please choose the webhook you are going to run the test with. Simply select the one you want to use down below.', 'wpwhpro-page-actions'); ?>
                                            <br>
                                            <select class="wpwhpro-webhook-actions-webhook-select custom-select-lg" wpwh-identkey="<?php echo $identkey; ?>">
                                                <option value="empty"><?php echo WPWHPRO()->helpers->translate( 'Choose...', 'wpwhpro-page-data-mapping' ); ?></option>
                                                <?php if( ! empty( $webhooks ) ) : ?>
                                                    <?php foreach( $webhooks as $subwebhook => $subwebhook_data ) : ?>
                                                        <option class="<?php echo $subwebhook; ?>" value="<?php echo WPWHPRO()->webhook->built_url( $subwebhook, $subwebhook_data['api_key'] ) . '&wpwhpro_direct_test=1'; ?>"><?php echo $subwebhook; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <form id="wpwh-action-testing-form-<?php echo $identkey; ?>" method="post" class="wpwh-actions-testing-form" action="" target="_blank" style="display:none;">

                                                <table class="wpwhpro-settings-table form-table">
                                                    <tbody>

                                                    <tr valign="top">
                                                        <td>
                                                            <input id="wpwhprotest_<?php echo $action['action']; ?>_action" class="form-control" type="text" name="action" value="<?php echo $action['action']; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions'); ?>">
                                                        </td>
                                                        <td scope="row" valign="top">
                                                            <label for="wpwhprotest_<?php echo $action['action']; ?>_action">
                                                                <strong>action</strong>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <p class="description">
                                                            <?php echo WPWHPRO()->helpers->translate( 'Always required. This argument determines which webhook you want to target. For this webhook action, please set it to ', 'wpwhpro-page-actions'); ?><strong><?php echo $action['action']; ?></strong>
                                                            </p>
                                                        </td>
                                                    </tr>

                                                    <?php foreach( $action['parameter'] as $param => $param_data ) : ?>

                                                        <tr valign="top">
                                                            <td>
                                                                <input id="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>" class="form-control" type="text" name="<?php echo $param; ?>" placeholder="<?php echo ( ! empty( $param_data['required'] ) ) ? WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions') : '' ?>">
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

                                                    <tr valign="top">
                                                            <td>
                                                                <input id="wpwhprotest_<?php echo $action['action']; ?>_access_token" class="form-control" type="text" name="access_token">
                                                            </td>
                                                            <td scope="row" valign="top">
                                                                <label for="wpwhprotest_<?php echo $action['action']; ?>_access_token">
                                                                    <strong>access_token</strong>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <p class="description">
                                                                    <?php echo WPWHPRO()->helpers->translate( 'This is a static input field. You only need to set it in case you activated the access_token functionality within the webhook settings.', 'wpwhpro-page-actions' ); ?>
                                                                </p>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                                <input type="submit" name="submit" id="submit" class="btn btn-primary" value="<?php echo WPWHPRO()->helpers->translate( 'Test action', 'admin-settings' ) ?>">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
	<?php else : ?>
        <div class="wpwhpro-empty">
		    <?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any actions activated. Please go to our settings tab and activate some.', 'wpwhpro-page-actions' ); ?>
        </div>
	<?php endif; ?>
</div>

<p>
    <small>* <?php echo WPWHPRO()->helpers->translate( 'Required fields.', 'wpwhpro-page-actions' ); ?></small>
</p>
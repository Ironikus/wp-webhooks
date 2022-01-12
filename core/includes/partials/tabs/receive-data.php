<?php

$webhooks = WPWHPRO()->webhook->get_hooks( 'action' );
$current_url = WPWHPRO()->helpers->get_current_url(false);
$clear_form_url = WPWHPRO()->helpers->get_current_url();
$action_nonce_data = WPWHPRO()->settings->get_action_nonce();
$actions = WPWHPRO()->webhook->get_actions();
$authentication_templates = WPWHPRO()->auth->get_auth_templates();

if( ! empty( $actions ) ){
    usort($actions, function($a, $b) {
        $aname = isset( $a['name'] ) ? $a['name'] : '';
        $bname = isset( $b['name'] ) ? $b['name'] : '';
        return strcmp($aname, $bname);
    });
}

//Validate templates to only allow available features
$allowed_auth_methods = array(
    'api_key',
    'basic_auth',
);
foreach( $authentication_templates as $template_key => $template_data ){
    if( isset( $template_data->auth_type ) && ! in_array( $template_data->auth_type, $allowed_auth_methods ) ){
        unset( $authentication_templates[ $template_key ] );
    }
}

if( isset( $_POST['ironikus-webhook-action-name'] ) ){
    if ( check_admin_referer( $action_nonce_data['action'], $action_nonce_data['arg'] ) ) {
		$webhook_slug = str_replace( '§', '', $_POST['ironikus-webhook-action-name'] );
		$webhook_slug = sanitize_title( $webhook_slug );

        if( strpos( $webhook_slug, 'wpwh-flow-' ) !== FALSE && substr( $webhook_slug, 0, 10 ) === 'wpwh-flow-' ){
            echo WPWHPRO()->helpers->create_admin_notice( 'Please adjust your webhook name as this notation is reserved for internal use only.', 'warning', true );
        } else {
            if( ! isset( $webhooks[ $webhook_slug ] ) ){
                $check = WPWHPRO()->webhook->create( $webhook_slug, 'action' );
    
                if( $check ){
                    echo WPWHPRO()->helpers->create_admin_notice( 'The webhook URL has been added.', 'success', true );
                } else {
                    echo WPWHPRO()->helpers->create_admin_notice( 'Error while adding the webhook URL.', 'warning', true );
                }
    
                //Reload webhooks
                $webhooks = WPWHPRO()->webhook->get_hooks( 'action' );
            }
        }
		
	}
}

//Sort webhooks
$grouped_actions = array();
$grouped_actions_pro = array();

foreach( $actions as $identkey => $webhook_action ){
    $group = 'ungrouped';

    if( isset( $webhook_action['integration'] ) ){
        $group = $webhook_action['integration'];
    }

    if( isset( $webhook_action['premium'] ) && $webhook_action['premium'] ){
        if( ! isset( $grouped_actions_pro[ $group ] ) ){
            $grouped_actions_pro[ $group ] = array(
                $identkey => $webhook_action
            );
        } else {
            $grouped_actions_pro[ $group ][ $identkey ] = $webhook_action;
        }
    } else {
        if( ! isset( $grouped_actions[ $group ] ) ){
            $grouped_actions[ $group ] = array(
                $identkey => $webhook_action
            );
        } else {
            $grouped_actions[ $group ][ $identkey ] = $webhook_action;
        }
    }

}

//add ungroped elements at the end
if( isset( $grouped_actions['ungrouped'] ) ){
	$ungrouped_actions = $grouped_actions['ungrouped'];
	unset( $grouped_actions['ungrouped'] );
	$grouped_actions['ungrouped'] = $ungrouped_actions;
}

//Map premium actions
if( ! empty( $grouped_actions_pro ) ){
	foreach( $grouped_actions_pro as $gtpk => $gtpv ){
		if( isset( $grouped_actions[ $gtpk ] ) ){
			$grouped_actions[ $gtpk ] = array_merge( $grouped_actions[ $gtpk ], $gtpv );
		} else {
			$grouped_actions[ $gtpk ] = $gtpv;
		}
	}
}

$active_trigger = isset( $_GET['wpwh-action'] ) ? filter_var( $_GET['wpwh-action'], FILTER_SANITIZE_STRING ) : 'create_user';

if ( empty( $active_trigger ) ) {
    $active_trigger = isset( $_GET['wpwh-trigger'] ) ? filter_var( $_GET['wpwh-trigger'], FILTER_SANITIZE_STRING ) : 'create_user';
}

?>
<?php add_ThickBox(); ?>

<style>
.integration-pro {
    background-color: #ff8e6b;
    background: linear-gradient(
180deg
,#ff8e6b 0,#f1592a 100%);
    color: #fff;
    padding: 2px 10px;
    border-radius: 50px;
	margin-left:10px;
}
.wpwh-go-pro{
	background-color: rgba(42,157,143,0.1);
    padding: 1.5rem 1.875rem;
    border-radius: 8px;
    margin-top:20px;
}
</style>

<div class="wpwh-container">
  <div class="wpwh-title-area mb-5">
    <h2><?php echo sprintf( WPWHPRO()->helpers->translate( 'Receive Data On %s', 'wpwhpro-page-actions' ), WPWH_NAME ); ?></h2>
  </div>

  <nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <a class="nav-link active" id="nav-webhook-urls-tab" data-toggle="tab" href="#nav-webhook-urls" role="tab" aria-controls="nav-webhook-urls" aria-selected="true"><?php echo WPWHPRO()->helpers->translate( 'Webhooks URLs', 'wpwhpro-page-actions' ); ?></a>
      <a class="nav-link" id="nav-webhook-actions-tab" data-toggle="tab" href="#nav-webhook-actions" role="tab" aria-controls="nav-webhook-actions" aria-selected="false"><?php echo WPWHPRO()->helpers->translate( 'Webhooks Actions', 'wpwhpro-page-actions' ); ?></a>
    </div>
  </nav>

  <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-webhook-urls" role="tabpanel" aria-labelledby="nav-webhook-urls-tab">
        <div class="wpwh-content">
            <p class="mb-4">
                <?php echo sprintf(WPWHPRO()->helpers->translate( 'Use the webhook URL down below to connect your external service with your site. This URL receives data from external endpoints and does certain actions on your WordPress site. Please note, that deleting the default main webhook creates automatically a new one. If you need more information, check out the installation and documentation by clicking <a class="text-secondary" href="%s" target="_blank" >here</a>.', 'wpwhpro-page-actions' ), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-wp-webhooks/'); ?>
            </p>
        </div>
        <div class="wpwh-table-container" data-wpwh-webook-search>
          <div class="wpwh-table-header d-flex align-items-center justify-content-between">
            <h2 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Webhook Action URLs', 'wpwhpro-page-actions' ); ?></h2>
            <div>
                <button class="wpwh-btn wpwh-btn--secondary" title="<?php echo WPWHPRO()->helpers->translate( 'Create Webhook URL', 'wpwhpro-page-actions' ); ?>" data-toggle="modal" data-target="#wpwhCreateActionModal">
                    <?php echo WPWHPRO()->helpers->translate( 'Create Webhook URL', 'wpwhpro-page-actions' ); ?>
                </button>
                <!-- <span data-tippy data-tippy-offset="[0, 20]" data-tippy-content="Search webhook name or URL and press enter"><input type="text" class="wpwh-form-input wpwh-webhook-search__input" placeholder="search webhook and press enter..." data-wpwh-webhook-search></span> -->
            </div>
          </div>
          <table class="wpwh-table wpwh-table--sm">
            <thead>
              <tr>
                <th></th>
                <th><?php echo WPWHPRO()->helpers->translate( 'Name', 'wpwhpro-page-actions' ); ?></th>
                <th><?php echo WPWHPRO()->helpers->translate( 'URL', 'wpwhpro-page-actions' ); ?></th>
                <th><?php echo WPWHPRO()->helpers->translate( 'API Key', 'wpwhpro-page-actions' ); ?></th>
                <th class="text-center"><?php echo WPWHPRO()->helpers->translate( 'Actions', 'wpwhpro-page-actions' ); ?></th>
              </tr>
            </thead>
            <tbody>
                <?php foreach( $webhooks as $webhook => $webhook_data ) :
                    $uid = $webhook;

                    if( strpos( $uid, 'wpwh-flow-' ) !== FALSE && substr( $uid, 0, 10 ) === 'wpwh-flow-' ){
                        continue;
                    }

                    //Map default action_attributes if available
                    $settings = array();
                    if( ! empty( $webhook_data['settings'] ) ){

                        if( isset( $webhook_data['settings']['data'] ) ){
                            $settings = (array) $webhook_data['settings']['data'];
                        }

                        if( isset( $webhook_data['settings']['load_default_settings'] ) && $webhook_data['settings']['load_default_settings'] === true ){
                            // $settings = array_merge( WPWHPRO()->settings->get_default_action_settings(), $settings );
                        }

                    }

                    //Map dynamic settings
                    $required_settings = WPWHPRO()->settings->get_required_action_settings();
                    foreach( $required_settings as $settings_ident => $settings_data ){

                        if( $settings_ident == 'wpwhpro_action_authentication' ){
                            if( ! empty( $authentication_templates ) ){
                                $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
                            } else {
                                unset( $required_settings[ $settings_ident ] ); //if empty
                            }
                        }

                    }

                    $settings = array_merge( $required_settings, $settings );

                    $status = 'active';
                    $status_name = 'Deactivate';
                    if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
                        $status = 'inactive';
                        $status_name = 'Activate';
                    }
                    ?>
                    <tr id="webhook-action-<?php echo $webhook; ?>" class="is-<?php echo $status; ?>" data-wpwh-webhook-search-name="<?php echo $webhook; ?>" data-wpwh-webhook-search-url="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>">
                        <td class="align-middle wpwh-status-cell wpwh-status-cell--<?php echo $status; ?>"><span data-tippy data-tippy-content="<?php echo WPWHPRO()->helpers->translate( $status, 'wpwhpro-page-actions' ); ?>"></span></td>
                        <td class="align-middle"><?php echo $webhook; ?></td>
                        <td>
                            <div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'copied!', 'wpwhpro-page-actions' ); ?>"><input class="wpwh-form-input w-100" type="text" value="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>" readonly /></div>
                        </td>
                        <td>
                            <div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'copied!', 'wpwhpro-page-actions' ); ?>"><input class="wpwh-form-input w-100" type="text" value="<?php echo $webhook_data['api_key']; ?>" readonly /></div>
                        </td>
                        <td class="py-0 align-middle text-center" style="width:100px;">
                            <div class="dropdown">
                            <button class="wpwh-btn wpwh-btn--link px-2 py-3 wpwh-dropdown-trigger" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/settings.svg'; ?>" alt="Settings Icon">
                                <span class="sr-only"><?php echo WPWHPRO()->helpers->translate( 'Options', 'wpwhpro-page-actions' ); ?></span>
                            </button>
                            <div class="dropdown-menu">
                                <a
                                    href="#"
                                    class="dropdown-item"

                                    data-wpwh-event="delete"
                                    data-wpwh-event-type="receive"
                                    data-wpwh-event-element="#webhook-action-<?php echo $webhook; ?>"

                                    data-wpwh-webhook-slug="<?php echo $webhook; ?>"
                                >
                                    <img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
                                    <span><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-actions' ); ?></span>
                                </a>
                                <a
                                    href="#"
                                    class="dropdown-item"

                                    data-wpwh-event="deactivate"
                                    data-wpwh-event-type="receive"
                                    data-wpwh-event-element="#webhook-action-<?php echo $webhook; ?>"

                                    data-wpwh-webhook-status="<?php echo $status; ?>"
                                    data-wpwh-webhook-slug="<?php echo $webhook; ?>"
                                >
                                    <img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/deactivate.svg'; ?>" alt="Deactivate" class="img-deactivate" <?php if ( $status === 'inactive' ): ?> style="display:none;" <?php endif; ?>>
                                    <img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/activate.svg'; ?>" alt="Activate" class="img-activate" <?php if ( $status === 'active' ): ?> style="display:none;" <?php endif; ?>>
                                    <span><?php echo WPWHPRO()->helpers->translate( $status_name, 'wpwhpro-page-actions' ); ?></span>
                                </a>
                                <button class="dropdown-item wpwh-action-settings-wrapper" title="<?php echo $webhook; ?>" data-toggle="modal" data-target="#wpwhActionSettings<?php echo $webhook; ?>">
                                    <img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="Settings">
                                    <span><?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-actions' ); ?></span>
                                </button>
                            </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
          </table>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-webhook-actions" role="tabpanel" aria-labelledby="nav-webhook-actions-tab">
        <p><?php echo WPWHPRO()->helpers->translate( 'Below you will find a list of all available webhook actions that you can use to send data from an external service to your WordPress site.', 'wpwhpro-page-actions' ); ?></p>
        <div class="wpwh-triggers" data-wpwh-trigger="">
          <div class="wpwh-triggers__sidebar">
            <div class="wpwh-trigger-search wpwh-box">
              <div class="wpwh-trigger-search__search">
                <input type="search" data-wpwh-trigger-search class="wpwh-form-input" name="search-trigger" id="search-trigger" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Search actions', 'wpwhpro-page-actions' ); ?>">
              </div>
              <div class="wpwh-trigger-search__items">
                <?php if( ! empty( $actions ) ) : ?>
                    <?php foreach( $grouped_actions as $group => $single_actions ) :

                    if( $group === 'ungrouped' ){
                        echo '<a class="wpwh-trigger-search__item wpwh-trigger-search__item--group">' . WPWHPRO()->helpers->translate( 'Others', 'wpwhpro-page-actions' ) . '</a>';
                    } else {
                        $group_details = WPWHPRO()->integrations->get_details( $group );
                        if( is_array( $group_details ) && isset( $group_details['name'] ) && ! empty( $group_details['name'] ) ){
                            echo '<a class="wpwh-trigger-search__item wpwh-trigger-search__item--group wpwh-trigger-search__item--group-icon">';

                            if( isset( $group_details['icon'] ) && ! empty( $group_details['icon'] ) ){
                                echo '<img class="wpwh-trigger-search__item-image" src="' . $group_details['icon'] . '" />';
                            }

                            echo '<span class="wpwh-trigger-search__item-name">' . $group_details['name'] . '</span>';
                            echo '</a>';
                        }
                    }

                    ?>
                        <?php $i = 0; foreach( $single_actions as $identkey => $action ) :
                            $is_active = $action['action'] === $active_trigger;
                            $action_name = isset( $action['name'] ) ? $action['name'] : $action['action'];

                            if( isset( $action['premium'] ) && $action['premium'] ){
                                $action_name .= '<span class="integration-pro">Pro</span>';
                            }
                        ?>
                            <a href="#webhook-action-<?php echo $action['action']; ?>" data-wpwh-trigger-id="<?php echo $action['action']; ?>" class="wpwh-trigger-search__item<?php echo $is_active ? ' wpwh-trigger-search__item--active' : ''; ?>"><?php echo $action_name; ?></a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="wpwh-triggers__content" data-wpwh-trigger-content="">
              <div class="wpwh-trigger-items">
                <?php if( ! empty( $actions ) ) : ?>
                    <?php foreach( $actions as $identkey => $action ) :
                        $is_active = $action['action'] === $active_trigger;
                        $is_premium = ( isset( $action['premium'] ) && $action['premium'] ) ? true : false;
                        $action_integration = isset( $action['integration'] ) ? $action['integration'] : '';
						$action_details = WPWHPRO()->integrations->get_details( $action_integration );

						$action_integration_icon = '';
						if( isset( $action_details['icon'] ) && ! empty( $action_details['icon'] ) ){
							$action_integration_icon = esc_html( $action_details['icon'] );
						}

						$trigger_integration_name = '';
						if( isset( $action_details['name'] ) && ! empty( $action_details['name'] ) ){
							$trigger_integration_name = esc_html( $action_details['name'] );
						}
                    ?>
                        <div class="wpwh-trigger-item<?php echo $is_active ? ' wpwh-trigger-item--active' : ''; ?> wpwh-table-container" id="webhook-action-<?php echo $action['action']; ?>">
                            <div class="wpwh-table-header">
                                <div class="mb-2 d-flex align-items-center justify-content-between">
									<h2 class="d-flex align-items-end" data-wpwh-trigger-name>
										<?php if( ! empty( $action_integration_icon ) ) : ?>
											<img class="wpwh-trigger-search__item-image mb-1" src="<?php echo $action_integration_icon; ?>" />
										<?php endif; ?>
										<div class="d-flex flex-column">
											<span class="wpwh-trigger-integration-name wpwh-text-small"><?php echo $trigger_integration_name; ?></span>
											<?php echo isset( $action['name'] ) ? $action['name'] : $action['action']; ?>
										</div>
									</h2>
									<div class="wpwh-trigger-webhook-name wpwh-text-small"><?php echo $action['action']; ?></div>
								</div>
                                <div class="wpwh-content mb-0">
                                    <?php echo $action['short_description']; ?>
								</div>
                                <?php if( $is_premium ) : ?>
									<div class="wpwh-go-pro">
										<h4 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Interested in this action?', 'wpwhpro-page-actions' ); ?></h4>
										<p>
											<?php echo sprintf( WPWHPRO()->helpers->translate( 'Get full access to this and all other premium actions with %s', 'wpwhpro-page-triggers' ), '<strong>' . $this->page_title . ' Pro</strong>' ); ?>
										</p>
										<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=action-prem-<?php echo urlencode( $action['action'] ); ?>&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo WPWHPRO()->helpers->translate( 'Go Pro', 'wpwhpro-page-actions' ); ?>">
											<?php echo WPWHPRO()->helpers->translate( 'Go Pro', 'wpwhpro-page-actions' ); ?>
										</a>
									</div>
								<?php endif; ?>
                            </div>
                            <div class="wpwh-accordion" id="wpwh_accordion_1">
                                <div class="wpwh-accordion__item border-top-0 pt-0">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_arguments_<?php echo $action['action']; ?>" aria-expanded="true" aria-controls="wpwh_accordion_arguments_<?php echo $action['action']; ?>">
                                        <span><?php echo WPWHPRO()->helpers->translate( 'Accepted arguments', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo WPWHPRO()->helpers->translate( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo WPWHPRO()->helpers->translate( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_arguments_<?php echo $action['action']; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
                                        <table class="wpwh-table wpwh-text-small">
                                            <thead>
                                                <tr>
                                                    <th><?php echo WPWHPRO()->helpers->translate( 'Argument', 'wpwhpro-page-actions' ); ?></th>
                                                    <th><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-actions' ); ?></th>
                                                    <th><?php echo WPWHPRO()->helpers->translate( 'More', 'wpwhpro-page-actions' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="wpwh-is-required">
                                                    <td class="wpwh-w-25"><strong class="text-lg">action</strong><br><span class="text-primary"><?php echo WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions' ); ?></span></td>
                                                    <td><?php echo WPWHPRO()->helpers->translate( 'Always required. Determines which webhook action you want to target. (Alternatively, set this value as a query parameter within the URL). For this webhook action, please set it to ', 'wpwhpro-page-actions'); ?><strong><?php echo $action['action']; ?></strong></td>
                                                    <td>
                                                        <a
															class="action-argument-details-<?php echo $action['action']; ?>"
															href="#"
															data-toggle="modal"
															data-target="#wpwhaction-argument-detail-modal-<?php echo $action['action']; ?>-action"
														>
															<span><?php echo WPWHPRO()->helpers->translate( 'Details', 'wpwhpro-page-triggers' ); ?></span>
														</a>
                                                    </td>
                                                </tr>
                                                <?php foreach( $action['parameter'] as $param => $param_data ) : ?>
                                                    <tr <?php if( ! empty( $param_data['required'] ) ) { echo 'class="wpwh-is-required"'; } ; ?>>
                                                        <td class="wpwh-w-25"><strong class="text-lg"><?php echo $param; ?></strong>
                                                            <?php if( ! empty( $param_data['required'] ) ) : ?>
                                                                <br><span class="text-primary"><?php echo WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions' ); ?></span>
                                                            <?php endif; ?>
                                                            <?php if( isset( $param_data['premium'] ) && $param_data['premium'] ) : ?>
                                                                <span class="integration-pro">Pro</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $param_data['short_description']; ?></td>
                                                        <td>
                                                            <?php if( isset( $param_data['description'] ) && ! empty( $param_data['description'] ) ) : ?>
                                                                <a
                                                                    class="action-argument-details-<?php echo $action['action']; ?>"
                                                                    href="#"
                                                                    data-toggle="modal"
                                                                    data-target="#wpwhaction-argument-detail-modal-<?php echo $action['action']; ?>-<?php echo $param; ?>"
                                                                >
                                                                    <span><?php echo WPWHPRO()->helpers->translate( 'Details', 'wpwhpro-page-triggers' ); ?></span>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_return_values_<?php echo $action['action']; ?>" aria-expanded="true" aria-controls="wpwh_accordion_return_values_<?php echo $action['action']; ?>">
                                        <span><?php echo WPWHPRO()->helpers->translate( 'Return values', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo WPWHPRO()->helpers->translate( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo WPWHPRO()->helpers->translate( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_return_values_<?php echo $action['action']; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingTwo">
                                        <?php if( ! empty( $action['returns'] ) ) : ?>
                                            <table class="wpwh-table wpwh-text-small mb-4">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo WPWHPRO()->helpers->translate( 'Argument', 'wpwhpro-page-actions' ); ?></th>
                                                        <th><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-actions' ); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach( $action['returns'] as $param => $param_data ) : ?>
                                                        <tr>
                                                            <th class="wpwh-text-left wpwh-w-25"><strong class="text-lg"><?php echo $param; ?></strong></th>
                                                            <td><?php echo $param_data['short_description']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                            <?php if( ! empty( $action['returns_code'] ) ) :

                                                $display_code = $action['returns_code'];
                                                if( is_array( $action['returns_code'] ) ){
                                                    $display_code = '<pre>' . htmlspecialchars( json_encode( $display_code, JSON_PRETTY_PRINT ) ) . '</pre>';
                                                }

                                            ?>
                                                <div class="wpwh-content">
                                                    <p>
                                                        <?php echo WPWHPRO()->helpers->translate( 'Here is an example of all the available fields. The fields may vary based on custom extensions, third party plugins or different values.', 'wpwhpro-page-actions'); ?>
                                                    </p>
                                                    <?php echo $display_code; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_description_<?php echo $action['action']; ?>" aria-expanded="true" aria-controls="wpwh_accordion_description_<?php echo $action['action']; ?>">
                                        <span><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo WPWHPRO()->helpers->translate( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo WPWHPRO()->helpers->translate( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_description_<?php echo $action['action']; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingThree">
                                        <div class="wpwh-content">
                                            <?php echo wpautop( $action['description'] ); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if( ! $is_premium ) : ?>
                                <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_test_action_<?php echo $action['action']; ?>" aria-expanded="true" aria-controls="wpwh_accordion_test_action_<?php echo $action['action']; ?>">
                                        <span><?php echo WPWHPRO()->helpers->translate( 'Test action', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo WPWHPRO()->helpers->translate( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo WPWHPRO()->helpers->translate( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_test_action_<?php echo $action['action']; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingFour">
                                        <div class="wpwh-content">
                                            <p>
                                                <?php echo WPWHPRO()->helpers->translate( 'Here you can test the specified webhook. Please note, that this test can modify the data of your website (Depending on what action you test). Also, you will see the response as any web service receives it.', 'wpwhpro-page-actions'); ?>
                                            </p>
                                            <p>
                                                <?php echo WPWHPRO()->helpers->translate( 'Please choose the webhook you are going to run the test with. Simply select the one you want to use down below.', 'wpwhpro-page-actions'); ?>
                                            </p>
                                            <select
                                                class="wpwh-form-input wpwh-webhook-receive-test-action"
                                                data-wpwh-identkey="<?php echo $action['action']; ?>"
                                                data-wpwh-target="#wpwh-action-testing-form-<?php echo $action['action']; ?>"
                                            >
                                                <option value="empty"><?php echo WPWHPRO()->helpers->translate( 'Choose action...', 'wpwhpro-page-data-mapping' ); ?></option>
                                                <?php if( ! empty( $webhooks ) ) : ?>
                                                    <?php foreach( $webhooks as $subwebhook => $subwebhook_data ) : 
                                                    
                                                    if( strpos( $subwebhook, 'wpwh-flow-' ) !== FALSE && substr( $subwebhook, 0, 10 ) === 'wpwh-flow-' ){
                                                        continue;
                                                    }  

                                                    ?>
                                                        <option class="<?php echo $subwebhook; ?>" value="<?php echo WPWHPRO()->webhook->built_url( $subwebhook, $subwebhook_data['api_key'] ) . '&wpwhpro_direct_test=1'; ?>"><?php echo $subwebhook; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <form id="wpwh-action-testing-form-<?php echo $action['action']; ?>" method="post" class="wpwh-actions-testing-form mt-4" action="" target="_blank" style="display:none;">
                                                <table class="wpwh-table wpwh-table--in-content">
                                                    <tbody>
                                                        <tr valign="top">
                                                            <td>
                                                                <input id="wpwhprotest_<?php echo $action['action']; ?>_action" class="wpwh-form-input" type="text" name="action" value="<?php echo $action['action']; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions'); ?>">
                                                            </td>
                                                            <td scope="row" valign="top">
                                                                <label for="wpwhprotest_<?php echo $action['action']; ?>_action">
                                                                    <strong>action</strong>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <?php echo WPWHPRO()->helpers->translate( 'Always required. This argument determines which webhook you want to target. For this webhook action, please set it to ', 'wpwhpro-page-actions'); ?><strong><?php echo $action['action']; ?></strong>
                                                            </td>
                                                        </tr>
                                                        <?php foreach( $action['parameter'] as $param => $param_data ) : ?>
                                                            <tr valign="top">
                                                                <td>
                                                                    <input id="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>" class="wpwh-form-input" type="text" name="<?php echo $param; ?>" placeholder="<?php echo ( ! empty( $param_data['required'] ) ) ? WPWHPRO()->helpers->translate( 'Required', 'wpwhpro-page-actions') : '' ?>">
                                                                </td>
                                                                <td scope="row" valign="top">
                                                                    <label for="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>">
                                                                        <strong><?php echo $param; ?></strong>
                                                                    </label>
                                                                </td>
                                                                <td>
                                                                    <?php echo $param_data['short_description']; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr valign="top">
                                                            <td>
                                                                <input id="wpwhprotest_<?php echo $action['action']; ?>_access_token" class="wpwh-form-input" type="text" name="access_token">
                                                            </td>
                                                            <td scope="row" valign="top">
                                                                <label for="wpwhprotest_<?php echo $action['action']; ?>_access_token">
                                                                    <strong>access_token</strong>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <?php echo WPWHPRO()->helpers->translate( 'This is a static input field. You only need to set it in case you activated the access_token functionality within the webhook settings.', 'wpwhpro-page-actions' ); ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="wpwh-text-center my-3">
                                                    <input type="submit" name="submit" id="submit-<?php echo $action['action']; ?>" class="wpwh-btn wpwh-btn--secondary" value="<?php echo WPWHPRO()->helpers->translate( 'Test action', 'admin-settings' ) ?>">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
              </div>
          </div>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="wpwhCreateActionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Create Webhook URL', 'wpwhpro-page-actions' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh_webhook_action_name"><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-actions' ); ?></label>
          <input class="wpwh-form-input w-100" type="text" id="wpwh_webhook_action_name" name="ironikus-webhook-action-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Enter webhook name', 'wpwhpro-page-actions' ); ?>" />
        </div>
        <div class="modal-footer">
          <?php echo WPWHPRO()->helpers->get_nonce_field( $action_nonce_data ); ?>
          <input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo WPWHPRO()->helpers->translate( 'Create', 'wpwhpro-page-actions' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<?php foreach( $webhooks as $webhook => $webhook_data ) :
    $uid = $webhook;

    //Map default action_attributes if available
    $settings = array();
    if( ! empty( $webhook_data['settings'] ) ){

        if( isset( $webhook_data['settings']['data'] ) ){
            $settings = (array) $webhook_data['settings']['data'];
        }

        if( isset( $webhook_data['settings']['load_default_settings'] ) && $webhook_data['settings']['load_default_settings'] === true ){
            // $settings = array_merge( WPWHPRO()->settings->get_default_action_settings(), $settings );
        }

    }

    //Map dynamic settings
    $required_settings = WPWHPRO()->settings->get_required_action_settings();
    foreach( $required_settings as $settings_ident => $settings_data ){

        if( $settings_ident == 'wpwhpro_action_authentication' ){
            if( ! empty( $authentication_templates ) ){
                $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
            } else {
                unset( $required_settings[ $settings_ident ] ); //if empty
            }
        }

    }

    $settings = array_merge( $required_settings, $settings );

    $status = 'active';
    $status_name = 'Deactivate';
    if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
        $status = 'inactive';
        $status_name = 'Activate';
    }
    ?>
    <div class="modal modal--lg fade" id="wpwhActionSettings<?php echo $webhook; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Action Settings for', 'wpwhpro-page-actions' ); ?> "<?php echo $webhook; ?>"</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 1L1 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1 1L13 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <strong class="mr-4 flex-shrink-0">Webhook url:</strong>
                        <input type="text" class="wpwh-form-input wpwh-w-100" value="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'] ); ?>" readonly>
                    </div>
                    <div class="ironikus-tb-webhook-settings">
                        <?php if( $settings ) : ?>
                            <form id="ironikus-webhook-action-form-<?php echo $webhook; ?>">
                                <table class="wpwh-table wpwh-table--sm mb-4">
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
                                                        <?php if ( $setting['type'] === 'text' ): ?>
                                                            <input class="wpwh-form-input wpwh-w-100" id="wpwh-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" value="<?php echo $value; ?>" style="min-width:170px;" />
                                                        <?php else: ?>
                                                            <input id="wpwh-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
                                                        <?php endif; ?>
                                                    <?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
                                                        <select class="wpwh-form-input wpwh-w-100" name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?> style="min-width:170px;">
                                                            <?php
                                                                if( isset( $settings_data[ $setting_name ] ) ){
                                                                    $settings_data[ $setting_name ] = ( is_array( $settings_data[ $setting_name ] ) ) ? array_flip( $settings_data[ $setting_name ] ) : $settings_data[ $setting_name ];
                                                                }
                                                            ?>
                                                            <?php foreach( $setting['choices'] as $choice_name => $choice_label ) : 

                                                                    //Compatibility with 4.3.0
                                                                    if( is_array( $choice_label ) ){
                                                                        if( isset( $choice_label['label'] ) ){
                                                                            $choice_label = $choice_label['label'];
                                                                        } else {
                                                                            $choice_label = $choice_name;
                                                                        }
                                                                    }

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

                                                                    } else {
                                                                        //Make sure we also cover webhooks that settings haven't been saved yet
                                                                        if( $choice_name === $value ){
                                                                            $selected = 'selected="selected"';
                                                                        }
                                                                    }
                                                                ?>
                                                                <option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo WPWHPRO()->helpers->translate( $choice_label, 'wpwhpro-page-actions' ); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php endif; ?>
                                                </td>
                                                <td scope="row" valign="top">
                                                    <label class="wpwh-form-label" for="wpwh-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>">
                                                        <strong><?php echo $setting['label']; ?></strong>
                                                    </label>
                                                </td>
                                                <td><?php echo $setting['description']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <button
                                    type="button"
                                    class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"

                                    data-wpwh-event="save"
                                    data-wpwh-event-type="receive"
                                    data-wpwh-event-element="wpwhActionSettings<?php echo $webhook; ?>"

                                    data-webhook-id="<?php echo $webhook; ?>"
                                >
                                    <span><?php echo WPWHPRO()->helpers->translate( 'Save Settings', 'wpwhpro-page-actions' ); ?></span>
                                </button>
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
    </div>
<?php endforeach; ?>

<!-- Action argument modals -->
<?php if( ! empty( $actions ) ) : ?>
    <?php foreach( $actions as $identkey => $action ) :
        $is_active = $action['action'] === $active_trigger;
    ?>

        <div class="modal modal--lg fade" id="wpwhaction-argument-detail-modal-<?php echo $action['action']; ?>-action" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Details for:', 'wpwhpro-page-actions' ); ?> action</h3>
                </div>
                <div class="modal-body">
                    <p><?php echo WPWHPRO()->helpers->translate( 'This argument is always required since it tells our plugin which wehook endpoint you are calling.', 'wpwhpro-page-actions' ); ?></p>
                    <p><?php echo sprintf( WPWHPRO()->helpers->translate( 'The argument can be defined within the URL as a query parameter (<code>&action=%s</code>), or within the payload (the real data of your request). Within external services such as Integromat, Pabbly or Zapier, we offer a predefined field for the argument.', 'wpwhpro-page-actions' ), $action['action'] ); ?></p>
                    <p><?php echo WPWHPRO()->helpers->translate( 'For this webhook action, please set the <strong>action</strong> argument to', 'wpwhpro-page-actions' ); ?> <strong><?php echo $action['action']; ?></strong></p>
                </div>
                </div>
            </div>
        </div>

        <?php foreach( $action['parameter'] as $param => $param_data ) :

            if( ! isset( $param_data['description'] ) || empty( $param_data['description'] ) ){
                continue;
            }

        ?>
            <div class="modal modal--lg fade" id="wpwhaction-argument-detail-modal-<?php echo $action['action']; ?>-<?php echo $param; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Details for:', 'wpwhpro-page-actions' ); ?> <?php echo $param; ?></h3>
                    </div>
                    <?php if( isset( $param_data['premium'] ) && $param_data['premium'] ) : ?>
                        <div class="wpwh-go-pro">
                            <h4 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Interested in this argument?', 'wpwhpro-page-actions' ); ?></h4>
                            <p>
                                <?php echo sprintf( WPWHPRO()->helpers->translate( 'Get full access to this and all other premium arguments with %s', 'wpwhpro-page-triggers' ), '<strong>' . $this->page_title . ' Pro</strong>' ); ?>
                            </p>
                            <a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=action-prem-<?php echo urlencode( $action['action'] ) . '-arg-' . urlencode( $param ); ?>&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo WPWHPRO()->helpers->translate( 'Go Pro', 'wpwhpro-page-actions' ); ?>">
                                <?php echo WPWHPRO()->helpers->translate( 'Go Pro', 'wpwhpro-page-actions' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="modal-body">
                        <?php echo wpautop( $param_data['description'] ); ?>
                    </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endforeach; ?>
<?php endif; ?>
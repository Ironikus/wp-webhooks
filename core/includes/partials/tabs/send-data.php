<?php
$triggers = WPWHPRO()->webhook->get_triggers();
$triggers_data = WPWHPRO()->webhook->get_hooks( 'trigger' );
$current_url = WPWHPRO()->helpers->get_current_url(false);
$current_url_full = WPWHPRO()->helpers->get_current_url();
$trigger_nonce_data = WPWHPRO()->settings->get_trigger_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url();
$authentication_templates = WPWHPRO()->auth->get_auth_templates();

if( ! empty( $triggers ) ){
    usort($triggers, function($a, $b) {
        $aname = isset( $a['name'] ) ? $a['name'] : '';
        $bname = isset( $b['name'] ) ? $b['name'] : '';
        return strcmp($aname, $bname);
    });
}

if( isset( $_POST['wpwh-add-webhook-url'] ) ){
    if ( check_admin_referer( $trigger_nonce_data['action'], $trigger_nonce_data['arg'] ) ) {

		$percentage_escape		= '{irnksescprcntg}';
		$webhook_url            = $_POST['wpwh-add-webhook-url'];
		$webhook_url 			= str_replace( '%', $percentage_escape, $webhook_url );
		$webhook_url 			= sanitize_text_field( $webhook_url );
		$webhook_url 			= str_replace( $percentage_escape, '%', $webhook_url );

		if( $webhook_url !== 'wpwhflow' ){
			$webhook_slug            = isset( $_POST['wpwh-add-webhook-name'] ) ? sanitize_title( $_POST['wpwh-add-webhook-name'] ) : '';
			$webhook_group          = isset( $_POST['wpwh-add-webhook-group'] ) ? sanitize_text_field( $_POST['wpwh-add-webhook-group'] ) : '';
			$webhooks               = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
	
			if( ! empty( $webhook_slug ) ){
				$new_webhook = $webhook_slug;
			} else {
				$new_webhook = strtotime( date( 'Y-n-d H:i:s' ) ) . 999 . rand( 10, 9999 );
			}
	
			if( ! isset( $webhooks[ $new_webhook ] ) ){
				$check = WPWHPRO()->webhook->create( $new_webhook, 'trigger', array( 'group' => $webhook_group, 'webhook_url' => $webhook_url ) );
	
				if( $check ){
					echo WPWHPRO()->helpers->create_admin_notice( 'The webhook URL has been added.', 'success', true );
				} else {
					echo WPWHPRO()->helpers->create_admin_notice( 'Error while adding the webhook URL.', 'warning', true );
				}
	
				//reload data
				$triggers = WPWHPRO()->webhook->get_triggers();
				$triggers_data = WPWHPRO()->webhook->get_hooks( 'trigger' );
			}
		} else {
			echo WPWHPRO()->helpers->create_admin_notice( 'This webhook URL is reserved for internal use only.', 'warning', true );
		}

        

	}
}

//Sort webhooks
$grouped_triggers = array();
$grouped_triggers_pro = array();
foreach( $triggers as $identkey => $webhook_trigger ){
    $group = 'ungrouped';

    if( isset( $webhook_trigger['integration'] ) ){
        $group = $webhook_trigger['integration'];
    }

    if( isset( $webhook_trigger['premium'] ) && $webhook_trigger['premium'] ){
		if( ! isset( $grouped_triggers_pro[ $group ] ) ){
			$grouped_triggers_pro[ $group ] = array(
				$identkey => $webhook_trigger
			);
		} else {
			$grouped_triggers_pro[ $group ][ $identkey ] = $webhook_trigger;
		}
	} else {
		if( ! isset( $grouped_triggers[ $group ] ) ){
			$grouped_triggers[ $group ] = array(
				$identkey => $webhook_trigger
			);
		} else {
			$grouped_triggers[ $group ][ $identkey ] = $webhook_trigger;
		}
	}
}

//add ungroped elements at the end
if( isset( $grouped_triggers['ungrouped'] ) ){
	$ungrouped_triggers = $grouped_triggers['ungrouped'];
	unset( $grouped_triggers['ungrouped'] );
	$grouped_triggers['ungrouped'] = $ungrouped_triggers;
}

//Map premium triggers
if( ! empty( $grouped_triggers_pro ) ){
	foreach( $grouped_triggers_pro as $gtpk => $gtpv ){
		if( isset( $grouped_triggers[ $gtpk ] ) ){
			$grouped_triggers[ $gtpk ] = array_merge( $grouped_triggers[ $gtpk ], $gtpv );
		} else {
			$grouped_triggers[ $gtpk ] = $gtpv;
		}
	}
}

$active_trigger = isset( $_GET['wpwh-trigger'] ) ? filter_var( $_GET['wpwh-trigger'], FILTER_SANITIZE_STRING ) : 'create_user';

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
}
</style>

<div class="wpwh-container">
  <div class="wpwh-title-area mb-5">
    <h1><?php echo WPWHPRO()->helpers->translate( 'Available Webhook Triggers', 'wpwhpro-page-triggers' ); ?></h1>
    <p class="wpwh-text-small">
		<?php echo sprintf( WPWHPRO()->helpers->translate( 'Below you will find a list of all available %1$s triggers. To use one, you need to specify a URL that should be triggered to send the available data. For more information on that, you can check out each webhook trigger description or our product documentation by clicking <a class="text-secondary" title="Go to our product documentation" target="_blank" href="%2$s">here</a>.', 'wpwhpro-page-triggers' ), '<strong>' . $this->page_title . '</strong>', 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-wp-webhooks/'); ?>
	</p>
  </div>

  <div class="wpwh-triggers" data-wpwh-trigger="">

    <div class="wpwh-triggers__sidebar">

      <div class="wpwh-trigger-search wpwh-box">
        <div class="wpwh-trigger-search__search">
          <input type="search" data-wpwh-trigger-search class="wpwh-form-input" name="search-trigger" id="search-trigger" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Search triggers', 'wpwhpro-page-triggers' ); ?>">
        </div>
				<?php if( ! empty( $triggers ) ) : ?>
					<div class="wpwh-trigger-search__items">
						<?php foreach( $grouped_triggers as $group => $single_triggers ) :

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
							<?php foreach( $single_triggers as $identkey => $trigger ) :
								$trigger_name = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
								$webhook_name = !empty( $trigger['trigger'] ) ? $trigger['trigger'] : '';

								if( isset( $trigger['premium'] ) && $trigger['premium'] ){
									$trigger_name .= '<span class="integration-pro">Pro</span>';
								}

								$is_active = $webhook_name === $active_trigger;

								?>
								<a href="#webhook-<?php echo $webhook_name; ?>" data-wpwh-trigger-id="<?php echo $webhook_name; ?>" class="wpwh-trigger-search__item<?php echo $is_active ? ' wpwh-trigger-search__item--active' : ''; ?>"><?php echo $trigger_name; ?></a>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
      </div>

    </div>

    <div class="wpwh-triggers__content" data-wpwh-trigger-content="">

		<?php if( ! empty( $triggers ) ) : ?>
				<div class="wpwh-trigger-items">
					<?php foreach( $triggers as $identkey => $trigger ) :

						$trigger_name = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
						$webhook_name = !empty( $trigger['trigger'] ) ? $trigger['trigger'] : '';
						$trigger_integration = isset( $trigger['integration'] ) ? $trigger['integration'] : '';
						$trigger_details = WPWHPRO()->integrations->get_details( $trigger_integration );

						$trigger_integration_icon = '';
						if( isset( $trigger_details['icon'] ) && ! empty( $trigger_details['icon'] ) ){
							$trigger_integration_icon = esc_html( $trigger_details['icon'] );
						}

						$trigger_integration_name = '';
						if( isset( $trigger_details['name'] ) && ! empty( $trigger_details['name'] ) ){
							$trigger_integration_name = esc_html( $trigger_details['name'] );
						}

						$is_active = $webhook_name === $active_trigger;
						$is_premium = ( isset( $trigger['premium'] ) && $trigger['premium'] ) ? true : false;

						//Map default trigger_attributes if available
						$settings = array();
						if( ! empty( $trigger['settings'] ) ){

							if( isset( $trigger['settings']['data'] ) ){
								$settings = (array) $trigger['settings']['data'];
							}

							if( isset( $trigger['settings']['load_default_settings'] ) && $trigger['settings']['load_default_settings'] === true ){
									$settings = array_merge( $settings, WPWHPRO()->settings->get_default_trigger_settings() );
							}
						}

						//Map dynamic settings
						$required_settings = WPWHPRO()->settings->get_required_trigger_settings();
						foreach( $required_settings as $settings_ident => $settings_data ){

							if( $settings_ident == 'wpwhpro_trigger_authentication' ){
								if( ! empty( $authentication_templates ) ){
									$required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
								} else {
									unset( $required_settings[ $settings_ident ] ); //if empty
								}
							}

						}

						$settings = array_merge( $settings, $required_settings );

						?>
						<div class="wpwh-trigger-item<?php echo $is_active ? ' wpwh-trigger-item--active' : ''; ?> wpwh-table-container" id="webhook-<?php echo $webhook_name; ?>" <?php echo ! $is_active ? 'style="display: none;"' : ''; ?>>
							<div class="wpwh-table-header">
								<div class="mb-2 d-flex align-items-center justify-content-between">
									<h2 class="d-flex align-items-end" data-wpwh-trigger-name>
										<?php if( ! empty( $trigger_integration_icon ) ) : ?>
											<img class="wpwh-trigger-search__item-image mb-1" src="<?php echo $trigger_integration_icon; ?>" />
										<?php endif; ?>
										<div class="d-flex flex-column">
											<span class="wpwh-trigger-integration-name wpwh-text-small"><?php echo $trigger_integration_name; ?></span>
											<?php echo $trigger_name; ?>
										</div>
									</h2>
									<div class="wpwh-trigger-webhook-name wpwh-text-small"><?php echo $webhook_name; ?></div>
								</div>
								<div class="wpwh-content mb-4">
									<?php echo $trigger['short_description']; ?>
								</div>
								<?php if( ! $is_premium ) : ?>
									<div class="d-flex align-items-center justify-content-end">
										<button class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo WPWHPRO()->helpers->translate( 'Add Webhook URL', 'wpwhpro-page-triggers' ); ?>" data-toggle="modal" data-target="#wpwhAddWebhookModal-<?php echo $identkey; ?>">
											<?php echo WPWHPRO()->helpers->translate( 'Add Webhook URL', 'wpwhpro-page-triggers' ); ?>
										</button>
									</div>
								<?php else : ?>
									<div class="wpwh-go-pro">
										<h4 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Interested in this trigger?', 'wpwhpro-page-triggers' ); ?></h4>
										<p>
											<?php echo sprintf( WPWHPRO()->helpers->translate( 'Get full access to this and all other premium triggers with %s', 'wpwhpro-page-triggers' ), '<strong>' . $this->page_title . ' Pro</strong>' ); ?>
										</p>
										<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=trigger-prem-<?php echo urlencode( $webhook_name ); ?>&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo WPWHPRO()->helpers->translate( 'Go Pro', 'wpwhpro-page-triggers' ); ?>">
											<?php echo WPWHPRO()->helpers->translate( 'Go Pro', 'wpwhpro-page-triggers' ); ?>
										</a>
									</div>
								<?php endif; ?>
							</div>
							<?php if( ! $is_premium ) : ?>
								<table class="wpwh-table wpwh-table--sm wpwh-text-small">
									<thead>
										<tr>
											<th></th>
											<th><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-triggers' ); ?></th>
											<th><?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-triggers' ); ?></th>
											<th class="text-center"><?php echo WPWHPRO()->helpers->translate( 'Action', 'wpwhpro-page-triggers' ); ?></th>
										</tr>
									</thead>
									<tbody>

										<?php $all_triggers = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger['trigger'] ); ?>
										<?php foreach( $all_triggers as $webhook => $webhook_data ) : ?>
											<?php if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; } ?>
											<?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-triggers' ), $webhook, $trigger['trigger'] ) ) ) { continue; } ?>
											<?php
												$is_flow = ( $webhook_data['webhook_url'] === 'wpwhflow' ) ? true : false;
												if( $is_flow ){
													continue;
												}

												$status = 'active';
												$status_name = 'Deactivate';
												if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
													$status = 'inactive';
													$status_name = 'Activate';
												}
											?>
											<tr id="webhook-trigger-<?php echo $webhook; ?>">
												<td class="align-middle wpwh-status-cell wpwh-status-cell--<?php echo $status; ?>">
													<span data-tippy data-tippy-content="<?php echo WPWHPRO()->helpers->translate( $status, 'wpwhpro-page-triggers' ); ?>"></span>
												</td>
												<td>
													<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'copied!', 'wpwhpro-page-triggers' ); ?>"><input class="wpwh-form-input w-100" type='text' name='ironikus_wp_webhooks_pro_webhook_name' value="<?php echo $webhook; ?>" readonly /></div>
												</td>
												<td class="wpwh-w-50">
													<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'copied!', 'wpwhpro-page-triggers' ); ?>"><input class="wpwh-form-input w-100" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo $webhook_data['webhook_url']; ?>" readonly /></div>
												</td>
												<td class="p-0 align-middle text-center wpwh-table__action">
													<div class="dropdown">
														<button type="button" class="wpwh-btn wpwh-btn--link px-2 py-3 wpwh-dropdown-trigger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/settings.svg'; ?>" alt="Settings Icon">
															<span class="sr-only">Options</span>
														</button>
														<div class="dropdown-menu">
															<a
																class="dropdown-item"
																href="#"

																data-wpwh-event="delete"
																data-wpwh-event-type="send"
																data-wpwh-event-element="#webhook-trigger-<?php echo $webhook; ?>"

																data-wpwh-delete="<?php echo $webhook; ?>"
																data-wpwh-group="<?php echo $trigger['trigger']; ?>"
															>
																<img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
																<span><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-triggers' ); ?></span>
															</a>
															<a
																class="dropdown-item"
																href="#"

																data-wpwh-event="deactivate"
																data-wpwh-event-type="send"
																data-wpwh-event-target="#webhook-action-<?php echo $webhook; ?>"

																data-wpwh-webhook-status="<?php echo $status; ?>"
																data-wpwh-webhook-group="<?php echo $trigger['trigger']; ?>"
																data-wpwh-webhook-slug="<?php echo $webhook; ?>"
															>
																<img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/deactivate.svg'; ?>" alt="Deactivate" class="img-deactivate" <?php if ( $status === 'inactive' ): ?> style="display:none;" <?php endif; ?>>
																<img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/activate.svg'; ?>" alt="Activate" class="img-activate" <?php if ( $status === 'active' ): ?> style="display:none;" <?php endif; ?>>
																<span><?php echo WPWHPRO()->helpers->translate( $status_name, 'wpwhpro-page-triggers' ); ?></span>
															</a>
															<a
																class="dropdown-item"
																href="#"
																data-toggle="modal"
																data-target="#wpwhTriggerSettingsModal-<?php echo $identkey; ?>-<?php echo $webhook; ?>"
															>
																<img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="Settings">
																<span><?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-triggers' ); ?></span>
															</a>
															<a
																class="dropdown-item"
																href="#"

																data-wpwh-event="demo"
																data-wpwh-event-type="send"

																data-wpwh-demo-data-callback="<?php echo isset( $trigger['callback'] ) ? $trigger['callback'] : ''; ?>"
																data-wpwh-webhook="<?php echo $webhook; ?>"
																data-wpwh-group="<?php echo $trigger['trigger']; ?>"
															>
																<img src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/send.svg'; ?>" alt="Settings">
																<span><?php echo WPWHPRO()->helpers->translate( 'Send Demo', 'wpwhpro-page-triggers' ); ?></span>
															</a>
														</div>
													</div>
												</td>
											</tr>

										<?php endforeach; ?>

									</tbody>
								</table>

							<?php endif; ?>

							<div class="wpwh-accordion" id="wpwh_accordion_<?php echo $identkey; ?>">

								<div class="wpwh-accordion__item">
									<button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_arguments_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_arguments_<?php echo $identkey; ?>">
										<span><?php echo WPWHPRO()->helpers->translate( 'Outgoing data', 'wpwhpro-page-triggers'); ?></span>
										<span class="text-secondary">
											<?php echo WPWHPRO()->helpers->translate( 'Expand', 'wpwhpro-page-triggers'); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
												<defs />
												<path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
											</svg>
										</span>
									</button>
									<div id="wpwh_accordion_arguments_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
										<table class="wpwh-table wpwh-text-small mb-4">
											<thead>
												<tr>
													<th><?php echo WPWHPRO()->helpers->translate( 'Argument', 'wpwhpro-page-triggers' ); ?></th>
													<th><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-triggers' ); ?></th>
												</tr>
											</thead>
											<tbody>
												<?php if( ! empty( $trigger['parameter'] ) ) : ?>
													<?php foreach( $trigger['parameter'] as $param => $param_data ) : ?>
														<tr>
															<td><?php echo $param;; ?></td>
															<td class="wpwh-w-50"><?php echo $param_data['short_description']; ?></td>
														</tr>
													<?php endforeach; ?>
												<?php else : ?>
													<tr>
														<td>-</td>
														<td class="wpwh-w-50"><?php echo WPWHPRO()->helpers->translate( 'No default values given', 'wpwhpro-page-triggers' ); ?></td>
													</tr>
												<?php endif; ?>
											</tbody>
										</table>

										<?php if( ! empty( $trigger['returns_code'] ) ) :

											$display_code = $trigger['returns_code'];
											if( is_array( $trigger['returns_code'] ) ){
												$display_code = htmlspecialchars( json_encode( $display_code, JSON_PRETTY_PRINT ) );
											}

											?>
											<p>
												<?php echo WPWHPRO()->helpers->translate( 'Here is an example of all the available default fields that are sent after the trigger is fired. The fields may vary based on custom extensions or third party plugins.', 'wpwhpro-page-triggers'); ?>
											</p>
											<pre><?php echo $display_code; ?></pre>
										<?php endif; ?>
									</div>
								</div>

								<div class="wpwh-accordion__item">
									<button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_description_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_description_<?php echo $identkey; ?>">
										<span><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-triggers'); ?></span>
										<span class="text-secondary">
											<?php echo WPWHPRO()->helpers->translate( 'Expand', 'wpwhpro-page-triggers'); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
												<defs />
												<path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
											</svg>
										</span>
									</button>
									<div id="wpwh_accordion_description_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
										<div class="wpwh-content">
											<?php echo wpautop( $trigger['description'] ); ?>
										</div>
									</div>
								</div>

							</div>

						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

    </div>

  </div>

</div>

<?php if( ! empty( $triggers ) ) : ?>
	<?php foreach( $triggers as $identkey => $trigger ) :

		$trigger_name = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
		$webhook_name = !empty( $trigger['trigger'] ) ? $trigger['trigger'] : '';

		//Map default trigger_attributes if available
		$settings = array();
		if( ! empty( $trigger['settings'] ) ){

			if( isset( $trigger['settings']['data'] ) ){
				$settings = (array) $trigger['settings']['data'];
			}

			if( isset( $trigger['settings']['load_default_settings'] ) && $trigger['settings']['load_default_settings'] === true ){
					$settings = array_merge( $settings, WPWHPRO()->settings->get_default_trigger_settings() );
			}
		}

		//Map dynamic settings
		$required_settings = WPWHPRO()->settings->get_required_trigger_settings();
		foreach( $required_settings as $settings_ident => $settings_data ){

			if( $settings_ident == 'wpwhpro_trigger_authentication' ){
				if( ! empty( $authentication_templates ) ){
					$required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
				} else {
					unset( $required_settings[ $settings_ident ] ); //if empty
				}
			}

		}

		$settings = array_merge( $settings, $required_settings );

		?>
		<div class="modal fade" id="wpwhAddWebhookModal-<?php echo $identkey; ?>" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Add Webhook URL', 'wpwhpro-page-triggers' ); ?></h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 1L1 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M1 1L13 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					</div>
					<?php
						$overwrite_query_params = array(
							'wpwh-trigger' => $trigger['trigger']
						);

						$add_trigger_query_params = array_merge( $_GET, $overwrite_query_params );
						$add_trigger_form_url = WPWHPRO()->helpers->built_url( $current_url, $add_trigger_query_params );
					?>
					<form action="<?php echo $add_trigger_form_url; ?>" method="post">
						<div class="modal-body">
							<div class="form-group pb-4">
								<label class="wpwh-form-label" for="wpwh-webhook-slug-<?php echo $trigger['trigger']; ?>"><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-triggers' ); ?></label>
								<input class="wpwh-form-input w-100" id="wpwh-webhook-slug-<?php echo $trigger['trigger']; ?>" name="wpwh-add-webhook-name" type="text" aria-label="<?php echo WPWHPRO()->helpers->translate( 'Webhook Name (Optional)', 'wpwhpro-page-triggers' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'my-new-webhook', 'wpwhpro-page-triggers' ); ?>">
							</div>
							<div class="form-group mb-0">
								<label class="wpwh-form-label" for="wpwh-webhook-url-<?php echo $trigger['trigger']; ?>">
									<?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-triggers' ); ?>
								</label>
								<div class="wpwh-content wpwh-text-small mb-3">
										<?php echo WPWHPRO()->helpers->translate( 'Using our Pro version, you can also add dynamic parameters to the URL that are later on mapped using the data mapping feature. E.g. ', 'wpwhpro-page-triggers' ); ?><strong>https://yourdomain.test/endpoint/{:user_id:}</strong>
								</div>
								<input class="wpwh-form-input w-100" id="wpwh-webhook-url-<?php echo $trigger['trigger']; ?>" name="wpwh-add-webhook-url" type="text" class="form-control ironikus-webhook-input-new h30" aria-label="<?php echo WPWHPRO()->helpers->translate( 'Include your webhook url here', 'wpwhpro-page-triggers' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'https://example.com/webbhook/onwzinsze', 'wpwhpro-page-triggers' ); ?>">
							</div>
						</div>
						<div class="modal-footer">
							<?php echo WPWHPRO()->helpers->get_nonce_field( $trigger_nonce_data ); ?>
							<input type="hidden" name="wpwh-add-webhook-group" value="<?php echo $trigger['trigger']; ?>">
							<input type="submit" name="submit" id="submit-<?php echo $trigger['trigger']; ?>" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo sprintf( WPWHPRO()->helpers->translate( 'Add for %s', 'wpwhpro-page-triggers' ), $webhook_name ); ?>">
						</div>
					</form>
				</div>
			</div>
		</div>

		<?php $all_triggers = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger['trigger'] ); ?>
		<?php foreach( $all_triggers as $webhook => $webhook_data ) :
			if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; }
			if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-triggers' ), $webhook, $trigger['trigger'] ) ) ) { continue; }

			$status = 'active';
			$status_name = 'Deactivate';
			if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
				$status = 'inactive';
				$status_name = 'Activate';
			}
			?>
			<div class="modal modal--lg fade" id="wpwhTriggerSettingsModal-<?php echo $identkey; ?>-<?php echo $webhook; ?>" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Action Settings for', 'wpwhpro-page-actions' ); ?> "<?php echo $webhook; ?>"</h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M13 1L1 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M1 1L13 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</button>
						</div>
						<div class="modal-body">
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook url:</strong>
								<input type="text" class="wpwh-form-input wpwh-form-input--sm rounded-lg" value="<?php echo $webhook_data['webhook_url']; ?>" readonly>
							</div>
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook trigger name:</strong>
								<?php echo $trigger_name; ?>
							</div>
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook technical name:</strong>
								<?php echo $webhook_name; ?>
							</div>
							<div class="ironikus-tb-webhook-settings">
								<?php if( $settings ) : ?>
									<form id="ironikus-webhook-form-<?php echo $trigger['trigger'] . '-' . $webhook; ?>">
										<table class="wpwh-table wpwh-table--sm mb-4">
											<tbody>
												<?php

												$settings_data = array();
												if( isset( $triggers_data[ $trigger['trigger'] ] ) ){
													if( isset( $triggers_data[ $trigger['trigger'] ][ $webhook ] ) ){
														if( isset( $triggers_data[ $trigger['trigger'] ][ $webhook ]['settings'] ) ){
															$settings_data = $triggers_data[ $trigger['trigger'] ][ $webhook ]['settings'];
														}
													}
												}

												foreach( $settings as $setting_name => $setting ) :

													$is_checked = ( $setting['type'] == 'checkbox' && isset( $setting['default_value'] ) && $setting['default_value'] == 'yes' ) ? 'checked' : '';
													$value = isset( $setting['default_value'] ) ? $setting['default_value'] : '';
													$placeholder = ( $setting['type'] != 'checkbox' && isset( $setting['placeholder'] ) ) ? $setting['placeholder'] : '';

													if( $setting['type'] == 'checkbox' ){
														$value = '1';
													}

													if( isset( $settings_data[ $setting_name ] ) ){
														$value = $settings_data[ $setting_name ];
														$is_checked = ( $setting['type'] == 'checkbox' && $value == 1 ) ? 'checked' : '';
													}

													?>
													<tr>
														<td>
															<label class="wpwh-form-label" for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>">
																<strong><?php echo $setting['label']; ?></strong>
															</label>
															<?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>
																<input class="wpwh-form-input" id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
															<?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
																<div class="wpwh-toggle wpwh-toggle--on-off">
																	<input type="<?php echo $setting['type']; ?>" id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?> placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?>>
																	<label class="wpwh-toggle__btn" for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>"></label>
																</div>
															<?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
																<select class="wpwh-form-input" name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>>
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
																	<option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo WPWHPRO()->helpers->translate( $choice_label, 'wpwhpro-page-triggers' ); ?></option>
																	<?php endforeach; ?>
																</select>
															<?php endif; ?>
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
											data-wpwh-event-type="send"
											data-wpwh-event-element="wpwhTriggerSettingsModal-<?php echo $webhook; ?>"

											data-webhook-group="<?php echo $trigger['trigger']; ?>"
											data-webhook-id="<?php echo $webhook; ?>"
										>
											<span><?php echo WPWHPRO()->helpers->translate( 'Save Settings', 'wpwhpro-page-triggers' ); ?></span>
										</button>
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
			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php
$triggers = WPWHPRO()->webhook->get_triggers();
$triggers_data = WPWHPRO()->webhook->get_hooks( 'trigger' );
$current_url = WPWHPRO()->helpers->get_current_url(false);
$current_url_full = WPWHPRO()->helpers->get_current_url();
$authentication_templates = WPWHPRO()->auth->get_auth_templates();

?>
<?php add_ThickBox(); ?>
<div class="ironikus-webhook-triggers">
	<h2><?php echo WPWHPRO()->helpers->translate( 'Available Webhook Triggers', 'wpwhpro-page-triggers' ); ?></h2>
	<div class="main-description"><?php echo sprintf( WPWHPRO()->helpers->translate( 'Below you will find a list of all active %1$s triggers. To use one, you need to define a URL that should be triggered to send the available data. For more information on that, you can check out our each webhook trigger description or our product documentation by clicking <a title="Go to our product documentation" target="_blank" href="%2$s">here</a>.', 'wpwhpro-page-triggers' ), '<strong>' . $this->page_title . '</strong>', 'https://ironikus.com/docs/?utm_source=wp-webhooks-pro&utm_medium=send-data-documentation&utm_campaign=WP%20Webhooks%20Pro'); ?></div>
	
	<?php if( ! empty( $triggers ) ) : ?>
		<div class="accordion" id="allTriggers">
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
							$settings = array_merge( WPWHPRO()->settings->get_default_trigger_settings(), $settings );
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

				$settings = array_merge( $required_settings, $settings );

				?>
				<div class="card">
					<div class="card-header collapsed" id="headingtrigger-<?php echo $identkey; ?>" data-toggle="collapse" data-target="#collapsetrigger-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapsetrigger-<?php echo $identkey; ?>">
						<button class="btn btn-link collapsed" type="button">
							<strong><?php echo $trigger_name; ?></strong>
							<?php if( ! empty( $webhook_name ) ) : ?>
								 <span class="accordion-sub-webhook-name">(<?php echo $webhook_name; ?>)</span>
							<?php endif; ?>
						</button>
					</div>
					<div id="collapsetrigger-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingtrigger-<?php echo $identkey; ?>" data-parent="#allTriggers">
						<div class="card-body">
							<div class="irnks-short-description">
								<?php echo $trigger['short_description']; ?>
							</div>

							<table class="table ironikus-webhook-table ironikus-group-<?php echo $trigger['trigger']; ?>">
								<thead class="thead-dark">
								<tr>
									<th style="width:15%">
										<?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-triggers' ); ?>
									</th>
									<th style="width:70%">
										<?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-triggers' ); ?>
									</th>
									<th style="width:15%">
										<?php echo WPWHPRO()->helpers->translate( 'Action', 'wpwhpro-page-triggers' ); ?>
									</th>
								</tr>
								</thead>
								<tbody class="single-webhook-trigger-table-body">
								<?php $all_triggers = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger['trigger'] ); ?>
								<?php foreach( $all_triggers as $webhook => $webhook_data ) : ?>
									<?php if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; } ?>
									<?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-triggers' ), $webhook ) ) ) { continue; } ?>
									<?php
										$status = 'active';
										$status_name = 'Deactivate';
										if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
											$status = 'inactive';
											$status_name = 'Activate';
										}
									?>
									<tr id="ironikus-webhook-id-<?php echo $webhook; ?>">
										<td>
											<?php echo $webhook; ?>
										</td>
										<td>
											<input class="ironikus-webhook-input" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo $webhook_data['webhook_url']; ?>" readonly /><br>
										</td>
										<td>
											<div class="ironikus-element-actions">
												<span class="ironikus-delete" ironikus-delete="<?php echo $webhook; ?>" ironikus-group="<?php echo $trigger['trigger']; ?>" ><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-triggers' ); ?></span><br>
												<span class="ironikus-status-action <?php echo $status; ?>" ironikus-webhook-status="<?php echo $status; ?>" ironikus-webhook-group="<?php echo $trigger['trigger']; ?>" ironikus-webhook-slug="<?php echo $webhook; ?>"><?php echo WPWHPRO()->helpers->translate( $status_name, 'wpwhpro-page-actions' ); ?></span><br>
												<a class="thickbox ironikus-settings-wrapper" title="<?php echo $trigger_name; ?>" href="#TB_inline?height=330&width=800&inlineId=wpwhpro-trigger-settings-<?php echo $webhook; ?>">
													<span class="ironikus-settings" ironikus-group="<?php echo $trigger['trigger']; ?>" ><?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-triggers' ); ?></span>
												</a>
												<?php if( ! empty( $trigger['callback'] ) ) : ?>
													<br><span class="ironikus-send-demo" ironikus-demo-data-callback="<?php echo $trigger['callback']; ?>" ironikus-webhook="<?php echo $webhook; ?>" ironikus-group="<?php echo $trigger['trigger']; ?>" ><?php echo WPWHPRO()->helpers->translate( 'Send demo', 'wpwhpro-page-triggers' ); ?></span>
												<?php endif; ?>
											</div>
											<div id="wpwhpro-trigger-settings-<?php echo $webhook; ?>" style="display:none;">
												<div class="ironikus-tb-webhook-wrapper">
													<div class="ironikus-tb-webhook-url">
														<strong>Webhook url:</strong> <?php echo $webhook_data['webhook_url']; ?>
														<br><strong>Webhook trigger name:</strong> <?php echo $trigger_name; ?>
														<br><strong>Webhook technical name:</strong> <?php echo $webhook_data['webhook_name']; ?>
													</div>
													<div class="ironikus-tb-webhook-settings">
														<?php if( $settings ) : ?>
															<form id="ironikus-webhook-form-<?php echo $webhook; ?>">
																<table class="table wpwhpro-settings-table form-table">
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

																		$is_checked = ( $setting['type'] == 'checkbox' && $setting['default_value'] == 'yes' ) ? 'checked' : '';
																		$value = ( $setting['type'] != 'checkbox' && isset( $setting['default_value'] ) ) ? $setting['default_value'] : '1';
																		$placeholder = ( $setting['type'] != 'checkbox' && isset( $setting['placeholder'] ) ) ? $setting['placeholder'] : '';

																		if( isset( $settings_data[ $setting_name ] ) ){
																			$value = $settings_data[ $setting_name ];
																			$is_checked = ( $setting['type'] == 'checkbox' && $value == 1 ) ? 'checked' : '';
																		}

																		?>
																		<tr valign="top">
																			<td class="tb-settings-input">
																				<label for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>">
																					<strong><?php echo $setting['label']; ?></strong>
																				</label>
																				<?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>
																				<input id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
																				<?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
																					<label class="switch ">
																						<input id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $webhook; ?>" class="default primary" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
																						<span class="slider round"></span>
																					</label>
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
																						<option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo WPWHPRO()->helpers->translate( $choice_label, 'wpwhpro-page-triggers' ); ?></option>
																						<?php endforeach; ?>
																					</select>
																				<?php endif; ?>
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
																<div class="ironikus-single-webhook-trigger-handler">
																	<p class="btn btn-primary h30 ironikus-submit-settings-form" id="<?php echo $webhook; ?>" webhook-group="<?php echo $trigger['trigger']; ?>" webhook-id="<?php echo $webhook; ?>" >
																		<span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Save Settings', 'wpwhpro-page-triggers' ); ?></span>
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
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>

							<div class="ironikus-single-webhook-trigger-handler">
								<div class="input-group mb-3">
									<label class="input-group-prepend" for="ironikus-webhook-slug-<?php echo $trigger['trigger']; ?>">
										<span class="input-group-text"><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-triggers' ); ?></span>
									</label>
									<input id="ironikus-webhook-slug-<?php echo $trigger['trigger']; ?>" type="text" class="form-control ironikus-webhook-input-new h30" aria-label="<?php echo WPWHPRO()->helpers->translate( 'Webhook Name (Optional)', 'wpwhpro-page-triggers' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'my-new-webhook', 'wpwhpro-page-triggers' ); ?>">
								</div>
								<div class="input-group mb-3">
									<label class="input-group-prepend" for="ironikus-webhook-url-<?php echo $trigger['trigger']; ?>">
										<span class="input-group-text"><?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-triggers' ); ?></span>
									</label>
									<input id="ironikus-webhook-url-<?php echo $trigger['trigger']; ?>" type="text" class="form-control ironikus-webhook-input-new h30" aria-label="<?php echo WPWHPRO()->helpers->translate( 'Include your webhook url here', 'wpwhpro-page-triggers' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'https://example.com/webbhook/onwzinsze', 'wpwhpro-page-triggers' ); ?>">
								</div>
								<p class="btn btn-primary ironikus-save h30" ironikus-webhook-callback="<?php echo !empty( $trigger['callback'] ) ? $trigger['callback'] : ''; ?>" ironikus-webhook-trigger="<?php echo $trigger['trigger']; ?>" >
									<span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Add', 'wpwhpro-page-triggers' ); ?></span>
									<img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
								</p>
							</div>

							<div class="accordion" id="triggerSendValues-<?php echo $identkey; ?>">
								<div class="card">
									<div class="card-header" id="headingTriggerSendValuesSub-<?php echo $identkey; ?>" data-toggle="collapse" data-target="#collapseTriggerSendValuesSub-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapseTriggerSendValuesSub-<?php echo $identkey; ?>">
										<button class="btn btn-link collapsed" type="button">
											<?php echo WPWHPRO()->helpers->translate( 'Outgoing values', 'wpwhpro-page-triggers'); ?>
										</button>
									</div>

									<div id="collapseTriggerSendValuesSub-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingTriggerSendValuesSub-<?php echo $identkey; ?>" data-parent="#triggerSendValues-<?php echo $identkey; ?>">
										<div class="card-body">
											<?php if( ! empty( $trigger['parameter'] ) ) : ?>
												<ul class="wpwh-trigger-arguments">
													<?php foreach( $trigger['parameter'] as $param => $param_data ) : ?>
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
											<?php endif; ?>

											<?php if( ! empty( $trigger['returns_code'] ) ) : ?>
												<p>
													<?php echo WPWHPRO()->helpers->translate( 'Here is an example of all the available default fields that are sent after the trigger is fired. The fields may vary based on custom extensions or third party plugins.', 'wpwhpro-page-triggers'); ?>
												</p>
												<pre><?php echo $trigger['returns_code']; ?></pre>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>

							<div class="accordion" id="triggerDescription-<?php echo $identkey; ?>">
								<div class="card">
									<div class="card-header" id="headingtriggerDescriptionSub-<?php echo $identkey; ?>"  data-toggle="collapse" data-target="#collapsetriggerDescriptionSub-<?php echo $identkey; ?>" aria-expanded="false" aria-controls="collapsetriggerDescriptionSub-<?php echo $identkey; ?>">
										<button class="btn btn-link collapsed" type="button">
											<?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-triggers'); ?>
										</button>
									</div>

									<div id="collapsetriggerDescriptionSub-<?php echo $identkey; ?>" class="collapse" aria-labelledby="headingtriggerDescriptionSub-<?php echo $identkey; ?>" data-parent="#triggerDescription-<?php echo $identkey; ?>">
										<div class="card-body">
											<?php echo $trigger['description']; ?>
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
			<?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any triggers activated. Please go to our settings tab and activate some.', 'wpwhpro-page-triggers' ); ?>
		</div>
	<?php endif; ?>

</div>

<input id="ironikus-webhook-current-url" type="hidden" value="<?php echo $current_url_full; ?>" />
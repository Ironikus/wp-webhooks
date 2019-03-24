<?php
$triggers = WPWHPRO()->webhook->get_triggers();
$current_url = WPWHPRO()->helpers->get_current_url(false);
$current_url_full = WPWHPRO()->helpers->get_current_url();

?>

<div class="ironikus-webhook-triggers">
    <h2><?php echo WPWHPRO()->helpers->translate( 'Available Webhook Triggers', 'wpwhpro-page-triggers' ); ?></h2>
    <p style="font-weight:normal;"><?php echo sprintf( WPWHPRO()->helpers->translate( 'Below you will find a list of all active Webhooks triggers. To use one, you need to define a url that should be triggered to send the available data to. For more information on that, you can checkout our product documentation by clicking <a title="Go to our product documentation" target="_blank" href="%s">here</a>.', 'wpwhpro-page-triggers' ), 'https://ironikus.com/docs/?utm_source=wp-webhooks-pro&utm_medium=send-data-documentation&utm_campaign=WP%20Webhooks%20Pro'); ?></p>

	<?php if( ! empty( $triggers ) ) : ?>
		<?php foreach( $triggers as $trigger ) : ?>
            <div class="accordion irnks-accordion">
                <div class="accordion__item irnks-accordion-item">
                    <div class="accordion-header irnks-accordion-header"><?php echo !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger']; ?></div>
                    <div class="accordion-body irnks-accordion-body">
                        <div class="accordion-body__contents">
							<div class="irnks-short-description">
								<?php echo $trigger['short_description']; ?>
                            </div>

                            <table class="ironikus-webhook-table ironikus-group-<?php echo $trigger['trigger']; ?>">
                                <thead>
                                <tr><th style="width:90%">
										<?php echo WPWHPRO()->helpers->translate( 'Webhook URL', 'wpwhpro-page-triggers' ); ?>
                                    </th>
                                    <th style="width:10%">
										<?php echo WPWHPRO()->helpers->translate( 'Action', 'wpwhpro-page-triggers' ); ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
								<?php $all_triggers = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger['trigger'] ); ?>
								<?php foreach( $all_triggers as $webhook => $webhook_data ) : ?>
									<?php if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; } ?>
									<?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-triggers' ), $webhook ) ) ) { continue; } ?>
                                    <tr id="ironikus-webhook-id-<?php echo $webhook; ?>">
                                        <td>
                                            <input class="ironikus-webhook-input" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo $webhook_data['webhook_url']; ?>" readonly /><br>
                                        </td>
                                        <td>
                                            <div class="ironikus-element-actions">
                                                <span class="ironikus-delete" ironikus-delete="<?php echo $webhook; ?>" ironikus-group="<?php echo $trigger['trigger']; ?>" ><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-triggers' ); ?></span>
												<?php if( ! empty( $trigger['callback'] ) ) : ?>
                                                    <br><span class="ironikus-send-demo" ironikus-demo-data-callback="<?php echo $trigger['callback']; ?>" ironikus-webhook="<?php echo $webhook; ?>" ironikus-group="<?php echo $trigger['trigger']; ?>" ><?php echo WPWHPRO()->helpers->translate( 'Send demo', 'wpwhpro-page-triggers' ); ?></span>
												<?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
								<?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="ironikus-single-webhook-trigger-handler">
                                <input id="ironikus-webhook-url-<?php echo $trigger['trigger']; ?>" class="ironikus-webhook-input-new h30" type="text" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Include your wehook url here.', 'wpwhpro-page-triggers' ); ?>" >
                                <p class="ironikus-save h30" ironikus-webhook-callback="<?php echo !empty( $trigger['callback'] ) ? $trigger['callback'] : ''; ?>" ironikus-webhook-trigger="<?php echo $trigger['trigger']; ?>" >
                                    <span class="ironikus-save-text active">Save</span>
                                    <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
                                </p>
                            </div>
                        </div>
                        <div class="accordion irnks-accordion">
                            <div class="accordion__item irnks-accordion-item">
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWHPRO()->helpers->translate( 'Sent values:', 'wpwhpro-page-triggers'); ?></div>
                                <div class="accordion-body irnks-accordion-body">
                                    <div class="accordion-body__contents ironikus-arguments">
                                        <ul>
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

                                        <?php if( ! empty( $trigger['returns_code'] ) ) : ?>
                                        <p>
                                            <?php echo WPWHPRO()->helpers->translate( 'Here is an example of all the available default fields that are sent after the trigger is fired. The fields may vary based on custom extensions or third party plugins.', 'wpwhpro-page-actions'); ?>
                                        </p>
                                        <pre>
                                            <?php echo $trigger['returns_code']; ?>
                                        </pre>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="accordion__item irnks-accordion-item">
                                <div class="accordion-header irnks-accordion-header"><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-triggers' ); ?></div>
                                <div class="accordion-body irnks-accordion-body">
                                    <div class="accordion-body__contents">
										<?php echo $trigger['description']; ?>
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
            <?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any triggers activated. Please go to our settings tab and activate some.', 'wpwhpro-page-triggers' ); ?>
        </div>
	<?php endif; ?>

</div>

<input id="ironikus-webhook-current-url" type="hidden" value="<?php echo $current_url_full; ?>" />
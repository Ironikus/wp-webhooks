<?php

/*
 * Settings Template
 */

$settings = WPWHPRO()->settings->get_settings();
$triggers = WPWHPRO()->webhook->get_triggers( '', false );
$actions = WPWHPRO()->webhook->get_actions( false );
$active_webhooks = WPWHPRO()->settings->get_active_webhooks();
$current_url_full = WPWHPRO()->helpers->get_current_url();

if( did_action( 'wpwh/admin/settings/settings_saved' ) ){
	echo WPWHPRO()->helpers->create_admin_notice( 'The settings are successfully updated. Please refresh the page.', 'success', true );
}

?>

<div class="ironikus-settings-wrapper">

	<h2><?php echo WPWHPRO()->helpers->translate('Global Settings', 'admin-settings'); ?></h2>

	<div class="sub-text">
		<?php echo WPWHPRO()->helpers->translate( 'Here you can configure the global settings for our plugin, enable certain features to extend the possibilities for your site, and activate your available webhook actions and triggers.', 'admin-settings' ); ?>
	</div>

	<form id="ironikus-main-settings-form" method="post" action="">

		<table class="table wpwhpro-settings-table form-table">
			<tbody>

			<?php foreach( $settings as $setting_name => $setting ) :

				$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
				$value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';
				$is_checkbox = ( $setting['type'] == 'checkbox' ) ? true : false;

			?>
				<tr valign="top">
					<td class="settings-input" >
						<label for="<?php echo $setting_name; ?>">
							<strong><?php echo $setting['label']; ?></strong>
						</label>
						<?php if( $is_checkbox ) : ?>
							<label class="switch ">
								<input id="<?php echo $setting['id']; ?>" class="default primary" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
								<span class="slider round"></span>
							</label>
						<?php else : ?>
							<input id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
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

		<p class="btn btn-primary h30 ironikus-submit-settings-data">
			<span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Save all', 'admin-settings' ); ?></span>
			<img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
		</p>

		<h2><?php echo WPWHPRO()->helpers->translate('Activate "Send Data" Triggers', 'admin-settings'); ?></h2>

		<div class="sub-text">
			<?php echo WPWHPRO()->helpers->translate( 'This is a list of all available data triggers, that are currently registered on your site. To use one, just check the box and click save. After that you will be able to use the trigger within the "Send Data" tab.', 'admin-settings' ); ?>
		</div>
		<table class="table wpwhpro-settings-table form-table">
			<tbody>

			<?php foreach( $triggers as $trigger ) :

				$ident = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
				$is_checked = isset( $active_webhooks['triggers'][ $trigger['trigger'] ] ) ?  'checked' : '';

				?>
				<tr valign="top">
					<td class="action-button-toggle">
						<label class="switch ">
							<input id="wpwhpropt_<?php echo $trigger['trigger']; ?>" class="regular-text default primary" name="wpwhpropt_<?php echo $trigger['trigger']; ?>" type="checkbox" class="regular-text" value="1" <?php echo $is_checked; ?> />
							<span class="slider round"></span>
						</label>
					</td>
					<td scope="row" valign="top">
						<label for="wpwhpropt_<?php echo $trigger['trigger']; ?>">
							<strong><?php echo $ident; ?></strong>
						</label>
					</td>
					<td>
						<p class="description">
							<?php echo $trigger['short_description']; ?>
						</p>
					</td>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>
		<p class="btn btn-primary h30 ironikus-submit-settings-data">
			<span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Save all', 'admin-settings' ); ?></span>
			<img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
		</p>

		<h2><?php echo WPWHPRO()->helpers->translate('Activate "Recieve Data" Actions', 'admin-settings'); ?></h2>

		<div class="sub-text">
			<?php echo WPWHPRO()->helpers->translate( 'This is a list of all available action webhooks registered on your site. To use one, just check the box and click save. After that, you will be able to use the action at the Recieve Data tab.', 'admin-settings' ); ?>
		</div>
		<table class="table wpwhpro-settings-table form-table">
			<tbody>

			<?php foreach( $actions as $action ) :

				$is_checked = isset( $active_webhooks['actions'][ $action['action'] ] ) ?  'checked' : '';

				?>
				<tr valign="top">
					<td class="action-button-toggle">
						<label class="switch ">
							<input id="wpwhpropa_<?php echo $action['action']; ?>" class="regular-text default primary" name="wpwhpropa_<?php echo $action['action']; ?>" type="checkbox" class="regular-text" value="1" <?php echo $is_checked; ?> />
							<span class="slider round"></span>
						</label>
					</td>
					<td scope="row" valign="top">
						<label for="wpwhpropa_<?php echo $action['action']; ?>">
							<strong><?php echo $action['action']; ?></strong>
						</label>
					</td>
					<td>
						<p class="description">
							<?php echo $action['short_description']; ?>
						</p>
					</td>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>

		<input type="hidden" name="ironikus_update_settings" value="yes">
		<p class="btn btn-primary h30 ironikus-submit-settings-data">
			<span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Save all', 'admin-settings' ); ?></span>
			<img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
		</p>

	</form>

</div>
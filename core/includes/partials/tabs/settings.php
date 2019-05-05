<?php

/*
 * Settings Template
 */

$settings = WPWHPRO()->settings->get_settings();
$triggers = WPWHPRO()->webhook->get_triggers( '', false );
$actions = WPWHPRO()->webhook->get_actions( false );
$active_webhooks = WPWHPRO()->settings->get_active_webhooks();
$current_url_full = WPWHPRO()->helpers->get_current_url();

$reload_location = false;

if( isset( $_POST['ironikus_update_settings'] ) ) {
	if( ! wp_verify_nonce( $_POST['ironikus_wpwhpro_settings_nonce'], 'ironikus_wpwhpro_settings' ) ) {
		return;
	}

	// START General Settings
	foreach( $settings as $settings_name => $setting ){

		$value = '';

		if( $setting['type'] == 'checkbox' ){
			if( ! isset( $_POST[ $settings_name ] ) ){
				$value = 'no';
			} else {
				$value = 'yes';
			}
		} elseif( $setting['type'] == 'text' ){
			if( isset( $_POST[ $settings_name ] ) ){
				$value = sanitize_title( $_POST[ $settings_name ] );
			}
		}

		update_option( $settings_name, $value );
		$settings[ $settings_name ][ 'value' ] = $value;
	}
	// END General Settings

	// START Trigger Settings
	foreach( $triggers as $trigger ){
		if( isset( $_POST[ 'wpwhpropt_' . $trigger['trigger'] ] ) ){
			$active_webhooks['triggers'][ $trigger['trigger'] ] = array();
		} else {
			unset( $active_webhooks['triggers'][ $trigger['trigger'] ] );
		}
	}
	// END Trigger Settings

	// START Action Settings
	foreach( $actions as $action ){
		if( isset( $_POST[ 'wpwhpropa_' . $action['action'] ] ) ){
			$active_webhooks['actions'][ $action['action'] ] = array();
		} else {
			unset( $active_webhooks['actions'][ $action['action'] ] );
		}
	}
	// END Action Settings
	update_option( WPWHPRO()->settings->get_active_webhooks_ident(),  $active_webhooks );
	$reload_location = true;

	echo WPWHPRO()->helpers->create_admin_notice( 'The settings are successfully updated. Please refresh the page.', 'success', true );
}

//Reload the page
if( $reload_location ){
	echo '<script type="text/javascript">window.location = window.location.href;location.reload();</script>';
}

?>

<h2><?php echo WPWHPRO()->helpers->translate('Global Settings', 'admin-settings'); ?></h2>

<p>
	<?php echo WPWHPRO()->helpers->translate( 'Here you can configure the global settings for our plugin, as well as enable certain features to extend the possibilities for your site.', 'admin-settings' ); ?>
</p>

<form method="post" action="">

	<table class="wpwhpro-settings-table form-table">
		<tbody>

		<?php foreach( $settings as $setting_name => $setting ) :

			$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
			$value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';

		?>
			<tr valign="top">
				<td>
					<input id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
				</td>
				<td scope="row" valign="top">
					<label for="<?php echo $setting_name; ?>">
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

	<?php submit_button( WPWHPRO()->helpers->translate( 'Save all', 'admin-settings' ) ); ?>

    <h2><?php echo WPWHPRO()->helpers->translate('Activate "Send Data" Triggers', 'admin-settings'); ?></h2>

    <p>
		<?php echo WPWHPRO()->helpers->translate( 'This is a list of all available data triggers, that are currently registered on your site. To use one, just check the box and click save. After that you will be able to use the trigger within the "Send Data" tab.', 'admin-settings' ); ?>
    </p>
    <table class="wpwhpro-settings-table form-table">
        <tbody>

		<?php foreach( $triggers as $trigger ) :

			$ident = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
			$is_checked = isset( $active_webhooks['triggers'][ $trigger['trigger'] ] ) ?  'checked' : '';

			?>
            <tr valign="top">
                <td>
                    <input id="wpwhpropt_<?php echo $trigger['trigger']; ?>" name="wpwhpropt_<?php echo $trigger['trigger']; ?>" type="checkbox" class="regular-text" value="1" <?php echo $is_checked; ?> />
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
	<?php submit_button( WPWHPRO()->helpers->translate( 'Save all', 'admin-settings' ) ); ?>

	<h2><?php echo WPWHPRO()->helpers->translate('Activate "Recieve Data" Actions', 'admin-settings'); ?></h2>

	<p>
		<?php echo WPWHPRO()->helpers->translate( 'This is a list of all available action webhooks registered on your site. To use one, just check the box and click save. After that you will be able to use the action at the Recieve Data tab.', 'admin-settings' ); ?>
	</p>
	<table class="wpwhpro-settings-table form-table">
		<tbody>

		<?php foreach( $actions as $action ) :

			$is_checked = isset( $active_webhooks['actions'][ $action['action'] ] ) ?  'checked' : '';

			?>
			<tr valign="top">
				<td>
					<input id="wpwhpropa_<?php echo $action['action']; ?>" name="wpwhpropa_<?php echo $action['action']; ?>" type="checkbox" class="regular-text" value="1" <?php echo $is_checked; ?> />
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
	<?php wp_nonce_field( 'ironikus_wpwhpro_settings', 'ironikus_wpwhpro_settings_nonce' ); ?>
	<?php submit_button( WPWHPRO()->helpers->translate( 'Save all', 'admin-settings' ) ); ?>

</form>
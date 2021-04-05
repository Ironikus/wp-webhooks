<?php

/*
 * Settings Template
 */

$settings = WPWHPRO()->settings->get_settings();
$triggers = WPWHPRO()->webhook->get_triggers( '', false );
$actions = WPWHPRO()->webhook->get_actions( false );
$current_url_full = WPWHPRO()->helpers->get_current_url();
$settings_nonce_data = WPWHPRO()->settings->get_settings_nonce();

if( did_action( 'wpwh/admin/settings/settings_saved' ) ){
	echo WPWHPRO()->helpers->create_admin_notice( 'The settings have been successfully updated. Please refresh the page.', 'success', true );
}

?>
<div class="wpwh-container">

    <form id="wpwh-main-settings-form" method="post" action="">

		<div class="wpwh-title-area mb-4">
			<h2><?php echo WPWHPRO()->helpers->translate( 'Global Settings', 'wpwhpro-page-settings' ); ?></h2>
			<p class="wpwh-text-small"><?php echo sprintf( WPWHPRO()->helpers->translate( 'Here you can configure the global settings for our plugin, enable certain features to extend the possibilities for your site, and activate your available webhook actions and triggers.', 'wpwhpro-page-settings' ), WPWH_NAME ); ?></p>
		</div>

		<div class="wpwh-settings">
			<?php foreach( $settings as $setting_name => $setting ) :

			if( isset( $setting['dangerzone'] ) && $setting['dangerzone'] ){
				continue;
			}

			$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
			$value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';
			$is_checkbox = ( $setting['type'] == 'checkbox' ) ? true : false;

			?>
			<div class="wpwh-setting">
				<div class="wpwh-setting__title">
				<label for="<?php echo $setting['id']; ?>"><?php echo $setting['label']; ?></label>
				</div>
				<div class="wpwh-setting__desc">
				<?php echo wpautop( $setting['description'] ); ?>
				</div>
				<div class="wpwh-setting__action">
				<?php if( $is_checkbox ) : ?>
					<div class="wpwh-toggle wpwh-toggle--on-off">
					<input type="<?php echo $setting['type']; ?>" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?>>
					<label class="wpwh-toggle__btn" for="<?php echo $setting['id']; ?>"></label>
					</div>
				<?php else : ?>
					<input id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
				<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="wpwh-text-center mt-4 pt-3">
			<button class="wpwh-btn wpwh-btn--secondary active" type="submit" name="wpwh_settings_submit">
			<span><?php echo WPWHPRO()->helpers->translate( 'Save All Settings', 'admin-settings' ); ?></span>
			</button>
		</div>

		<div class="wpwh-title-area mb-4 mt-4">
			<h2 class="wpwh-text-danger"><?php echo WPWHPRO()->helpers->translate( 'Danger Zone', 'wpwhpro-page-settings' ); ?></h2>
			<p class="wpwh-text-small"><?php echo sprintf( WPWHPRO()->helpers->translate( 'The settings down below are very powerful and have a huge impact to the functionality of the plugin. Please use them with caution.', 'wpwhpro-page-settings' ), WPWH_NAME ); ?></p>
		</div>

		<div class="wpwh-settings">
			<?php foreach( $settings as $setting_name => $setting ) :

			if( isset( $setting['dangerzone'] ) && ! $setting['dangerzone'] ){
				continue;
			}

			$is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
			$value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';
			$is_checkbox = ( $setting['type'] == 'checkbox' ) ? true : false;

			?>
			<div class="wpwh-setting">
				<div class="wpwh-setting__title">
				<label for="<?php echo $setting['id']; ?>"><?php echo $setting['label']; ?></label>
				</div>
				<div class="wpwh-setting__desc">
				<?php echo wpautop( $setting['description'] ); ?>
				</div>
				<div class="wpwh-setting__action">
				<?php if( $is_checkbox ) : ?>
					<div class="wpwh-toggle wpwh-toggle--on-off">
					<input type="<?php echo $setting['type']; ?>" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?>>
					<label class="wpwh-toggle__btn" for="<?php echo $setting['id']; ?>"></label>
					</div>
				<?php else : ?>
					<input id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
				<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="wpwh-text-center mt-4 pt-3">
			<button class="wpwh-btn wpwh-btn--secondary active" type="submit" name="wpwh_settings_submit">
			<span><?php echo WPWHPRO()->helpers->translate( 'Save All Settings', 'admin-settings' ); ?></span>
			</button>
		</div>

		<?php wp_nonce_field( $settings_nonce_data['action'], $settings_nonce_data['arg'] ); ?>
    </form>
</div>
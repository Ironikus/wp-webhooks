<?php

$templates = WPWHPRO()->auth->get_auth_templates();
$auth_methods = WPWHPRO()->settings->get_authentication_methods();

?>
<?php add_ThickBox(); ?>
<div class="wpwhpro-authentication-wrapper">
    <h2><?php echo WPWHPRO()->helpers->translate( 'Authentication', 'wpwhpro-page-authentication' ); ?></h2>

    <div>
        <?php echo sprintf(WPWHPRO()->helpers->translate( 'Create your own authentication template down below. This allows you to authenticate your outgoing "Send Data" webhook triggers to a given endpoint. For more information, please check out the authentication documentation by clicking <a href="%s" target="_blank" >here</a>.', 'wpwhpro-page-authentication' ), 'https://ironikus.com/docs/knowledge-base/how-to-use-authentication/'); ?>
    </div>

    <div id="wpwhpro-authentication-actions">
        <form id="ironikus-authentication-form" method="post" action="">
            <div class="input-group">
                <label class="input-group-prepend" for="wpwh-authentication-template">
                    <span class="input-group-text" id="wpwh-authentication-template-label"><?php echo WPWHPRO()->helpers->translate( 'Template name', 'wpwhpro-page-authentication' ); ?></span>
                </label>
                <input type="text" class="form-control" id="wpwh-authentication-template" name="wpwh-authentication-template" aria-describedby="wpwh-authentication-template-label" placeholder="<?php echo WPWHPRO()->helpers->translate( 'my-template-name', 'wpwhpro-page-authentication' ); ?>">
            </div>

            <div class="input-group">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="wpwh-authentication-type"><?php echo WPWHPRO()->helpers->translate( 'Auth Type', 'wpwhpro-page-authentication' ); ?></label>
                </div>
                <select id="wpwh-authentication-type" class="custom-select">
                    <option value="empty" selected><?php echo WPWHPRO()->helpers->translate( 'Choose...', 'wpwhpro-page-authentication' ); ?>.</option>
                    <?php foreach( $auth_methods as $auth_type => $auth_data ) : ?>
                        <option value="<?php echo $auth_type; ?>"><?php echo $auth_data['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p class="btn btn-primary h30 ironikus-submit-auth-data">
                <span class="ironikus-save-text active"><?php echo WPWHPRO()->helpers->translate( 'Create Template', 'admin-settings' ); ?></span>
                <img class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader.gif'; ?>" />
            </p>
        </form>

    </div>

    <?php if( ! empty( $templates ) ) : ?>
        <div class="wpwhpro-authentication-template-wrapper">
            <select id="wpwhpro-authentication-template-select">
                <option value="empty"><?php echo WPWHPRO()->helpers->translate( 'Choose...', 'wpwhpro-page-authentication' ); ?></option>
                <?php foreach( $templates as $template ) : ?>
                    <option value="<?php echo $template->id; ?>"><?php echo WPWHPRO()->helpers->translate( $template->name, 'wpwhpro-page-authentication' ); ?></option>
                <?php endforeach; ?>
            </select>
            <img id="wpwhpro-authentication-template-loader-img" class="ironikus-loader" src="<?php echo WPWH_PLUGIN_URL . 'core/includes/assets/img/loader-black.gif'; ?>" />
        </div>
        <?php else : ?>
        <div class="wpwhpro-empty">
            <?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any authentication templates available. Please create one first.', 'wpwhpro-page-authentication' ); ?>
        </div>
    <?php endif; ?>

    <div id="wpwhpro-authentication-content-wrapper">
        <div class="wpwhpro-empty">
            <?php echo WPWHPRO()->helpers->translate( 'Please choose a template first.', 'wpwhpro-page-authentication' ); ?>
        </div>
    </div>
</div>
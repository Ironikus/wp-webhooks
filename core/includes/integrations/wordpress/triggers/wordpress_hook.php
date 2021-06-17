<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wordpress_hook' ) ) :

	/**
	 * Load the wordpress_hook trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wordpress_hook {

        public function get_details(){

            $translation_ident = "action-wordpress_hook-description";

            $parameter = array(
                'none'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'No default values given. Send over whatever you like.', 'trigger-login-user-content' ) ),
            );

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on a WordPress trigger or action hook, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>WordPress Hook</strong> (wordpress_hook) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>WordPress Hook</strong> (wordpress_hook)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to define the WordPress hook.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger contains two trigger calls. The first one is registered on the <strong>wpwhpro/integrations/callbacks_registered</strong> hook, which fires directly after all integration callbacks have been registered.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_filter( 'wpwhpro/integrations/callbacks_registered', array( $this, 'register_wrodpress_hook_callbacks' ), 10, 1 );</pre>
<?php echo WPWHPRO()->helpers->translate( "Within this trigger callback, we call the WordPress callback you defined within the settings of the webhook URL you added.", $translation_ident ); ?>
<br><br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to fire this trigger?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "To fire this trigger, you need to add the WordPress hook you want to fire it within the settings of your added webhook URL.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "If you want to learn more about the hooks, please refer to the following manual: ", $translation_ident ); ?>
<br>
<a title="Go to wordpress.org" target="_blank" href="https://developer.wordpress.org/plugins/hooks/">https://developer.wordpress.org/plugins/hooks/</a>
<br>
<br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "Within the settings, you can also define the amount of arguments to return. By default it is set to one, but if your custom WordPress hook supports multiple ones, you can also return all of them.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "This webhook trigger also supports the majority of third-party WordPress hook calls.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
            $description = ob_get_clean();

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_wordpress_hook_definition_type' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_type',
						'type'        => 'select',
						'multiple'    => false,
						'choices'      => array(
                            'action' => WPWHPRO()->helpers->translate('Action', $translation_ident),
                            'filter' => WPWHPRO()->helpers->translate('Filter', $translation_ident),
                        ),
						'label'       => WPWHPRO()->helpers->translate('WordPress hook type', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Select whether your defined hook is a filter or an action.', $translation_ident)
					),
					'wpwhpro_wordpress_hook_definition' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('WordPress hook name', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Add the WordPress hook name that you want to use to fire this trigger on.', $translation_ident)
					),
					'wpwhpro_wordpress_hook_definition_priority' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_priority',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('WordPress hook priority', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Add a custom WordPress hook priority. Default: 10', $translation_ident)
					),
					'wpwhpro_wordpress_hook_definition_arguments' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_arguments',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('WordPress hook arguments', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Define the number of arguments this hook has. Default: 1', $translation_ident)
					),
				)
            );

            return array(
                'trigger'           => 'wordpress_hook',
                'name'              => WPWHPRO()->helpers->translate( 'WordPress hook', 'trigger-custom-action' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo(),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires once your selected WordPress hook (filter or action) has been called.', 'trigger-custom-action' ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            return array( WPWHPRO()->helpers->translate( 'The data construct of your given hook callback.', 'trigger-custom-action' ) ); // Custom content from the action
        }

    }

endif; // End if class_exists check.
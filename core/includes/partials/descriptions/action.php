<?php 

/**
 * The global template used for a single webhook trigger
 *
 * @since 4.2.2
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 * @version 1.0.0
 */

$webhook_name = '';
if( isset( $data['webhook_name'] ) ){
	$webhook_name = esc_html( $data['webhook_name'] );
}

$webhook_slug = '';
if( isset( $data['webhook_slug'] ) ){
	$webhook_slug = esc_html( $data['webhook_slug'] );
}

$steps = array();
if( isset( $data['steps'] ) ){
	$steps = $data['steps'];
}

$tipps = array();
if( isset( $data['tipps'] ) ){
    $tipps = $data['tipps'];
}

$translation_ident = 'action-' . $webhook_slug . '-description';

?>
<?php if( isset( $data['before_description'] ) ) : ?>
<?php echo WPWHPRO()->helpers->translate( $data['before_description'], $translation_ident ); ?>
<?php endif; ?>
<?php echo sprintf( WPWHPRO()->helpers->translate( 'The description is uniquely made for the <strong>%1$s</strong> (%2$s) webhook action.', $translation_ident ), $webhook_name, $webhook_slug ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( 'In case you want to first understand how to setup webhook actions in general, please check out the following manuals:', $translation_ident ); ?>
<br>
<a title='Go to wp-webhooks.com/docs' target='_blank' href='https://wp-webhooks.com/docs/article-categories/get-started/'>https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo sprintf( WPWHPRO()->helpers->translate( 'How to use the <strong>%1$s</strong> (%2$s) webhook action.', $translation_ident ), $webhook_name, $webhook_slug ); ?></h4>
<ol>
	<li><?php echo sprintf( WPWHPRO()->helpers->translate( 'The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>%1$s</strong>.', $translation_ident ), $webhook_slug ); ?></li>
	<?php if( ! empty( $steps ) ) : ?>
		<?php foreach( $steps as $step ) : ?>
			<li><?php echo $step; ?></li>
		<?php endforeach; ?>
	<?php endif; ?>
	<li><?php echo WPWHPRO()->helpers->translate( 'All the other arguments are optional and just extend the functionality of the webhook action.', $translation_ident ); ?></li>
</ol>
<?php if( isset( $data['after_how_to'] ) ) : ?>
    <?php echo $data['after_how_to']; ?>
<?php endif; ?>

<?php if( ! empty( $tipps ) ) : ?>
	<h4><?php echo WPWHPRO()->helpers->translate( 'Tipps', $translation_ident ); ?></h4>
	<ol>
	<?php if( ! empty( $tipps ) ) : ?>
		<?php foreach( $tipps as $tipp ) : ?>
			<li><?php echo $tipp; ?></li>
		<?php endforeach; ?>
	<?php endif; ?>
	</ol>
<?php endif; ?>
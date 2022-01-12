<div class="wpwh-container">

	<?php if( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'logs' ) : ?>
		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2 class="mb-3">Logs <strong>(Pro feature)</strong></h2>
				<p>
					Our log feature allows you to review every incoming and outgoing request that was made by WP Webhooks. This will not only help you to debug, but also gives you further insights about what is happening behind the scenes and where requests come from.
				</p>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li>Track all triggers and actions that have been sent or received by WP Webhooks.</li>
					<li>Track all Flow related triggers and actions.</li>
					<li>Automatically clean logs every 30 days.</li>
					<li>Make debugging easy with detailed request information.</li>
				</ul>
				<a href="https://wp-webhooks.com/docs/article-categories/logs/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Learn more</a>
				<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Go Pro</a>
			</div>
		</div>
	<?php elseif( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'data-mapping' ) : ?>
		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2 class="mb-3">Data Mapping <strong>(Pro feature)</strong></h2>
				<p>
					Manipulate incoming and outgoing webhook data to fit it to the required structure of your service or WP Webhooks.
				</p>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li>Create new data within the payload, map existing data, or format it to your requirements.</li>
					<li>Support for webhook trigger requests and responses</li>
					<li>Support for webhook action requests and responses</li>
					<li>Apply data mapping templates to cookies and webhook URLs</li>
					<li>Supports our Flow feature.</li>
					<li>Whitelist/Blacklist data to minimize payload.</li>
				</ul>
				<a href="https://wp-webhooks.com/docs/article-categories/data-mapping/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Learn more</a>
				<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Go Pro</a>
			</div>
		</div>
	<?php elseif( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'flows' ) : ?>
		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2 class="mb-3">Flows <strong>(Pro feature)</strong></h2>
				<p>
					Create powerful WordPress automation workflows using all of WP Webhooks actions and triggers. This allows you to execute various actions once a trigger of your choice is fired.
				</p>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li>Create unlimited Flows with unlimited actions.</li>
					<li>Supports all of WP Webhooks triggers and actions</li>
					<li>Allows you to re-format data from previous steps</li>
					<li>Use further, common data that is available throughout your site.</li>
				</ul>
				<a href="https://wp-webhooks.com/docs/article-categories/flows/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Learn more</a>
				<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Go Pro</a>
			</div>
		</div>
	<?php elseif( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'whitelist' ) : ?>
		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2 class="mb-3">Whitelist <strong>(Pro feature)</strong></h2>
				<p>
					Optimize your website security by allowing webhook action access only to your chosen IP addresses.
				</p>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li>Whilelist unlimited IP addresses.</li>
					<li>Support for whitelisting IP address ranges.</li>
					<li>Support for tracking unauthorized action requests.</li>
				</ul>
				<a href="https://wp-webhooks.com/docs/article-categories/whitelist/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Learn more</a>
				<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Go Pro</a>
			</div>
		</div>
	<?php else : ?>
		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2 class="mb-3"><strong>Go Pro</strong> and unlock massive benefits!</h2>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li>Create unlimited <a href="https://wp-webhooks.com/features/?utm_source=wpwh&utm_medium=pro-feat&utm_campaign=Go%20Pro" target="_blank" rel="noopener noreferrer" class="text-secondary"><strong>workflow automations</strong></a>.</li>
					<li>Access to all of our <a href="https://wp-webhooks.com/features/?utm_source=wpwh&utm_medium=pro-feat&utm_campaign=Go%20Pro" target="_blank" rel="noopener noreferrer" class="text-secondary"><strong>features</strong></a>.</li>
					<li>Access to all of our <a href="https://wp-webhooks.com/integrations/?utm_source=wpwh&utm_medium=pro-integ&utm_campaign=Go%20Pro" target="_blank" rel="noopener noreferrer" class="text-secondary"><strong>pro integrations</strong></a>.</li>
					<li>Access to all of our <a href="https://wp-webhooks.com/downloads/?utm_source=wpwh&utm_medium=pro-ext&utm_campaign=Go%20Pro" target="_blank" rel="noopener noreferrer" class="text-secondary"><strong>pro extensions</strong></a>.</li>
					<li>Create users and posts with custom post meta (Advanced custom fields supported)</li>
					<li>Enhanced security through <strong>IP whitelists</strong>, security tokens and permission limitations</li>
					<li><strong>Logs</strong> to keep track of all incoming and outgoing webhook calls</li>
					<li>Our powerful <strong>Data Mapping Engine</strong> to directly integrate services with your WordPress website</li>
					<li><strong>Bulk webhook action:</strong> Fire multiple webhook actions within a single call</li>
					<li><strong>Custom button:</strong> Create a button that can trigger a webhook</li>
					<li><strong>Custom link:</strong> Create a link that can trigger a webhook</li>
					<li><strong>Whitelabel</strong> feature (For unlimited license holders)</li>
				</ul>
				<a href="https://wp-webhooks.com/compare-wp-webhooks-pro/?utm_source=wpwh&utm_medium=tab-pro&utm_campaign=Go%20Pro" target="_blank" class="wpwh-btn wpwh-btn--secondary" rel="noopener noreferrer">Go Pro</a>
			</div>
		</div>
	<?php endif; ?>

</div>
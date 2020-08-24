<div class="wrap">

    <?php Auto_Login_Link::check_notice(TRUE); ?>

    <h1 class="wp-heading-inline"><?php echo $title; ?></h1>
    <a href="<?php echo admin_url('admin.php?page=' .  Auto_Login_Link::PLUGIN_SLUG . '&tab=create'); ?>" class="page-title-action"><?php echo __('Add New', Auto_Login_Link::LANGUAGE_DOMAIN); ?></a>
    <form method="post">
		<?php wp_nonce_field('bulk-action', 'auto-login-link-nonce'); ?>
        <?php $list_table->display(); ?>
    </form>

    <style type="text/css">
        .wp-list-table .column-id { width: 3%; }
        .wp-list-table .column-url { width: 40%; }
        .wp-list-table .column-user { width: 60%; }
    </style>
</div>
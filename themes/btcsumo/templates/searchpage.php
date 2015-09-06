<?php
/*
Template Name: Search Page
*/
?>

<?php get_template_part( 'templates/page', 'header' ); ?>

<div class="alert alert-info">
	<?php get_search_form(); ?>
	<?php esc_html_e( 'Search for bitcoin news.', 'btcsumo' ); ?>
</div>

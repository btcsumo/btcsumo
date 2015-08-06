<?php get_template_part( 'templates/page', 'header' ); ?>

<div class="alert alert-warning">
  <?php _e( 'Sorry, but the page you were trying to view does not exist.', 'btcsumo' ); ?>
  <br>
  <br>
  <a href="<?= home_url(); ?>"><?php _e( 'Go back home.', 'btcsumo' ); ?></a>
</div>
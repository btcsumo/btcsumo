<?php get_template_part( 'templates/page', 'header' ); ?>

<div class="alert alert-warning">
  <?= __( 'Sorry, but the page you were trying to view does not exist.', 'btcsumo' ); ?>
  <br>
  <br>
  <a href="<?= home_url(); ?>"><?= __( 'Go back home.', 'btcsumo' ); ?></a>
</div>
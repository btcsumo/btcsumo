<?php use Roots\Sage\Assets; ?>
<footer class="footer content-info" role="contentinfo">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <ul class="powered-by">
          <li><?php _e( 'Powered by:', 'btcsumo' ); ?>
          <li><a href="http://www.wordpress.org/" target="_blank" rel="nofollow">
            <img src="<?= Assets\asset_path( 'images/logos/wordpress.png' ); ?>" alt="WordPress">
          </a>
          <li><a href="http://www.roots.io/sage/" target="_blank" rel="nofollow">
            <img src="<?= Assets\asset_path( 'images/logos/sage.png' ); ?>" alt="Sage">
          </a>
          <li><a href="http://getbootstrap.com/" target="_blank" rel="nofollow">
            <img src="<?= Assets\asset_path( 'images/logos/bootstrap.png' ); ?>" alt="Bootstrap">
          </a>
        </ul>
      </div>
    </div>
  </div>
</footer>
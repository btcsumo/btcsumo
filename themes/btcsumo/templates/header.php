<?php
  use BTCSumo\Assets;
  use BTCSumo\BitcoinTicker;
?>

<header class="banner navbar navbar-default navbar-static-top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only"><?php _e( 'Toggle navigation', 'btcsumo' ); ?></span>
        <span><?php _e( 'Menu', 'btcsumo' ); ?></span>
        <span class="fa fa-bars"></span>
      </button>
      <a class="navbar-brand" href="<?= esc_url( home_url( '/' ) ); ?>"><img src="<?= Assets\asset_path( 'images/logo.png' ); ?>"></a>
    </div>

    <nav class="collapse navbar-collapse" role="navigation">
      <?php
      if ( has_nav_menu( 'primary_navigation' ) ) {
        wp_nav_menu( [ 'theme_location' => 'primary_navigation', 'walker' => new WP_Bootstrap_Nav_Walker(), 'menu_class' => 'nav navbar-nav' ] );
      }
      ?>
    </nav>

    <div id="bitcoin-ticker" class="btn-group hidden-xs">
    <?php
    if ( $tickers = BitcoinTicker\get_bitcoin_tickers() ) {
      $ul = '<ul id="bitcoin-ticker-list" class="dropdown-menu dropdown-menu-right">';
      $active_ticker = 'bitstamp';
      foreach ( $tickers as $ticker ) {
        if ( $active_ticker === $ticker->id ) {
          ?>
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only"><?php _e( 'Toggle bitcoin prices', 'btcsumo' ); ?></span>
            <span id="bitcoin-ticker-price"><span><?= $ticker->cur; ?></span><?= $ticker->price; ?></span> <span class="caret"></span>
          </button>
          <?php
        }
        // Unset any info we don't need.
        unset( $ticker->url );

        $ul .= sprintf( '<li data-info="%3$s"%2$s><a>%1$s</a>',
          $ticker->name,
          ( $active_ticker === $ticker->id ) ? ' class="active"' : '',
          esc_attr( json_encode( $ticker ) )
        );
      }
      $ul .= '</ul>';
      echo $ul;
    }
    ?>
    </div>

  </div>
</header>

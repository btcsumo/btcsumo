<?php

$feeds_list = '
Cryptocoins News
https://www.cryptocoinsnews.com
https://www.cryptocoinsnews.com/category/news/feed

CoinDesk
http://coindesk.com
http://feeds.feedburner.com/CoinDesk

Follow The Coin
http://www.followthecoin.com
http://www.followthecoin.com/feed/atom

Coin Telegraph
http://cointelegraph.com
http://cointelegraph.com/rss

Bitcoin Magazine
https://bitcoinmagazine.com
http://feeds.feedburner.com/BitcoinMagazine

Tyra CPA
https://www.tyracpa.com
https://www.tyracpa.com/feed

Bitcoin Chaser
http://bitcoinchaser.com
http://bitcoinchaser.com/feed

Bitcoinist
http://bitcoinist.net
http://bitcoinist.net/feed

Bitcoin Warrior
http://bitcoinwarrior.net
http://bitcoinwarrior.net/feed

CEX.IO
http://blog.cex.io
http://blog.cex.io/feed

Coins Source
http://www.coinssource.com
http://www.coinssource.com/rss

Altcoin Speculation
http://altcoinspeculation.com
http://altcoinspeculation.com/feed

CoinFinance
http://www.coinfinance.com
http://feeds.feedburner.com/CoinFinance

Bitcoin Outpost
http://bitcoinoutpost.com
http://bitcoinoutpost.com/?feed=podcast

Bitcoin Feeds
http://bitcoinfeeds.com
http://bitcoinfeeds.com/feed

The Blogchain
http://theblogchain.com
http://theblogchain.com/feed

Cryptocoin Talk
https://cryptocointalk.com
https://cryptocointalk.com/rss/blog

Altcoin Fever
http://www.altcoinfever.com
http://www.altcoinfever.com/feed

Brave New Coin
http://bravenewcoin.com
http://bravenewcoin.com/news/rss

Crypto Mining Blog
http://cryptomining-blog.com
http://cryptomining-blog.com/feed

Inside Bitcoins
http://insidebitcoins.com
http://insidebitcoins.com/feed

Live Bitcoin News
http://www.livebitcoinnews.com
http://www.livebitcoinnews.com/feed

Coin Center
https://coincenter.org
https://coincenter.org/category/blog/feed

Best Bitcoin Wallet
http://bestbitcoinwallet.info
http://bestbitcoinwallet.info/feed

Bitcoin Beginner
http://blog.bitcoinbeginner.com
http://blog.bitcoinbeginner.com/rss

Bitcoin Money
http://bitcoinmoney.com
http://bitcoinmoney.com/rss

Signal Strength Finance
https://signalstrengthfinance.wordpress.com
https://signalstrengthfinance.wordpress.com/feed

Suitpossum
http://suitpossum.blogspot.co.uk/m
http://feeds.feedburner.com/SuitpossumFragmentsOfFinancialSubversion

BTC Trading
https://btctrading.wordpress.com
https://btctrading.wordpress.com/feed

Bitcoin Blog
http://www.bitcoinblog.me
http://www.bitcoinblog.me/feed

Digital Money Times
http://digitalmoneytimes.com
http://digitalmoneytimes.com/feed

CoinBuzz
http://www.coinbuzz.com
http://www.coinbuzz.com/feed

Coin Report
https://coinreport.net
https://coinreport.net/feed

Bitcoin Scientist
http://bitcoinscientist.com
http://feeds.feedburner.com/BitcoinScientist

BitCoinada
http://bitcoinada.com
http://bitcoinada.com/feed

Bitcoin Weekly
http://bitcoinweekly.com
http://bitcoinweekly.com/rss/10cj1e7m

Bitcoin Reporter
http://bitcoinreporter.com
http://bitcoinreporter.com/?format=feed&type=rss

All Coins News
http://allcoinsnews.com
http://allcoinsnews.com/feed
';

$feeds_arr = array_values( array_filter( explode( "\n", $feeds_list ) ) );

require $_SERVER['DOCUMENT_ROOT'] . '/wp/wp-load.php';

$inserted_items = [];
$skipped_items = [];
$failed_items = [];

for ( $i = 0; $i < count( $feeds_arr ); $i += 3 ) {
  $title    = $feeds_arr[ $i ];

  if ( $post = get_page_by_title( $title, OBJECT, 'feeds' ) ) {
    $skipped_items[ $post->ID ] = $title;
    continue;
  }

  $site_url = $feeds_arr[ $i + 1 ];
  $feed_url = $feeds_arr[ $i + 2 ];

  // Create post object
  $post = array(
    'post_title'  => $title,
    'post_type'   => 'feeds',
    'post_status' => 'publish',
    'post_author' => 1
  );

  if ( $post_id = wp_insert_post( $post, false ) ) {
    add_post_meta( $post_id, 'feed-active', count( $inserted_items ) < 7 );
    add_post_meta( $post_id, 'feed-site-url', $site_url );
    add_post_meta( $post_id, 'feed-feed-url', $feed_url );
    $inserted_items[ $post_id ] = $title;
  } else {
    $failed_items[] = $title;
  }
}

wp_die( sprintf( 'Inserted Items: %d<br>Skipped Items: %d<br>Failed Items: %d<br><br><a href="%s">Home</a>', count( $inserted_items ), count( $skipped_items ), count( $failed_items ), home_url() ) );

?>




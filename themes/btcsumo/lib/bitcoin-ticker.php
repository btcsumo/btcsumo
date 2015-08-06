<?php

namespace BTCSumo\BitcoinTicker;

// Schedule to update the bitcoin prices every 5 minutes.
add_action( 'update_bitcoin_tickers', __NAMESPACE__ . '\\update_bitcoin_tickers' );
if ( ! wp_next_scheduled( 'update_bitcoin_tickers' ) ) {
  wp_schedule_event( time(), '5min', 'update_bitcoin_tickers' );
}
// If we ever need to clear the schedule, we can just call "wp_clear_scheduled_hook( 'update_bitcoin_tickers' );" here.

/**
 * Update the bitcoin tickers by fetching the current price using each ticker's API.
 * @return array List of updated tickers.
 */
function update_bitcoin_tickers() {
  $tickers = [
    (object) [ 'id' => 'btce',     'name' => 'BTC-e',     'cur' => '$',   'url' => 'https://btc-e.com/api/3/ticker/btc_usd',
      'cb' => function( $data ) { return $data->btc_usd->last; } ], // https://btc-e.com/api/3/docs#ticker
    (object) [ 'id' => 'btcchina', 'name' => 'BTC China', 'cur' => '¥',   'url' => 'https://data.btcchina.com/data/ticker?market=btccny',
      'cb' => function( $data ) { return $data->ticker->last; } ], // http://btcchina.org/api-market-data-documentation-en
    (object) [ 'id' => 'bitfinex', 'name' => 'Bitfinex',  'cur' => '$',   'url' => 'https://api.bitfinex.com/v1/pubticker/BTCUSD',
      'cb' => function( $data ) { return $data->last_price; } ], // https://www.bitfinex.com/pages/api
    (object) [ 'id' => 'bitstamp', 'name' => 'Bitstamp',  'cur' => '$',   'url' => 'https://www.bitstamp.net/api/ticker_hour/',
      'cb' => function( $data ) { return $data->last; } ], // https://www.bitstamp.net/api/
    (object) [ 'id' => 'cavirtex', 'name' => 'CAVIRTEX',  'cur' => 'CAD', 'url' => 'https://cavirtex.com/api2/ticker.json?currencypair=BTCCAD',
      'cb' => function( $data ) { return $data->ticker->BTCCAD->last; } ], // https://www.cavirtex.com/api_information#ticker_api
    (object) [ 'id' => 'coinbase', 'name' => 'Coinbase',  'cur' => '$',   'url' => 'https://coinbase.com/api/v1/prices/spot_rate',
      'cb' => function( $data ) { return $data->amount; } ], // https://developers.coinbase.com/api/v1#get-the-spot-price-of-bitcoin
    (object) [ 'id' => 'itbit',    'name' => 'itBit',     'cur' => '$',   'url' => 'https://api.itbit.com/v1/markets/XBTUSD/ticker',
      'cb' => function( $data ) { return $data->lastPrice; } ], // https://api.itbit.com/docs#market-data-get-ticker
    (object) [ 'id' => 'lakebtc',  'name' => 'LakeBTC',   'cur' => '$',   'url' => 'https://www.lakebtc.com/api_v1/ticker',
      'cb' => function( $data ) { return $data->USD->last; } ], // https://www.lakebtc.com/s/api
    (object) [ 'id' => 'kraken',   'name' => 'Kraken',    'cur' => '$',   'url' => 'https://api.kraken.com/0/public/Ticker?pair=XXBTZUSD',
      'cb' => function( $data ) { return $data->result->XXBTZUSD->c[0]; } ], // https://www.kraken.com/en-us/help/api#get-ticker-info
    (object) [ 'id' => 'okcoin',   'name' => 'OKCoin',    'cur' => '$',   'url' => 'https://www.okcoin.com/api/v1/ticker.do?symbol=btc_usd',
      'cb' => function( $data ) { return $data->ticker->last; } ] // https://www.okcoin.com/about/rest_api.do
  ];

  foreach ( $tickers as $ticker ) {
    if ( $data = json_decode( get_curl_data( $ticker->url ) ) ) {
      $ticker->price = number_format( call_user_func( $ticker->cb, $data ), 2 );
      unset( $ticker->cb );
    }
  }

  // Save the ticker data.
  update_option( 'bitcoin_tickers', $tickers );

  // Save the time of each update to know if the cron is working well, keep only the latest 10.
  $btu = get_option( 'bitcoin_tickers_updated', [] );
  array_unshift( $btu, date( 'YmdHis' ) );
  update_option( 'bitcoin_tickers_updated', array_slice( $btu, 0, 10 ) );

  return $tickers;
}

/**
 * Get the list of bitcoin tickers.
 * @return array List of bitcoin tickers.
 */
function get_bitcoin_tickers() {
  if ( $tickers = get_option( 'bitcoin_tickers' ) ) {
    return $tickers;
  }
  return update_bitcoin_tickers();
}

/**
 * AJAX call to get the list of bitcoin tickers.
 */
function ajax_get_bitcoin_tickers() {
  wp_send_json_success( get_bitcoin_tickers() );
}
add_action( 'wp_ajax_ajax_get_bitcoin_tickers', __NAMESPACE__ . '\\ajax_get_bitcoin_tickers' );
add_action( 'wp_ajax_nopriv_ajax_get_bitcoin_tickers', __NAMESPACE__ . '\\ajax_get_bitcoin_tickers' );

/**
 * Get URL data via CURL.
 * @param  string $url URL to load.
 * @return string      Loaded content.
 */
function get_curl_data( $url ) {
  $ch = curl_init( $url );

  curl_setopt( $ch, CURLOPT_FAILONERROR,    true );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
  curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );

  $data = curl_exec( $ch );
  curl_close( $ch );
  return $data;
}

?>

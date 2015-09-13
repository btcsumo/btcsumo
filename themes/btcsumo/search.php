<?php

use BTCSumo\Feeds;

$search_strings = array_filter(
	explode( ' ', get_query_var( 's' ) ),
	function( $string ) {
		return trim( $string );
	}
);
// Clean up the search query.
set_query_var( 's', implode( ' ', $search_strings ) );

// Get all the active feeds.
$feed_posts = get_posts([
	'posts_per_page' => -1,
	'post_type'      => 'feeds',
	'meta_key'       => 'feed-active',
	'meta_value'     => true
]);

$found_feed_items = [];

foreach ( $feed_posts as $feed_post ) {
	if ( $feed_items = get_post_meta( $feed_post->ID, 'feed-feed-items', true ) ) {
		foreach ( $feed_items as $feed_item ) {
			$new_title = $feed_item->title;
			foreach ( $search_strings as $search_string ) {
				// Highlight the found search strings.
				$pos = stripos( $new_title, $search_string );
				if ( false !== $pos ) {
					$new_title = sprintf(
						'%s<b>%s</b>%s',
						substr( $new_title, 0, $pos ),
						substr( $new_title, $pos, strlen( $search_string ) ),
						substr( $new_title, $pos + strlen( $search_string ) )
					);
				}
			}

			if ( $new_title !== $feed_item->title ) {
				$feed_item->title = $new_title;
				$feed_item->feed_title = $feed_post->post_title;
				$feed_item->feed_site_url = get_post_meta( $feed_post->ID, 'feed-site-url', true );
				$found_feed_items[] = $feed_item;
			}
		}
	}
}

// Sort all entries by date, newest first.
usort( $found_feed_items, function( $a, $b ) {
	return ( ( $a->timestamp >= $b->timestamp ) ? -1 : 1 );
} );

?>

<?php if ( empty( $found_feed_items ) ) : ?>
	<div class="alert alert-warning">
		<?php get_search_form(); ?>
		<?php esc_html_e( 'Sorry, no results were found.', 'btcsumo' ); ?>
	</div>
<?php else : ?>
	<div class="alert alert-success">
		<?php get_search_form(); ?>
		<?php
			printf( '%d %s',
				count( $found_feed_items ),
				_n( 'entry found', 'entries found', count( $found_feed_items ), 'btcsumo' )
			);
		?>
	</div>
	<div class="row feed-boxes">
		<div class="feed-box col-xs-12">
			<div class="panel panel-default">
				<ul class="feed-list list-group">
					<?php
					// Loop through and render each feed item.
					foreach ( $found_feed_items as $feed_item ) {
						include locate_template( 'templates/feed-item-search.php', false, false );
					}
					?>
				</ul>
			</div>
		</div>
	</div>
<?php endif; ?>

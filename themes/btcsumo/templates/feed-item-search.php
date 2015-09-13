<li class="feed-item list-group-item">
  <a class="feed-item-date label" href="<?= $feed_item->feed_site_url; ?>" target="_blank"><?= $feed_item->feed_title; ?> - <?= date( 'j M', $feed_item->timestamp ); ?></a>
  <a href="<?= $feed_item->permalink; ?>" target="_blank"><?= $feed_item->title; ?></a>
</li>

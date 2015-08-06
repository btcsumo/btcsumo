<li class="feed-item list-group-item">
  <span class="feed-item-date label" title="<?= date( 'j F Y, H:i:s', $feed_item->timestamp ); ?>"><?= date( 'j M', $feed_item->timestamp ); ?></span>
  <a href="<?= $feed_item->permalink; ?>" target="_blank"><?= $feed_item->title; ?></a>
</li>

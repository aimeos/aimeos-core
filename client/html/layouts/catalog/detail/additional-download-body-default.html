<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

if( isset( $this->detailProductItem ) ) {
	$items = $this->detailProductItem->getRefItems( 'media', null, 'download' );
} else {
	$items = array();
}

$mediaItems = $this->get( 'detailProductMediaItems', array() );
$enc = $this->encoder();

?>
<div class="additional-box">
<?php if( count( $items ) > 0 ) : ?>
	<h2 class="header downloads"><?php echo $enc->html( $this->translate( 'client/html', 'Downloads' ), $enc::TRUST ); ?></h2>
	<ul class="content downloads">
<?php foreach( $items as $id => $item ) : ?>
<?php	if( isset( $mediaItems[$id] ) ) { $item = $mediaItems[$id]; } ?>
		<li class="item">
			<a href="<?php echo $this->content( $item->getUrl() ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>">
				<img class="media-image" src="<?php echo $this->content( $item->getPreview() ); ?>" alt="<?php echo $enc->attr( $item->getName() ); ?>" />
				<span class="media-name"><?php echo $enc->html( $item->getName() ); ?></span>
			</a>
		</li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php echo $this->get( 'downloadBody' ); ?>
</div>

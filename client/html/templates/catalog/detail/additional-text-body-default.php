<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

if( isset( $this->detailProductItem ) ) {
	$textItems = $this->detailProductItem->getRefItems( 'text', 'long' );
} else {
	$textItems = array();
}

$enc = $this->encoder();

?>
<?php $this->block()->start( 'catalog/detail/additional/text' ); ?>
<div class="additional-box">
<?php if( count( $textItems ) > 0 ) : ?>
	<h2 class="header description"><?php echo $enc->html( $this->translate( 'client', 'Description' ), $enc::TRUST ); ?></h2>
	<div class="content description">
<?php foreach( $textItems as $textItem ) : ?>
		<div class="long item"><?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?></div>
<?php endforeach; ?>
	</div>
<?php endif; ?>
<?php echo $this->get( 'textBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/detail/additional/text' ); ?>

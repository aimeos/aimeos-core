<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$productItems = $this->get( 'promoItems', array() );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );

?>
<?php $this->block()->start( 'catalog/lists/promo' ); ?>
<?php if( !empty( $productItems ) ) : ?>
<section class="catalog-list-promo">
	<h2 class="header"><?php echo $this->translate( 'client', 'Top seller' ); ?></h2>
	<ul class="promo-items"><!--
<?php	foreach( $productItems as $id => $productItem ) : ?>
<?php		$firstImage = true; $params = array( 'd_name' => $productItem->getName( 'url' ), 'd_prodid' => $id ); ?>
		--><li class="product" itemscope="" itemtype="http://schema.org/Product">
			<a href="<?php echo $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, array(), $detailConfig ) ); ?>">
				<div class="media-list">
<?php		foreach( $productItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
<?php			$mediaUrl = $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>
<?php			if( $firstImage === true ) : $firstImage = false; ?>
					<noscript>
						<div class="media-item" style="background-image: url('<?php echo $mediaUrl; ?>')" itemscope="" itemtype="http://schema.org/ImageObject">
							<meta itemprop="contentUrl" content="<?php echo $mediaUrl; ?>" />
						</div>
					</noscript>
					<div class="media-item lazy-image" data-src="<?php echo $mediaUrl; ?>"></div>
<?php			else : ?>
					<div class="media-item" data-src="<?php echo $mediaUrl; ?>"></div>
<?php			endif; ?>
<?php		endforeach; ?>
				</div>
				<div class="text-list">
					<h2 itemprop="name"><?php echo $enc->html( $productItem->getName(), $enc::TRUST ); ?></h2>
<?php		foreach( $productItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
					<div class="text-item" itemprop="description">
<?php			echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?><br/>
					</div>
<?php		endforeach; ?>
				</div>
				<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<div class="stock" data-prodid="<?php echo $id; ?>"></div>
					<div class="price-list">
<?php		echo $this->partial( $this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ), array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) ) ); ?>
					</div>
				</div>
			</a>
		</li><!--
<?php	endforeach; ?>
	--></ul>
<?php	echo $this->get( 'promoBody' ); ?>
</section>
<?php endif; ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/lists/promo' ); ?>

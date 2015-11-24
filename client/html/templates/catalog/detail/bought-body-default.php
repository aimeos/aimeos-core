<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();
$productItems = $this->get( 'boughtItems', array() );
$positionItems = $this->get( 'boughtPosItems', array() );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );

?>
<?php if( !empty( $productItems ) || $this->boughtBody != '' ) : ?>
<section class="catalog-detail-bought">
	<h2 class="header"><?php echo $this->translate( 'client/html', 'Other customers also bought' ); ?></h2>
	<ul class="bought-items">
<?php	foreach( $positionItems as $id => $item ) : ?>
<?php		if( isset( $productItems[$id] ) ) : $productItem = $productItems[$id]; ?>
		<li class="bought-item">
<?php			$params = array( 'd_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId() ); ?>
			<a href="<?php echo $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, array(), $detailConfig ) ); ?>">
<?php			$mediaItems = $productItem->getRefItems( 'media', 'default', 'default' ); ?>
<?php			if( ( $mediaItem = reset( $mediaItems ) ) !== false ) : ?>
				<div class="media-item" style="background-image: url('<?php echo $this->content( $mediaItem->getPreview() ); ?>')"></div>
<?php			else : ?>
				<div class="media-item"></div>
<?php			endif; ?>
				<h3 class="name"><?php echo $enc->html( $productItem->getName(), $enc::TRUST ); ?></h3>
				<div class="price-list">
<?php			echo $this->partial( 'client/html/common/partials/price', 'common/partials/price-default.php', array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) ) ); ?>
				</div>
			</a>
		</li>
<?php		endif; ?>
<?php	endforeach; ?>
	</ul>
<?php echo $this->boughtBody; ?>
</section>
<?php endif; ?>

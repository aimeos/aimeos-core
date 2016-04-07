<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$id = $css = '';
if( isset( $this->detailProductItem ) )
{
	$id = $this->detailProductItem->getId();
	$conf = $this->detailProductItem->getConfig();
	$css = ( isset( $conf['css-class'] ) ? $conf['css-class'] : '' );
}

?>
<?php $this->block()->start( 'catalog/detail' ); ?>
<section class="aimeos catalog-detail" itemscope="" itemtype="http://schema.org/Product">
<?php if( isset( $this->detailErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->detailErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<article class="product <?php echo $css; ?>" data-id="<?php echo $id; ?>">
<?php echo $this->get( 'detailBody' ); ?>
	</article>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/detail' ); ?>

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$classes = '';
foreach( (array) $this->get( 'listCatPath', array() ) as $cat )
{
	$config = $cat->getConfig();
	if( isset( $config['css-class'] ) ) {
		$classes .= ' ' . $config['css-class'];
	}
}

?>
<?php $this->block()->start( 'catalog/lists' ); ?>
<section class="aimeos catalog-list<?php echo $enc->attr( $classes ); ?>">
<?php if( isset( $this->listErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->listErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php echo $this->get( 'listBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/lists' ); ?>

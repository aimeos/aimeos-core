<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$classes = '';
foreach( (array) $this->get( 'stageCatPath', array() ) as $cat )
{
	$config = $cat->getConfig();
	if( isset( $config['css-class'] ) ) {
		$classes .= ' ' . $config['css-class'];
	}
}

?>
<?php $this->block()->start( 'catalog/stage' ); ?>
<section class="aimeos catalog-stage<?php echo $enc->attr( $classes ); ?>">
<?php if( isset( $this->stageErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->stageErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php echo $this->get( 'stageBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/stage' ); ?>

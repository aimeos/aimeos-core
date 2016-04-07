<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'basket/related' ); ?>
<section class="aimeos basket-related">
<?php if( isset( $this->relatedErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->relatedErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<h1><?php echo $enc->html( $this->translate( 'client', 'Related' ), $enc::TRUST ); ?></h1>
<?php echo $this->get( 'relatedBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'basket/related' ); ?>

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'basket/mini' ); ?>
<section class="aimeos basket-mini">
<?php if( ( $errors = $this->get( 'miniErrorList', array() ) ) !== array() ) : ?>
	<ul class="error-list">
<?php foreach( $errors as $error ) : ?>
		<li class="error-item"><?php echo $enc->html( $error ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<h1><?php echo $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ); ?></h1>
<?php echo $this->get( 'miniBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'basket/mini' ); ?>

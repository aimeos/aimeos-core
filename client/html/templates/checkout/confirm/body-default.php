<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'checkout/confirm' ); ?>
<section class="aimeos checkout-confirm">
<?php if( isset( $this->confirmErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->confirmErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<h1><?php echo $enc->html( $this->translate( 'client', 'Confirmation' ), $enc::TRUST ); ?></h1>
<?php echo $this->get( 'confirmBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/confirm' ); ?>

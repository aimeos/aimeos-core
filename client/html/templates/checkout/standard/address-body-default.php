<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

?>
<section class="checkout-standard-address">
	<h1><?php echo $enc->html( $this->translate( 'client', 'address' ), $enc::TRUST ); ?></h1>
	<p class="note"><?php echo $enc->html( $this->translate( 'client', 'Fields with an * are mandatory' ), $enc::TRUST ); ?></p>
<?php echo $this->get( 'addressBody' ); ?>
	<div class="button-group">
		<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->get( 'standardUrlBack' ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ); ?></a>
		<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ); ?></button>
	</div>
</section>

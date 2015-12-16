<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

?>
<section class="checkout-standard-summary common-summary">
	<h1><?php echo $enc->html( $this->translate( 'client', 'summary' ), $enc::TRUST ); ?></h1>
	<p class="note"><?php echo $enc->html( $this->translate( 'client', 'Please check your order' ), $enc::TRUST ); ?></p>
	<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'cs_order' ) ) ); ?>" value="1" />
<?php echo $this->get( 'summaryBody' ); ?>
	<div class="button-group">
		<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->get( 'standardUrlBack' ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ); ?></a> 
		<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Buy now' ), $enc::TRUST ); ?></button>
	</div>
</section>

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

if( $this->get( 'standardUrlExternal', true ) )
{
	$namefcn = function( $view, $key ) {
		return $key;
	};
}
else
{
	$namefcn = function( $view, $key ) {
		return $view->formparam( array( $key ) );
	};
}

$testfcn = function( $list, $key, $default = '' ) {
	return ( isset( $list[$key] ) ? $list[$key] : $default );
};

$enc = $this->encoder();
$public = $hidden = array();
$errors = $this->get( 'standardErrorList', array() );
$params = $this->get( 'standardProcessParams', array() );

foreach( $params as $key => $item )
{
	if( $item->isPublic() ) {
		$public[$key] = $item;
	} else {
		$hidden[$key] = $item;
	}
}

/** client/html/checkout/standard/process/validate
 * List of regular expressions for validating the payment details
 *
 * To validate the payment input data of the customer, an individual Perl
 * compatible regular expression (http://php.net/manual/en/pcre.pattern.php)
 * can be applied to each field. Available fields are:
 * * payment.cardno
 * * payment.cvv
 * * payment.expirymonthyear
 *
 * To validate e.g the CVV security code, you can define a regular expression
 * like this to allow only three digits:
 *  client/html/checkout/standard/process/validate/payment.cvv = '^[0-9]{3}$'
 *
 * Several regular expressions can be defined line this:
 *  client/html/checkout/standard/process/validate = array(
 *   'payment.cardno' = '^[0-9]{16,19}$',
 *   'payment.cvv' = '^[0-9]{3}$',
 *  )
 *
 * Don't add any delimiting characters like slashes (/) to the beginning or the
 * end of the regular expression. They will be added automatically. Any slashes
 * inside the expression must be escaped by backlashes, i.e. "/".
 *
 * @param array Associative list of field names and regular expressions 
 * @since 2015.07
 * @category User
 * @category Developer
 * @see client/html/checkout/standard/address/validate
 */
$defaultRegex = array( 'payment.cardno' => '^[0-9]{16,19}$', 'payment.cvv' => '^[0-9]{3}$' );
$regex = $this->config( 'client/html/checkout/standard/process/validate', $defaultRegex );

?>
<div class="checkout-standard-process">
	<h2><?php echo $enc->html( $this->translate( 'client', 'Payment' ), $enc::TRUST ); ?></h2>
<?php if( !empty( $errors ) ) : ?>
	<p class="order-notice"><?php echo $enc->html( $this->translate( 'client', 'Processing the payment failed' ), $enc::TRUST ); ?></p>
<?php elseif( !empty( $public ) ) : ?>
	<p class="order-notice"><?php echo $enc->html( $this->translate( 'client', 'Please enter your payment details' ), $enc::TRUST ); ?></p>
<?php else : ?>
	<p class="order-notice"><?php echo $enc->html( $this->translate( 'client', 'You will now be forwarded to the next step' ), $enc::TRUST ); ?></p>
<?php endif; ?>
<?php foreach( $hidden as $key => $item ) : ?>
	<input name="<?php echo $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>" type="hidden" value="<?php echo $enc->attr( $item->getDefault() ); ?>" />
<?php endforeach; ?>
	<ul class="form-list">
<?php foreach( $public as $key => $item ) : ?>
		<li class="form-item <?php echo $key . ( $item->isRequired() ? ' mandatory' : ' optional' ); ?>" data-regex="<?php echo $testfcn( $regex, $key ); ?>">
			<label for="process-<?php echo $key; ?>"><?php echo $enc->html( $this->translate( 'client/code', $item->getCode() ), $enc::TRUST ); ?></label><?php
			switch( $item->getType() ) : case 'select':
				?><select id="process-<?php echo $key; ?>" name="<?php echo $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>">
					<option value=""><?php echo $enc->html( $this->translate( 'client', 'Please select' ) ); ?></option>
<?php				foreach( (array) $item->getDefault() as $option ) : ?>
					<option value="<?php echo $enc->attr( $option ); ?>"><?php echo $enc->html( $option ); ?></option>
<?php				endforeach; ?>
				</select><?php
				break; case 'boolean':
				?><input type="checkbox" id="process-<?php echo $key; ?>" name="<?php echo $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>" value="<?php echo $enc->attr( $item->getDefault() ); ?>" placeholder="<?php echo $enc->attr( $this->translate( 'client/code', $key ) ); ?>" /><?php
				break; case 'integer': case 'number':
				?><input type="number" id="process-<?php echo $key; ?>" name="<?php echo $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>" value="<?php echo $enc->attr( $item->getDefault() ); ?>" placeholder="<?php echo $enc->attr( $this->translate( 'client/code', $key ) ); ?>" /><?php
				break; case 'date': case 'datetime': case 'time':
				?><input type="<?php echo $attribute->getType(); ?>" id="process-<?php echo $key; ?>" name="<?php echo $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>" value="<?php echo $enc->attr( $item->getDefault() ); ?>" placeholder="<?php echo $enc->attr( $this->translate( 'client/code', $key ) ); ?>" /><?php
				break; default:
				?><input type="text" id="process-<?php echo $key; ?>" name="<?php echo $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>" value="<?php echo $enc->attr( $item->getDefault() ); ?>" placeholder="<?php echo $enc->attr( $this->translate( 'client/code', $key ) ); ?>" />
<?php		endswitch; ?>
		</li>
<?php endforeach; ?>
	</ul>
<?php echo $this->get( 'processBody' ); ?>
	<div class="button-group">
<?php if( !empty( $errors ) ) : ?>
		<a class="standardbutton" href="<?php echo $enc->attr( $this->standardUrlPayment ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ); ?></a> 
		<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Try again' ), $enc::TRUST ); ?></button>
<?php elseif( !empty( $public ) ) : ?>
		<a class="standardbutton" href="<?php echo $enc->attr( $this->standardUrlPayment ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ); ?></a> 
		<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Pay now' ), $enc::TRUST ); ?></button>
<?php else : ?>
		<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Proceed' ), $enc::TRUST ); ?></button>
<?php endif; ?>
	</div>
</div>

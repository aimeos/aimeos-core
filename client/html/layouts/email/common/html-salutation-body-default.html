<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

$salutations = array(
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS,
);

try
{
	/// Payment e-mail form of address with salutation (%1$s), first name (%2$s) and last name (%3$s)
	$msg = $this->translate( 'client/html', 'Dear %1$s %2$s %3$s' );
	$addr = $this->extAddressItem;

	$string = sprintf( $msg,
		( in_array( $addr->getSalutation(), $salutations ) ? $this->translate( 'client/html/code', $addr->getSalutation() ) : '' ),
		$addr->getFirstName(),
		$addr->getLastName()
	);
}
catch( Exception $e )
{
	$string = $this->translate( 'client/html/email', 'Dear Sir or Madam' );
}

?>
<p class="email-common-salutation content-block">
<?php echo $enc->html( $string ); ?>
<?php echo $this->get( 'salutationBody' ); ?>
</p>

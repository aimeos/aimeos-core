<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$salutations = array(
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS,
	\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS,
);

try
{
	/// Payment e-mail form of address with salutation (%1$s), first name (%2$s) and last name (%3$s)
	$msg = $this->translate( 'client', 'Dear %1$s %2$s %3$s' );
	$addr = $this->extAddressItem;

	$string = sprintf( $msg,
		( in_array( $addr->getSalutation(), $salutations ) ? $this->translate( 'client/code', $addr->getSalutation() ) : '' ),
		$addr->getFirstName(),
		$addr->getLastName()
	);
}
catch( Exception $e )
{
	$string = $this->translate( 'client/html/email', 'Dear Sir or Madam' );
}

?>
<?php $this->block()->start( 'email/common/text/salutation' ); ?>
<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'salutationBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/salutation' ); ?>

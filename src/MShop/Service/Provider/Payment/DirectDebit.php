<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Payment provider for direct debit orders.
 *
 * @package MShop
 * @subpackage Service
 */
class DirectDebit
	extends \Aimeos\MShop\Service\Provider\Payment\Base
	implements \Aimeos\MShop\Service\Provider\Payment\Iface
{
	private array $feConfig = array(
		'directdebit.accountowner' => array(
			'code' => 'directdebit.accountowner',
			'internalcode' => 'accountowner',
			'label' => 'Account owner',
			'default' => '',
			'required' => true
		),
		'directdebit.accountno' => array(
			'code' => 'directdebit.accountno',
			'internalcode' => 'accountno',
			'label' => 'Account number',
			'default' => '',
			'required' => true
		),
		'directdebit.bankcode' => array(
			'code' => 'directdebit.bankcode',
			'internalcode' => 'bankcode',
			'label' => 'Bank code',
			'default' => '',
			'required' => true
		),
		'directdebit.bankname' => array(
			'code' => 'directdebit.bankname',
			'internalcode' => 'bankname',
			'label' => 'Bank name',
			'default' => '',
			'required' => true
		),
	);


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Iface $basket ) : array
	{
		$feconfig = $this->feConfig;

		try
		{
			$address = $basket->getAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT, 0 );

			if( ( $fn = $address->getFirstname() ) !== '' && ( $ln = $address->getLastname() ) !== '' ) {
				$feconfig['directdebit.accountowner']['default'] = $fn . ' ' . $ln;
			}
		}
		catch( \Aimeos\MShop\Order\Exception $e ) { ; } // If address isn't available

		return $this->getConfigItems( $feconfig );
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes ) : array
	{
		return $this->checkConfig( $this->feConfig, $attributes );
	}


	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item with attributes added
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem,
		array $attributes ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		$orderServiceItem->addAttributeItems( $this->attributes( $attributes ) );

		if( ( $attrItem = $orderServiceItem->getAttributeItem( 'directdebit.accountno' ) ) !== null )
		{
			$attrList = [$attrItem->getCode() => $attrItem->getValue()];
			$orderServiceItem->addAttributeItems( $this->attributes( $attrList, 'hidden' ) );

			if( is_string( $value = $attrItem->getValue() ) )
			{
				$len = strlen( $value );
				$xstr = ( $len > 3 ? str_repeat( 'X', $len - 3 ) : '' );

				$attrItem->setValue( $xstr . substr( $value, -3 ) );
				$orderServiceItem->setAttributeItem( $attrItem );
			}
		}

		return $orderServiceItem;
	}


	/**
	 * Executes the payment again for the given order if supported.
	 * This requires support of the payment gateway and token based payment
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 */
	public function repay( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED );
	}


	/**
	 * Updates the orders for whose status updates have been received by the confirmation page
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object with parameters and request body
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order item that should be updated
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 * @throws \Aimeos\MShop\Service\Exception If updating the orders failed
	 */
	public function updateSync( \Psr\Http\Message\ServerRequestInterface $request,
		\Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $order->getStatusPayment() < 0 ) {
			$order->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED );
		}

		return $order;
	}
}

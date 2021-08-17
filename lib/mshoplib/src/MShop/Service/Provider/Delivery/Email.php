<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * Email delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class Email
	extends \Aimeos\MShop\Service\Provider\Delivery\Base
	implements \Aimeos\MShop\Service\Provider\Delivery\Iface
{
	private $beConfig = [
		'email.from' => [
			'code' => 'email.from',
			'internalcode' => 'email.from',
			'label' => 'Sender e-mail address',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		],
		'email.to' => [
			'code' => 'email.to',
			'internalcode' => 'email.to',
			'label' => 'Recipient e-mail address',
			'type' => 'string',
			'internaltype' => '',
			'default' => '',
			'required' => true,
		],
		'email.subject' => [
			'code' => 'email.subject',
			'internalcode' => 'email.subject',
			'label' => 'E-mail subject',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		],
		'email.template' => [
			'code' => 'email.template',
			'internalcode' => 'email.template',
			'label' => 'E-mail template',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => 'service/provider/delivery/email-body-standard',
			'required' => true,
		],
		'email.order-template' => [
			'code' => 'email.order-template',
			'internalcode' => 'email.order-template',
			'label' => 'Order template',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => 'service/provider/delivery/email-order-standard',
			'required' => true,
		],
	];


	/**
	 * Checks the backend configuration attributes for validity
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Sends the email and updates the delivery status
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order instance
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		$baseItem = $this->getOrderBase( $order->getBaseId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
		$this->send( [$order], [$baseItem->getId() => $baseItem] );

		return $order->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}


	/**
	 * Sends the email with several orders and updates the delivery status
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\MShop\Order\Item\Iface[] Updated order items
	 */
	public function processBatch( iterable $orders ) : \Aimeos\Map
	{
		$this->send( $orders, $this->getOrderBaseItems( $orders ) );
		return map( $orders )->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}


	/**
	 * Returns the content for the e-mail body
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @param \Aimeos\MShop\Order\Item\Base\Iface[] $baseItems Associative list of order base items to export
	 */
	protected function getEmailContent( iterable $orderItems, iterable $baseItems )
	{
		$template = $this->getConfigValue( 'email.template', 'service/provider/delivery/email-body-standard' );

		return $this->getContext()->getView()
			->assign( ['orderItems' => $orderItems, 'baseItems' => $baseItems] )
			->render( $template );
	}


	/**
	 * Returns the order content for the e-mail attachment
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @param \Aimeos\MShop\Order\Item\Base\Iface[] $baseItems Associative list of order base items to export
	 */
	protected function getOrderContent( iterable $orderItems, iterable $baseItems )
	{
		$template = $this->getConfigValue( 'email.order-template', 'service/provider/delivery/email-order-standard' );

		return $this->getContext()->getView()
			->assign( ['orderItems' => $orderItems, 'baseItems' => $baseItems] )
			->render( $template );
	}


	/**
	 * Returns the order base items for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Base\Iface with IDs as keys
	 */
	protected function getOrderBaseItems( iterable $orderItems ) : \Aimeos\Map
	{
		$ids = map( $orderItems )->getBaseId();
		$ref = ['order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'];

		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/base' );
		$search = $manager->filter()->slice( 0, $ids->count() );
		$search->setConditions( $search->compare( '==', 'order.base.id', $ids->toArray() ) );

		return $manager->search( $search, $ref );
	}


	/**
	 * Sends an e-mail for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @param \Aimeos\MShop\Order\Item\Base\Iface[] $baseItems Associative list of order base items to export
	 */
	protected function send( iterable $orderItems, iterable $baseItems )
	{
		$this->getContext()->mail()->createMessage()
			->addTo( (string) $this->getConfigValue( 'email.to' ) )
			->addFrom( (string) $this->getConfigValue( 'email.from' ) )
			->setSubject( (string) $this->getConfigValue( 'email.subject', 'New orders' ) )
			->addAttachment( $this->getOrderContent( $orderItems, $baseItems ), 'text/plain', 'orders.csv' )
			->setBody( $this->getEmailContent( $orderItems, $baseItems ) )
			->send();
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
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
	private array $beConfig = [
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
			'default' => 'service/provider/delivery/email-body',
			'required' => true,
		],
		'email.order-template' => [
			'code' => 'email.order-template',
			'internalcode' => 'email.order-template',
			'label' => 'Order template',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => 'service/provider/delivery/email-order',
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Sends the email with several orders and updates the delivery status
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\MShop\Order\Item\Iface[] Updated order items
	 */
	public function push( iterable $orders ) : \Aimeos\Map
	{
		return $this->send( $orders )->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}


	/**
	 * Returns the content for the e-mail body
	 *
	 * @param iterable $orderItems List of order items to export
	 */
	protected function getEmailContent( iterable $orderItems )
	{
		$template = $this->getConfigValue( 'email.template', 'service/provider/delivery/email-body' );

		return $this->context()->view()
			->assign( ['orderItems' => $orderItems] )
			->render( $template );
	}


	/**
	 * Returns the order content for the e-mail attachment
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 */
	protected function getOrderContent( iterable $orderItems )
	{
		$template = $this->getConfigValue( 'email.order-template', 'service/provider/delivery/email-order' );

		return $this->context()->view()
			->assign( ['orderItems' => $orderItems] )
			->render( $template );
	}


	/**
	 * Sends an e-mail for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @return \Aimeos\Map List of order items
	 */
	protected function send( iterable $orderItems ) : \Aimeos\Map
	{
		$this->context()->mail()->create()
			->to( (string) $this->getConfigValue( 'email.to' ) )
			->from( (string) $this->getConfigValue( 'email.from' ) )
			->subject( (string) $this->getConfigValue( 'email.subject', 'New orders' ) )
			->attach( $this->getOrderContent( $orderItems ), 'orders.csv', 'text/plain' )
			->text( $this->getEmailContent( $orderItems ) )
			->send();

		return map( $orderItems );
	}
}

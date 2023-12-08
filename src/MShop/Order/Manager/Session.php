<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager;


/**
 * Session trait for order managers
 *
 * @package MShop
 * @subpackage Order
 */
trait Session
{
	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function object() : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Returns the context item object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context item object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Iface Shopping basket
	 */
	public function getSession( string $type = 'default' ) : \Aimeos\MShop\Order\Item\Iface
	{
		$context = $this->context();
		$token = $context->token();
		$locale = $context->locale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();

		$key = $token . '-' . $sitecode . '-' . $language . '-' . $currency . '-' . $type;

		try
		{
			if( ( $order = \Aimeos\MShop::create( $context, 'order/basket' )->get( $key )->getItem() ) === null ) {
				return $this->object()->create();
			}

			\Aimeos\MShop::create( $context, 'plugin' )->register( $order, 'order' );
		}
		catch( \Exception $e )
		{
			return $this->object()->create();
		}

		return $order;
	}


	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Iface $order, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$context = $this->context();
		$token = $context->token();
		$locale = $context->locale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();

		$key = $token . '-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$session = $context->session();

		$list = $session->get( 'aimeos/basket/list', [] );
		$list[$key] = $key;

		$session->set( 'aimeos/basket/list', $list );

		$manager = \Aimeos\MShop::create( $context, 'order/basket' );
		$manager->save( $manager->create()->setId( $key )->setCustomerId( $context->user() )->setItem( clone $order ) );

		return $this;
	}
}

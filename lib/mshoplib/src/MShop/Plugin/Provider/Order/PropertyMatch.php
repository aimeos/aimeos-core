<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the value of a property defined in the configuration
 *
 * @package MShop
 * @subpackage Plugin
 */
class PropertyMatch
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this, 'addProduct.before' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		$class = '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface';
		if( !( $order instanceof $class ) ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$class = '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface';
		if( !( $value instanceof $class ) ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$config = $this->getItemBase()->getConfig();

		if( $config === array() ) {
			return true;
		}

		$productManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );

		$criteria = $productManager->createSearch( true );

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.id', $value->getProductId() );
		$expr[] = $criteria->getConditions();

		foreach( $config as $property => $value ) {
			$expr[] = $criteria->compare( '==', $property, $value );
		}

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$result = $productManager->searchItems( $criteria );

		if( reset( $result ) === false )
		{
			$code = array( 'product' => array_keys( $config ) );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( 'Product matching given properties not found' ), -1, null, $code );
		}

		return true;
	}
}

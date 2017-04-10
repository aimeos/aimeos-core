<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds attributes to a product in an order
 *
 * @package MShop
 * @subpackage Plugin
 */
class PropertyAdd
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $orderAttrManager;
	private $type;


	/**
	 * Initializes the plugin instance
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Plugin\Item\Iface $item )
	{
		parent::__construct( $context, $item );

		$this->orderAttrManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product/attribute' );
		$this->type = $context->getConfig()->get( 'plugin/provider/order/propertyadd/type', 'property' );
	}


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
	 * @throws \Aimeos\MShop\Plugin\Exception in case of faulty configuration or parameters
	 * @return bool true if attributes have been added successfully
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}

		if( !( $value instanceof \Aimeos\MShop\Order\Item\Base\Product\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Product\Iface' ) );
		}

		$productManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );

		$config = $this->getItemBase()->getConfig();

		foreach( $config as $key => $properties )
		{
			$keyElements = explode( '.', $key );

			if( $keyElements[0] !== 'product' || count( $keyElements ) < 3 )
			{
				$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Configuration invalid' );
				throw new \Aimeos\MShop\Plugin\Exception( $msg );
			}

			$productSubManager = $productManager->getSubManager( $keyElements[1] );

			$search = $productSubManager->createSearch( true );

			$cond = [];
			$cond[] = $search->compare( '==', $key, $value->getProductId() );
			$cond[] = $search->getConditions();

			$search->setConditions( $search->combine( '&&', $cond ) );

			$result = $productSubManager->searchItems( $search );

			foreach( $result as $item )
			{
				$attributes = $this->addAttributes( $item, $value, $properties );
				$value->setAttributes( $attributes );
			}
		}

		return true;
	}


	/**
	 * Adds attribute items to an array.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item containing the properties to be added as attributes
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $product Product containing attributes
	 * @param Array $properties List of item properties to be converted
	 * @return Array List of attributes
	 */
	protected function addAttributes( \Aimeos\MShop\Common\Item\Iface $item, \Aimeos\MShop\Order\Item\Base\Product\Iface $product, array $properties )
	{
		$attributeList = $product->getAttributes();
		$itemProperties = $item->toArray( true );

		foreach( $properties as $code )
		{
			if( array_key_exists( $code, $itemProperties )
				&& $product->getAttribute( $code, $this->type ) === null
			) {
				$new = $this->orderAttrManager->createItem();
				$new->setCode( $code );
				$new->setType( $this->type );
				$new->setValue( $itemProperties[$code] );

				$attributeList[] = $new;
			}
		}

		return $attributeList;
	}
}

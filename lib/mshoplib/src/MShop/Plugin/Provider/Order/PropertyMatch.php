<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the value of a property defined in the configuration
 *
 * Products can be only added to the basket if they contain the required
 * product properties.
 *
 * Example:
 * - values: {"mytype": "myvalue"}
 *
 * This configuration enforces products to have a size and color property.
 * Otherwise, they can't be added to the basket by the customers.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class PropertyMatch
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'values' => array(
			'code' => 'values',
			'internalcode' => 'values',
			'label' => 'Property type/value map',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => true,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
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
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$plugin = $this->getObject();

		$p->attach( $plugin, 'addProduct.before' );
		$p->attach( $plugin, 'setProducts.before' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		if( ( $map = (array) $this->getItemBase()->getConfigValue( 'values', [] ) ) === [] ) {
			return $value;
		}

		$list = [];

		if( is_array( $value ) )
		{
			foreach( $value as $orderProduct )
			{
				\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $orderProduct );
				$list[] = $orderProduct->getProductId();
			}
		}
		else
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $value );
			$list[] = $value->getProductId();
		}

		if( $this->getProductItems( array_unique( $list ), $map )->count() !== count( $list ) )
		{
			$code = array( 'product' => $map );
			$msg = $this->getContext()->translate( 'mshop', 'Product matching given properties not found' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return $value;
	}


	/**
	 * Returns the product items for the given product IDs limited by the map of properties
	 *
	 * @param string[] $productIds List of product IDs
	 * @param array $map Assoicative list of property types as keys and property values
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Product\Item\Iface with IDs as keys
	 */
	protected function getProductItems( array $productIds, array $map ) : \Aimeos\Map
	{
		$context = $this->getContext();
		$langId = $context->getLocale()->getLanguageId();

		$manager = \Aimeos\MShop::create( $context, 'product' );
		$search = $manager->filter( true );
		$expr = [
			$search->compare( '==', 'product.id', $productIds ),
			$search->getConditions(),
		];

		foreach( $map as $type => $value )
		{
			$func = $search->make( 'product:prop', [$type, [$langId, null], (string) $value] );
			$expr[] = $search->compare( '!=', $func, null );
		}

		$search->setConditions( $search->and( $expr ) );

		return $manager->search( $search );
	}
}

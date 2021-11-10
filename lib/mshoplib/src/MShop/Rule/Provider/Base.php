<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider;


/**
 * Abstract class for rule provider and decorator implementations
 *
 * @package MShop
 * @subpackage Rule
 */
abstract class Base
	implements \Aimeos\MW\Macro\Iface
{
	use \Aimeos\MW\Macro\Traits;

	private $item;
	private $context;
	private $object;
	private $beConfig = [
		'last-rule' => [
			'code' => 'last-rule',
			'internalcode' => 'last-rule',
			'label' => 'Don\'t execute subsequent rules',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => 0,
			'required' => false,
		],
	];


	/**
	 * Initializes the rule instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Rule\Item\Iface $item )
	{
		$this->item = $item;
		$this->context = $context;
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->checkConfig( $this->beConfig, $attributes );
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
	 * Injects the outer object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Rule\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Rule\Provider\Iface Rule object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Rule\Provider\Iface $object ) : \Aimeos\MShop\Rule\Provider\Iface
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Checks required fields and the types of the given data map
	 *
	 * @param array $criteria Multi-dimensional associative list of criteria configuration
	 * @param array $map Values to check agains the criteria
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function checkConfig( array $criteria, array $map ) : array
	{
		$helper = new \Aimeos\MShop\Common\Helper\Config\Standard( $this->getConfigItems( $criteria ) );
		return $helper->check( $map );
	}


	/**
	 * Returns the criteria attribute items for the backend configuration
	 *
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of criteria attribute items
	 */
	protected function getConfigItems( array $configList ) : array
	{
		$list = [];

		foreach( $configList as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
		}

		return $list;
	}


	/**
	 * Returns the first object of the decorator stack
	 *
	 * @return \Aimeos\MShop\Rule\Provider\Iface First object of the decorator stack
	 */
	protected function getObject() : \Aimeos\MShop\Rule\Provider\Iface
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the rule item the provider is configured with.
	 *
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item object
	 */
	protected function getItemBase() : \Aimeos\MShop\Rule\Item\Iface
	{
		return $this->item;
	}


	/**
	 * Returns the configuration value from the service item specified by its key.
	 *
	 * @param string $key Configuration key
	 * @param mixed $default Default value if configuration key isn't available
	 * @return mixed Value from service item configuration
	 */
	protected function getConfigValue( string $key, $default = null )
	{
		$config = $this->item->getConfig();

		if( isset( $config[$key] ) ) {
			return $config[$key];
		}

		return $default;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Tests if this rule should be the last one that is applied
	 *
	 * @return bool True, if rule should be the last one, false to continue with further rules
	 */
	protected function isLast() : bool
	{
		return (bool) $this->getConfigValue( 'last-rule', false );
	}
}

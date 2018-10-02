<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider;


/**
 * Abstract class for plugin provider and decorator implementations
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class Base
{
	private $item;
	private $context;
	private $object;


	/**
	 * Initializes the plugin instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Plugin\Item\Iface $item )
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
	public function checkConfigBE( array $attributes )
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return [];
	}


	/**
	 * Injects the outer object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Plugin\Provider\Iface $object )
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Checks required fields and the types of the config array.
	 *
	 * @param array $config Config parameters
	 * @param array $attributes Attributes for the config array
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function checkConfig( array $config, array $attributes )
	{
		$errors = [];

		foreach( $config as $key => $def )
		{
			if( $def['required'] === true && ( !isset( $attributes[$key] ) || $attributes[$key] === '' ) )
			{
				$errors[$key] = sprintf( 'Configuration for "%1$s" is missing', $key );
				continue;
			}

			if( isset( $attributes[$key] ) && $attributes[$key] != '' )
			{
				switch( $def['type'] )
				{
					case 'boolean':
						if( !is_string( $attributes[$key] ) || $attributes[$key] != '0' && $attributes[$key] != '1' ) {
							$errors[$key] = sprintf( 'Not a true/false value' ); continue 2;
						}
						break;
					case 'string':
						if( is_string( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a string' ); continue 2;
						}
						break;
					case 'integer':
						if( is_integer( $attributes[$key] ) === false && ctype_digit( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not an integer number' ); continue 2;
						}
						break;
					case 'number':
						if( is_numeric( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a number' ); continue 2;
						}
						break;
					case 'date':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date' ); continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date and time' ); continue 2;
						}
						break;
					case 'time':
						$pattern = '/^([0-2])?[0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a time' ); continue 2;
						}
						break;
					case 'list':
					case 'select':
						if( !is_array( $def['default'] ) || !isset( $def['default'][$attributes[$key]] )
							&& !in_array( $attributes[$key], $def['default'] )
						) {
							$errors[$key] = sprintf( 'Not a listed value' ); continue 2;
						}
						break;
					case 'map':
						if( !is_array( $attributes[$key] ) ) {
							$errors[$key] = sprintf( 'Not a key/value map' ); continue 2;
						}
						break;
					default:
						throw new \Aimeos\MShop\Service\Exception( sprintf( 'Invalid type "%1$s"', $def['type'] ) );
				}
			}

			$errors[$key] = null;
		}

		return $errors;
	}


	/**
	 * Returns the criteria attribute items for the backend configuration
	 *
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of criteria attribute items
	 */
	protected function getConfigItems( array $configList )
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
	 * @return \Aimeos\MShop\Plugin\Provider\Iface First object of the decorator stack
	 */
	protected function getObject()
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the plugin item the provider is configured with.
	 *
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item object
	 */
	protected function getItemBase()
	{
		return $this->item;
	}


	/**
	 * Returns the configuration value from the service item specified by its key.
	 *
	 * @param string $key Configuration key
	 * @param mixed $default Default value if configuration key isn't available
	 * @return string|null Value from service item configuration
	 */
	protected function getConfigValue( $key, $default = null )
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
	protected function getContext()
	{
		return $this->context;
	}
}
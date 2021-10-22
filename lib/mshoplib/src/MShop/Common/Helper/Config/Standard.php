<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Helper\Config;


/**
 * Default implementation of the config helper item
 *
 * @package MShop
 * @subpackage Common
 */
class Standard implements \Aimeos\MShop\Common\Helper\Config\Iface
{
	private $criteria;


	/**
	 * Initializes the object with the criteria objects to check against
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface $criteria Criteria attribute objects
	 */
	public function __construct( array $criteria )
	{
		$this->criteria = $criteria;
	}


	/**
	 * Checks required fields and the types of the config array
	 *
	 * @param array $map Values to check agains the criteria
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function check( array $config ) : array
	{
		$errors = [];

		foreach( $this->criteria as $key => $attr )
		{
			if( $attr->isRequired() === true && ( !isset( $config[$key] ) || $config[$key] === '' ) )
			{
				$errors[$key] = sprintf( 'Configuration for "%1$s" is missing', $key );
				continue;
			}

			if( isset( $config[$key] ) )
			{
				switch( $attr->getType() )
				{
					case 'boolean':
						if( $config[$key] != null && !is_scalar( $config[$key] ) || $config[$key] != '0' && $config[$key] != '1' ) {
							$errors[$key] = sprintf( 'Not a true/false value' ); continue 2;
						}
						break;
					case 'string':
					case 'text':
						if( !is_string( $config[$key] ) && !is_numeric( $config[$key] ) ) {
							$errors[$key] = sprintf( 'Not a string' ); continue 2;
						}
						break;
					case 'integer':
						if( !is_integer( $config[$key] ) && !ctype_digit( $config[$key] ) ) {
							$errors[$key] = sprintf( 'Not an integer number' ); continue 2;
						}
						break;
					case 'number':
						if( !is_numeric( $config[$key] ) ) {
							$errors[$key] = sprintf( 'Not a number' ); continue 2;
						}
						break;
					case 'date':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';
						if( !is_string( $config[$key] ) || preg_match( $pattern, $config[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date' ); continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $config[$key] ) || preg_match( $pattern, $config[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date and time' ); continue 2;
						}
						break;
					case 'time':
						$pattern = '/^([0-2])?[0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $config[$key] ) || preg_match( $pattern, $config[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a time' ); continue 2;
						}
						break;
					case 'list':
					case 'select':
						$default = (array) $attr->getDefault();
						if( !empty( $default ) && !isset( $default[$config[$key]] ) && !in_array( $config[$key], $default ) ) {
							$errors[$key] = sprintf( 'Not a listed value' ); continue 2;
						}
						break;
					case 'map':
						if( !is_array( $config[$key] ) ) {
							$errors[$key] = sprintf( 'Not a key/value map' ); continue 2;
						}
						break;
					default:
						throw new \Aimeos\MShop\Exception( sprintf( 'Invalid type "%1$s"', $attr->getType() ) );
				}
			}

			$errors[$key] = null;
		}

		return $errors;
	}
}

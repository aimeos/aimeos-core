<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Url;


/**
 * Base view helper class for building URLs
 *
 * @package MW
 * @subpackage View
 */
abstract class Base extends \Aimeos\MW\View\Helper\Base
{
	/**
	 * Replaces dangerous characteris in the parameters
	 *
	 * @param array $params Associative list of key/value pairs
	 * @param array $names Replace characters in the parameters of the given names, empty for all
	 * @return array Associative list with encoded values
	 */
	protected function sanitize( array $params, $names = array( 'f_name', 'd_name' ) )
	{
		$regex = '/(\s|\&|\%|\?|\#|\=|\{|\}|\||\\\\|\~|\[|\]|\`|\^|\_|\/)+/';

		foreach( $params as $key => $value )
		{
			if( is_array( $value ) ) {
				$params[$key] = $this->sanitize( $value, $names );
			} elseif( empty( $names ) || in_array( (string) $key, $names ) ) {
				$params[$key] = trim( preg_replace( $regex, '_', $value ), '_' );
			}
		}

		return $params;
	}
}
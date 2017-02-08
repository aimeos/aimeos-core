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
	 * @param boolean $all Replace characters in all parameters or in names only
	 * @return array Associative list with encoded values
	 */
	protected function sanitize( array $params, $all = false )
	{
		$regex = '/(\s|\&|\%|\?|\#|\=|\{|\}|\||\\\\|\~|\[|\]|\`|\^|\_|\/)+/';

		foreach( $params as $key => $value )
		{
			if( is_array( $value ) ) {
				$params[$key] = $this->sanitize( $value );
			} elseif( $all || in_array( (string) $key, array( 'f_name', 'd_name' ) ) ) {
				$params[$key] = trim( preg_replace( $regex, '_', $value ), '_' );
			}
		}

		return $params;
	}
}
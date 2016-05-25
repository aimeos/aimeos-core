<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria;


/**
 * PostgreSQL search criteria class.
 *
 * @package MW
 * @subpackage Common
 */
class PgSQL extends \Aimeos\MW\Criteria\SQL
{
	/**
	 * Creates a function signature for expressions.
	 *
	 * @param string $name Function name
	 * @param string[] $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 */
	public function createFunction( $name, array $params )
	{
		switch( $name )
		{
			case 'index.text.relevance':
			case 'sort:index.text.relevance':

				if( isset( $params[2] ) )
				{
					$regex = '/(\s|\&|\%|\?|\#|\=|\{|\}|\||\\\\|\~|\[|\]|\`|\^|\/|\-|\+|\>|\<|\(|\)|\*|\:|\"|\!|\§|\$|\'|\;|\.|\,|\@)+/';
					$search = trim( preg_replace( $regex, ' ', $params[2] ) );

					$params[2] = implode( ':* & ', explode( ' ', $search ) ) . ':*';
				}
				break;
		}

		return \Aimeos\MW\Criteria\Expression\Base::createFunction( $name, $params );
	}
}

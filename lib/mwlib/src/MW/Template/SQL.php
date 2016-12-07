<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Template
 */


namespace Aimeos\MW\Template;


/**
 * Statement text processing.
 *
 * @package MW
 * @subpackage Template
 */
class SQL extends \Aimeos\MW\Template\Base
{
	/**
	 * Builds the statement object with string and markers.
	 *
	 * @param string $text Statement as text
	 * @param string $begin Marker for start sequence with '*' as wildcard
	 * @param string $end Marker for stop sequence with '*' as wildcard
	 */
	public function __construct( $text, $begin = '/*-$*/', $end = '/*$-*/' )
	{
		parent::__construct( $text, $begin, $end );
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
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
	 * @return \Aimeos\MW\Template\Iface
	 */
	public function __construct( $text, $begin = '/*-$*/', $end = '/*$-*/' )
	{
		parent::__construct( $text, $begin, $end );
	}
}

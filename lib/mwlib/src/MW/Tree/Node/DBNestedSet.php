<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Tree
 */


namespace Aimeos\MW\Tree\Node;


/**
 * Nested set implementation of a tree node
 *
 * @package MW
 * @subpackage Tree
 * @property integer $left Left number of the nested set item
 * @property integer $right Right number of the nested set item
 */
class DBNestedSet extends Standard
{
	/**
	 * Tests if a node has children.
	 *
	 * @return bool True if node has children, false if not
	 */
	public function hasChildren() : bool
	{
		if( isset( $this->right ) && isset( $this->left ) && $this->right > $this->left + 1 ) {
			return true;
		}

		return false;
	}
}

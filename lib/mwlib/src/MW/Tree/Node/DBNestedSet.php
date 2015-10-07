<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 */


namespace Aimeos\MW\Tree\Node;


/**
 * Nested set implementation of a tree node
 *
 * @package MW
 * @subpackage Tree
 */
class DBNestedSet extends \Aimeos\MW\Tree\Node\Standard
{
	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren()
	{
		if( $this->right > $this->left + 1 ) {
			return true;
		}

		return false;
	}
}

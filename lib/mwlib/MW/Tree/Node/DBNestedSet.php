<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 * @version $Id: DBNestedSet.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Nested set implementation of a tree node
 *
 * @package MW
 * @subpackage Tree
 */
class MW_Tree_Node_DBNestedSet extends MW_Tree_Node_Default
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

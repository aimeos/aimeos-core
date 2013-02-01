<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 * @version $Id: NavTree.php 1338 2012-10-25 17:03:37Z nsendetzky $
 */


/**
 * View helper class for generating the navigation tree.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_NavTree_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	/**
	 * Returns the HTML for the navigation tree.
	 *
	 * @param MShop_Catalog_Item_Interface $item Catalog item with child nodes
	 * @return string Rendered HTML of the navigation tree
	 */
	public function transform( MShop_Catalog_Item_Interface $item )
	{
		if( $item->getStatus() <= 0 ) {
			return '';
		}

		$id = $item->getId();
		$trailing = array( $item->getname() );
		$output = '<li class="tree-catid-' . $id . ( $item->hasChildren() ? ' catWithChild' : ' catNoChild' ) . '"><a href="' . $this->url( $this->config( 'catalog-list-target' ), 'catalog', 'list', array( 'f-catalog-id' => $id ), $trailing ) . '">' . $item->getName() . '</a>';

		if( $item->hasChildren() )
		{
			$output .= '<ul class="tree-level-' . ( $item->getNode()->level + 1 ) . '">';

			foreach( $item->getChildren() as $child ) {
				$output .= $this->transform( $child );
			}

			$output .= '</ul>';
		}

		$output .= '</li>';

		return $output;
	}
}
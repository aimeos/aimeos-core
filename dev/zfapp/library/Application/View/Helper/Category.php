<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 *  Category helper
 */
class Application_View_Helper_Category extends Zend_View_Helper_Abstract
{
	/**
	 * Return categories.
	 *
	 * @param string $class Css class to identify active elements
	 * @return array()
	 */
	public function category( $class='categories' )
	{
		$data = array();

		// for the main navigation
		try
		{
			$front = Zend_Controller_Front::getInstance();
			$current = $front->getRequest()->getParam( 'f-catalog-id' );
			$catalogManager = Zend_Registry::get('MShop_Catalog_Manager');
			$node = $catalogManager->getTree(null, array('text'), MW_Tree_Manager_Abstract::LEVEL_LIST );

			if( $node->getStatus() <= 0 ) {
				return $data;
			}

			$label = $node->getName();
			$catId = $node->getId();

			$item = array(
				'uri' => $this->view->url( array( 'site' => $this->view->params['site'], 'trailing' => $label, 'f-catalog-id' => $catId ), 'routeDefault', true ),
				'pages' => $this->_createSubTree( $node, $current ),
				'class' => ( ( $catId === $current ) ? 'active' : '' ),
				'label' => $label,
			);

			$data = array_merge( array( $item ), $this->_createSubTree( $node, $current ) );
		}
		catch( Exception $e)
		{
			if (APPLICATION_ENV != 'development')
			{
				$msgtext = 'Missing categories or categories are in error: "%1$s" %2$s';
				$msg = sprintf($msgtext, $e->getMessage(), $e->getTraceAsString() );
				error_log( $msg );
			}
		}

		return $data;
	}


	/**
	 * Creates the nessassary data for the Zend_Navigation sub category tree.
	 *
	 * @param object $node Nodes MW_Tree
	 * @param integer $activkey
	 * @return array Nested list of navigation elements for Zend_Navigation.
	 */
	private function _createSubTree( $node, $current )
	{
		$data = array();

		foreach ( $node->getChildren() as $value )
		{
			if( $value->getStatus() <= 0 ) {
				continue;
			}

			$label = $value->getName();
			$catId = $value->getId();
			$active = ( $catId === $current );

			$item = array(
				'uri' => $this->view->url( array( 'site' => $this->view->params['site'], 'trailing' => $label, 'f-catalog-id' => $catId ), 'routeDefault', true ),
				'class' => ( ( $catId === $current ) ? 'active' : '' ),
				'label' => $label,
			);

			$data[] = $item;
		}

		return $data;
	}

}

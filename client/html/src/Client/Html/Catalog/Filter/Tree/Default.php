<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage HTML
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of catalog tree filter section in HTML client.
 *
 * @package Client
 * @subpackage HTML
 */
class Client_Html_Catalog_Filter_Tree_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/catalog/filter/tree/default/subparts';
	private $_subPartNames = array();
	private $_controller;
	private $_pathcache;


	/**
	 * Initializes the class instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths )
	{
		parent::__construct( $context, $templatePaths );

		$this->_controller = Controller_Frontend_Catalog_Factory::createController( $context );
	}


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$context = $this->_getContext();
		$cache = $context->getCache();
		$cachable = $cache->isAvailable();

		$view = $this->getView();
		$startid = $view->config( 'catalog/filter/tree/startid' );

		if( $cachable && ( $result = $cache->get( 'catalog-filter-tree:' . $startid ) ) !== null ) {
			return $result;
		}


		$catpath = $this->_getCatalogPath( $view->param( 'f-catalog-id' ) );
		$view->treeCatalogPath = $catpath;

		if( $cachable ) {
			$view->treeCatalogTree = $this->_getCatalogTree( $startid );
		} else {
			$view->treeCatalogTree = $this->_getCatalogTree( $startid, $catpath );
		}


		$navHelper = new MW_View_Helper_NavTree_Default( $view );
		$view->addHelper( 'navtree', $navHelper );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->treeBody = $html;


		$tplconf = 'client/html/catalog/filter/tree/default/template-body';
		$default = 'catalog/filter/tree-body-default.html';

		$output = $view->render( $this->_getTemplate( $tplconf, $default ) );

		if( $this->isCachable( Client_HTML_Abstract::CACHE_BODY ) ) {
			$cache->set( 'catalog-filter-tree:' . $startid, $output );
		}

		return $output;
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string|null $name Template name
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $name = null )
	{
		$view = $this->getView();
		$catid = $view->param( 'f-catalog-id' );

		$view->treeCatalogPath = $this->_getCatalogPath( $catid );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->treeHeader = $html;

		$tplconf = 'client/html/catalog/filter/tree/default/template-header';
		$default = 'catalog/filter/tree-header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'catalog/filter/tree/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		if( $what === Client_Html_Abstract::CACHE_HEADER ) {
			return false;
		}

		return $this->_isCachable( $what, $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Returns a list of catalog items that are in the path from the given ID to the catalog root.
	 *
	 * @param string $catid Unique category ID
	 * @return array List of catalog items along the path to the catalog root
	 */
	protected function _getCatalogPath( $catid )
	{
		if( !isset( $this->_pathcache ) ) {
			$this->_pathcache = $this->_controller->getCatalogPath( $catid );
		}

		return $this->_pathcache;
	}


	protected function _getCatalogTree( $startid, array $catpath = array() )
	{
		if( count( $catpath ) === 0 ) {
			return $this->_controller->getCatalogTree( $startid );
		}

		foreach( $catpath as $id => $item )
		{
			if( $id === $startid || $startid === null )
			{
				unset( $catpath[$id] );
				$startid = $id;
				break;
			}
			unset( $catpath[$id] );
		}

		$root = $node = $this->_controller->getCatalogTree( $startid, array( 'text', 'media' ), MW_Tree_Manager_Abstract::LEVEL_LIST );

		foreach( $catpath as $id => $item )
		{
			$subnode = $this->_controller->getCatalogTree( $id, array( 'text', 'media' ), MW_Tree_Manager_Abstract::LEVEL_LIST );
			$childid = $subnode->getId();

			foreach( $node->getChildren() as $child )
			{
				if( $child->getId() == $childid )
				{
					foreach( $subnode->getChildren() as $subchild ) {
						$child->addChild( $subchild );
					}

					break;
				}
			}

			$node = $subnode;
		}

		return $root;
	}
}

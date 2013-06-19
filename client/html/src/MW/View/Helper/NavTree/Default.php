<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
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
	private $_target;
	private $_controller;
	private $_action;
	private $_encoder;


	/**
	 * Initializes the view helper classes.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 */
	public function __construct( MW_View_Interface $view )
	{
		parent::__construct( $view );

		$this->_target = $view->config( 'client/html/catalog/list/url/target' );
		$this->_controller = $view->config( 'client/html/catalog/list/url/controller', 'catalog' );
		$this->_action = $view->config( 'client/html/catalog/list/url/action', 'list' );

		$this->_encoder = $view->encoder();
	}


	/**
	 * Returns the HTML for the navigation tree.
	 *
	 * @param MShop_Catalog_Item_Interface $item Catalog item with child nodes
	 * @param array Associative list of catalog IDs as keys and catalog nodes as values
	 * @return string Rendered HTML of the navigation tree
	 */
	public function transform( MShop_Catalog_Item_Interface $item, array $path )
	{
		if( $item->getStatus() <= 0 ) {
			return '';
		}

		$id = $item->getId();
		$enc = $this->_encoder;
		$config = $item->getConfig();

		$class = ( $item->hasChildren() ? ' withchild' : ' nochild' );
		$class .= ( isset( $path[ $item->getId() ] ) ? ' active' : '' );
		$class .= ( isset( $config['css-class'] ) ? ' ' . $config['css-class'] : '' );

		$params = array( 'a-name' => str_replace( ' ', '-', $item->getName() ), 'f-catalog-id' => $id );
		$url = $enc->attr( $this->url( $this->_target, $this->_controller, $this->_action, $params ) );

		$output = '<li class="catid-' . $enc->attr( $id . $class ) . '"><a href="' . $url . '">' . $enc->html( $item->getName() ) . '</a>';

		if( $item->hasChildren() )
		{
			$output .= '<ul class="level-' . $enc->attr( $item->getNode()->level + 1 ) . '">';

			foreach( $item->getChildren() as $child ) {
				$output .= $this->transform( $child, $path );
			}

			$output .= '</ul>';
		}

		$output .= '</li>';

		return $output;
	}
}
<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog count tree section HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Count_Tree_Default
	extends Client_Html_Catalog_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/catalog/count/tree/default/subparts';
	private $_subPartNames = array();
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->treeBody = $html;

		$tplconf = 'client/html/catalog/count/tree/default/template-body';
		$default = 'catalog/count/tree-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->treeHeader = $html;

		$tplconf = 'client/html/catalog/count/tree/default/template-header';
		$default = 'catalog/count/tree-header-default.html';

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
		return $this->_createSubClient( 'catalog/count/tree/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$context = $this->_getContext();
			$config = $context->getConfig();

			if( $config->get( 'client/html/catalog/count/tree/aggregate', true ) == true )
			{
				$filter = $this->_getProductListFilter( $view, false );
				$filter->setSlice( 0, $config->get( 'client/html/catalog/count/limit', 10000 ) );
				$filter->setSortations( array() ); // it's not necessary and slows down the query

				$controller = Controller_Frontend_Factory::createController( $context, 'catalog' );
				$view->treeCountList = $controller->aggregate( $filter, 'catalog.index.catalog.id' );
			}

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}

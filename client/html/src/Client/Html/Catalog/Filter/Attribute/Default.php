<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog attribute filter section in HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Filter_Attribute_Default
	extends Client_Html_Abstract
{
	private $_subPartPath = 'client/html/catalog/filter/attribute/default/subparts';
	private $_subPartNames = array();
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string|null $name Template name
	 * @return string HTML code
	 */
	public function getBody( $name = null )
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->attributeBody = $html;

		$tplconf = 'client/html/catalog/filter/attribute/default/template-body';
		$default = 'catalog/filter/attribute-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string|null $name Template name
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $name = null )
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->attributeHeader = $html;

		$tplconf = 'client/html/catalog/filter/attribute/default/template-header';
		$default = 'catalog/filter/attribute-header-default.html';

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
		return $this->_createSubClient( 'catalog/filter/attribute/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return $this->_isCachable( $what, $this->_subPartPath, $this->_subPartNames );
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
			$attrMap = array();
			$context = $this->_getContext();
			$config = $context->getConfig();

			$manager = MShop_Factory::createManager( $context, 'attribute' );

			$search = $manager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'attribute.domain', 'product' ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', 'attribute.position' ) ) );
			$search->setSlice( 0, 1000 );

			foreach( $manager->searchItems( $search, array( 'text' ) ) as $id => $item ) {
				$attrMap[ $item->getType() ][$id] = $item;
			}


			$text = (string) $view->param( 'f-search-text' );
			$catid = (string) $view->param( 'f-catalog-id' );
			$attrids = $view->param( 'f-attr-id', array() );

			if( is_string( $attrids ) ) {
				$attrids = explode( ' ', $attrids );
			}

			if( $catid == '' ) {
				$catid = $config->get( 'client/html/catalog/list/catid-default', '' );
			}

			$controller = Controller_Frontend_Catalog_Factory::createController( $context );

			if( $text !== '' ) {
				$filter = $controller->createProductFilterByText( $text );
			} else if( $catid !== '' ) {
				$filter = $controller->createProductFilterByCategory( $catid );
			} else {
				$filter = $controller->createProductFilterDefault();
			}

			if( !empty( $attrids ) )
			{
				$func = $filter->createFunction( 'catalog.index.attributeaggregate', array( $attrids ) );

				$expr = array(
					$filter->getConditions(),
					$filter->compare( '==', $func, count( $attrids ) ),
				);

				$filter->setConditions( $filter->combine( '&&', $expr ) );
			}

			if( $config->get( 'client/html/catalog/filter/attribute/aggregate', true ) === true ) {
				$view->attributeAggregate = $controller->aggregate( $filter, 'catalog.index.attribute.id' );
			}

			$view->attributeMap = $attrMap;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
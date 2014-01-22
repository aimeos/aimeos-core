<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout detail summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Common_Summary_Detail_Default
	extends Client_Html_Abstract
{
	private $_cache;
	private $_subPartPath = 'client/html/common/summary/detail/default/subparts';
	private $_subPartNames = array();


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
		$view->detailBody = $html;

		$tplconf = 'client/html/common/summary/detail/default/template-body';
		$default = 'common/summary/detail-body-default.html';

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
		$view->detailHeader = $html;

		$tplconf = 'client/html/common/summary/detail/default/template-header';
		$default = 'common/summary/detail-header-default.html';

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
		return $this->_createSubClient( 'common/summary/detail/' . $type, $name );
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
	 * Returns a list of tax rates and values for the given basket.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket containing the products, services, etc.
	 * @return array Associative list of tax rates as key and corresponding amounts as value
	 */
	protected function _getTaxRates( MShop_Order_Item_Base_Interface $basket )
	{
		$taxrates = array();

		foreach( $basket->getProducts() as $product )
		{
			$price = $product->getPrice();

			if( isset( $taxrates[ $price->getTaxrate() ] ) ) {
				$taxrates[ $price->getTaxrate() ] += ( $price->getValue() + $price->getCosts() ) * $product->getQuantity();
			} else {
				$taxrates[ $price->getTaxrate() ] = ( $price->getValue() + $price->getCosts() ) * $product->getQuantity();
			}
		}

		try
		{
			$price = $basket->getService( 'delivery' )->getPrice();

			if( isset( $taxrates[ $price->getTaxrate() ] ) ) {
				$taxrates[ $price->getTaxrate() ] += $price->getValue() + $price->getCosts();
			} else {
				$taxrates[ $price->getTaxrate() ] = $price->getValue() + $price->getCosts();
			}
		}
		catch( Exception $e ) { ; }

		try
		{
			$price = $basket->getService( 'payment' )->getPrice();

			if( isset( $taxrates[ $price->getTaxrate() ] ) ) {
				$taxrates[ $price->getTaxrate() ] += $price->getValue() + $price->getCosts();
			} else {
				$taxrates[ $price->getTaxrate() ] = $price->getValue() + $price->getCosts();
			}
		}
		catch( Exception $e ) { ; }

		return $taxrates;
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		return $view;
	}
}
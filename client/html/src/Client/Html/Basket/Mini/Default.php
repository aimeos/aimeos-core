<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of mini basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Basket_Mini_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/basket/mini/default/subparts';
	private $_subPartNames = array( 'main' );

	
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

		$view->miniBody = $html;

		$tplconf = 'client/html/basket/mini/default/template-body';
		$default = 'basket/mini/body-default.html';

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
		$view->standardHeader = $html;

		$tplconf = 'client/html/basket/standard/default/template-header';
		$default = 'basket/standard/header-default.html';

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
		return $this->_createSubClient( 'basket/mini/' . $type, $name );
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
	 * Sets the necessary parameter values in the view.
	 */
	public function process()
	{
		$view = $this->getView();

		$controller = Controller_Frontend_Basket_Factory::createController( $this->_getContext() );

		$view->miniBasket = $controller->get();

		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
	
	
	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		try
		{
			$price = $view->miniBasket->getPrice();
			$count = 0;
			foreach( $view->miniBasket->getProducts() as $product ) {
				$count = $count + $product->getQuantity();
			}
			$view->quantity = $count;
			$view->priceValue = $price->getValue();
			$view->priceCurrency = $view->translate( 'core/client/html/currency', $price->getCurrencyId() );
		}
		catch( Exception $e )
		{
			$view->quantity = 0;
			$view->priceValue = '0.00';
			$view->priceCurrency = '';
		}
		
		$view->priceFormat = $view->translate( 'core/client/html', '%1$s%2$s' );
		
		return $view;
	}
}
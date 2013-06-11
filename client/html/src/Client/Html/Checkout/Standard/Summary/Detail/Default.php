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
class Client_Html_Checkout_Standard_Summary_Detail_Default
	extends Client_Html_Common_Summary_Detail_Default
	implements Client_Html_Interface
{
	private $_cache;


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		$view = parent::_setViewParams( $view );

		if( !isset( $this->_cache ) )
		{
			$basket = $view->standardBasket;

			$target = $view->config( 'client/html/basket/standard/url/target' );
			$cntl = $view->config( 'client/html/basket/standard/url/controller', 'basket' );
			$action = $view->config( 'client/html/basket/standard/url/action', 'index' );

			$view->summaryUrlDetailBasket = $view->url( $target, $cntl, $action );
			$view->summaryTaxRates = $this->_getTaxRates( $basket );
			$view->summaryBasket = $view->standardBasket;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
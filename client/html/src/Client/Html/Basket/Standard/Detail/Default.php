<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Basket_Standard_Detail_Default
	extends Client_Html_Common_Summary_Detail_Default
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

			$target = $view->config( 'client/html/checkout/standard/url/target' );
			$cntl = $view->config( 'client/html/checkout/standard/url/controller', 'checkout' );
			$action = $view->config( 'client/html/checkout/standard/url/action', 'index' );

			$view->summaryUrlServicePayment = $view->url( $target, $cntl, $action, array( 'c-step' => 'payment' ) );
			$view->summaryUrlServiceDelivery = $view->url( $target, $cntl, $action, array( 'c-step' => 'delivery' ) );
			$view->summaryTaxRates = $this->_getTaxRates( $basket );
			$view->summaryEnableModify = true;
			$view->summaryBasket = $basket;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
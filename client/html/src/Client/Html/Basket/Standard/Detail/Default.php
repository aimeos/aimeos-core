<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Basket_Standard_Detail_Default
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

			$view->summaryEnableModify = true;
			$view->summaryTaxRates = $this->_getTaxRates( $basket );
			$view->summaryBasket = $basket;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
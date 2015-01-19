<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout service summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Summary_Service_Default
	extends Client_Html_Common_Summary_Service_Default
{
	private $_cache;


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		$view = parent::_setViewParams( $view, $tags, $expire );

		if( !isset( $this->_cache ) )
		{
			$target = $view->config( 'client/html/checkout/standard/url/target' );
			$cntl = $view->config( 'client/html/checkout/standard/url/controller', 'checkout' );
			$action = $view->config( 'client/html/checkout/standard/url/action', 'index' );
			$config = $view->config( 'client/html/checkout/standard/url/config', array() );

			$view->summaryUrlServicePayment = $view->url( $target, $cntl, $action, array( 'c_step' => 'payment' ), array(), $config );
			$view->summaryUrlServiceDelivery = $view->url( $target, $cntl, $action, array( 'c_step' => 'delivery' ), array(), $config );

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
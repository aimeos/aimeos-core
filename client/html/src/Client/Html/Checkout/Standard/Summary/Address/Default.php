<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout address summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Summary_Address_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/summary/address/default/subparts';
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
		$view->addressBody = $html;

		$tplconf = 'client/html/checkout/standard/summary/address/default/template-body';
		$default = 'checkout/standard/summary-address-body-default.html';

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
		$view->addressHeader = $html;

		$tplconf = 'client/html/checkout/standard/summary/address/default/template-header';
		$default = 'checkout/standard/summary-address-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/summary/address/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return false;
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
			$default = array(
				'order.base.address.salutation',
				'order.base.address.firstname',
				'order.base.address.lastname',
				'order.base.address.address1',
				'order.base.address.postal',
				'order.base.address.city',
				'order.base.address.langid',
				'order.base.address.email'
			);

			$view->addressBillingMandatory = $view->config( 'checkout/address/billing/mandatory', $default );

			$default = array(
				'order.base.address.company',
				'order.base.address.address2',
				'order.base.address.countryid',
			);

			$view->addressBillingOptional = $view->config( 'checkout/address/billing/optional', $default );


			$default = array(
				'order.base.address.salutation',
				'order.base.address.firstname',
				'order.base.address.lastname',
				'order.base.address.address1',
				'order.base.address.postal',
				'order.base.address.city',
				'order.base.address.langid',
			);

			$view->addressDeliveryMandatory = $view->config( 'checkout/address/delivery/mandatory', $default );

			$default = array(
				'order.base.address.company',
				'order.base.address.address2',
				'order.base.address.countryid',
			);

			$view->addressDeliveryOptional = $view->config( 'checkout/address/delivery/optional', $default );


			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
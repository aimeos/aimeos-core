<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


// Strings for translation
_('address');


/**
 * Default implementation of checkout address HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Address_Default
	extends Client_Html_Abstract
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/address/default/subparts';
	private $_subPartNames = array( 'billing', 'delivery' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		if( $view->get( 'standardStepActive', 'address' ) != 'address' ) {
			return '';
		}

		$view = $this->_setViewParams( $view );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->addressBody = $html;

		$tplconf = 'client/html/checkout/standard/address/default/template-body';
		$default = 'checkout/standard/address-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->getView();

		if( $view->get( 'standardStepActive', 'address' ) != 'address' ) {
			return '';
		}

		$view = $this->_setViewParams( $view );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->addressHeader = $html;

		$tplconf = 'client/html/checkout/standard/address/default/template-header';
		$default = 'checkout/standard/address-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/address/' . $type, $name );
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
		try
		{
			$this->_process( $this->_subPartPath, $this->_subPartNames );
		}
		catch( Exception $e )
		{
			$this->getView()->standardStepActive = 'address';
			throw $e;
		}

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


			$customerManager = MShop_Customer_Manager_Factory::createManager( $context );

			$search = $customerManager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'customer.code', $context->getEditor() ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$items = $customerManager->searchItems( $search );

			if( ( $item = reset( $items ) ) !== false )
			{
				$view->addressCustomerItem = $item;

				$customerAddressManager = $customerManager->getSubManager( 'address' );

				$search = $customerAddressManager->createSearch();
				$search->setConditions( $search->compare( '==', 'customer.address.refid', $item->getId() ) );

				$view->addressCustomerAddressItems = $customerAddressManager->searchItems( $search );
			}


			$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
			$locales = $localeManager->searchItems( $localeManager->createSearch( true ) );

			$languages = array();
			foreach( $locales as $locale ) {
				$languages[] = $locale->getLanguageId();
			}

			$view->addressLanguages = $languages;
			$view->addressCountries = $view->config( 'client/html/common/address/countries', array() );


			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
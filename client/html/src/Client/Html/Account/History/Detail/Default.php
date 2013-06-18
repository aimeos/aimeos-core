<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of acount history detail HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Account_History_Detail_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/account/history/detail/default/subparts';
	private $_subPartNames = array( 'address', 'service', 'basket' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->getView();

		if( $view->param( 'h-action' ) != 'detail' ) {
			return '';
		}

		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->detailBody = $html;

		$tplconf = 'client/html/account/history/detail/default/template-body';
		$default = 'account/history/detail-body-default.html';

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

		if( $view->param( 'h-action' ) != 'detail' ) {
			return '';
		}

		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->detailHeader = $html;

		$tplconf = 'client/html/account/history/detail/default/template-header';
		$default = 'account/history/detail-header-default.html';

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
		return $this->_createSubClient( 'account/history/detail/' . $type, $name );
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
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$context = $this->_getContext();
			$manager = MShop_Order_Manager_Factory::createManager( $context );

			$orderId = $view->param( 'h-order-id', null );

			if( $orderId !== null )
			{
				$search = $manager->createSearch( true );
				$expr = array(
					$search->getConditions(),
					$search->compare( '==', 'order.id', $orderId ),
					$search->compare( '==', 'order.base.customerid', $context->getUserId() ),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$orderItems = $manager->searchItems( $search );

				if( ( $orderItem = reset( $orderItems ) ) === false )
				{
					$msg = $view->translate( 'client/html', 'Order with ID "%1$s" not found' );
					throw new Client_Html_Exception( sprintf( $msg, $orderId ) );
				}

				$view->summaryBasket = $manager->getSubManager( 'base' )->load( $orderItem->getBaseId() );
			}

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
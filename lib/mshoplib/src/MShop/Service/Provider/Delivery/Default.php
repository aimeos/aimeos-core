<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Default delivery provider implementation.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Delivery_Default
	extends MShop_Service_Provider_Delivery_Abstract
	implements MShop_Service_Provider_Delivery_Interface
{

	private $_beConfig = array(
		'project' => array(
			'code' => 'project',
			'internalcode'=> 'project',
			'label'=> 'Project name',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'url' => array(
			'code' => 'url',
			'internalcode'=> 'url',
			'label'=> 'URL to success page',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'username' => array(
			'code' => 'username',
			'internalcode'=> 'username',
			'label'=> 'Username',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'password' => array(
			'code' => 'password',
			'internalcode'=> 'password',
			'label'=> 'Password',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'ssl' => array(
			'code' => 'ssl',
			'internalcode'=> 'ssl',
			'label'=> 'SSL mode ("weak" for self signed certificates)',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
	);


	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration for the provider
	 */
	public function __construct(MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem)
	{
		parent::__construct($context, $serviceItem);
	}


	/**
	 * Sends the order details to the ERP system for further processing.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object to process
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$xml = $this->buildXML( $order );

		$this->_context->getLogger()->log( __METHOD__ . ": XML request =\n" . $xml, MW_Logger_Abstract::INFO );

		$response = $this->_sendRequest( $xml );

		$this->_context->getLogger()->log( __METHOD__ . ": XML response =\n" . trim( $response ), MW_Logger_Abstract::INFO );

		$this->_checkResponse( $response, $order->getId() );

		$order->setDeliveryStatus( MShop_Order_Item_Abstract::STAT_PROGRESS );
	}


	/**
	* Returns the configuration attribute definitions of the provider to generate a list of available fields and
	* rules for the value of each field in the administration interface.
	*
	* @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	*/
	public function getConfigBE()
	{
		$list = array();

		foreach( $this->_beConfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->_checkConfig( $this->_beConfig, $attributes );
	}


	/**
	 * Send http request to a configured url
	 *
	 * @param string $xml Complete order data as valid XML string
	 * @return string response body of a http request
	 * @throws MShop_Service_Exception If the request couldn't be sent
	 */
	protected function _sendRequest( $xml )
	{
		$context = $this->_getContext();
		$response = '';
		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['url'] ) ) {
			throw new MShop_Service_Exception(
				sprintf( 'Missing parameter "%1$s" in service config', "url" ), parent::ERR_TEMP );
		}

		if( ( $curl = curl_init() )=== false ) {
			throw new MShop_Service_Exception( 'Could not initialize curl', parent::ERR_TEMP );
		}

		try
		{
			curl_setopt( $curl, CURLOPT_USERAGENT, 'MShop library' );

			curl_setopt( $curl, CURLOPT_URL, $config['url'] );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, array( 'xml' => $xml ) );

			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 25 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );   // return data as string
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false );   // don't allow redirects
			curl_setopt( $curl, CURLOPT_MAXREDIRS, 1 );   // maximum amount of redirects

			if( isset( $config['username'] ) && isset( $config['password'] ) )
			{
				$context->getLogger()->log( 'Using user name and password for authentication', MW_Logger_Abstract::NOTICE );
				curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
				curl_setopt( $curl, CURLOPT_USERPWD, $config['username'] . ':' . $config['password'] );
			}

			$urlinfo = parse_url($config['url']);
			if (isset($urlinfo['scheme']) && $urlinfo['scheme'] == 'https')
			{
				if( isset( $config['ssl'] ) && $config['ssl'] == 'weak' )
				{
					$context->getLogger()->log( 'Using weak SSL options', MW_Logger_Abstract::NOTICE );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, true );
				}
				else
				{
					$context->getLogger()->log( 'Using strict SSL options', MW_Logger_Abstract::NOTICE );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 );   // check CN and match host name
				}
			}
			else
			{
				$context->getLogger()->log( 'Using no SSL encryption', MW_Logger_Abstract::NOTICE );
			}

			if ( ( $response = curl_exec( $curl ) ) === false ) {
				throw new MShop_Service_Exception(
					sprintf( 'Sending order failed: "%1$s"', curl_error( $curl ) ), parent::ERR_TEMP );
			}

			$curlinfo = curl_getinfo( $curl );
			if( $curlinfo['http_code'] != '200' ) {
				throw new MShop_Service_Exception(
					sprintf( 'Sending order failed with HTTP status "%1$s"', $curlinfo['http_code'] ), parent::ERR_TEMP );
			}

			curl_close( $curl );
		}
		catch( Exception $e )
		{
			curl_close( $curl );
			throw $e;
		}

		return $response;
	}


	/**
	 * Check response sent back from the server after the request
	 *
	 * @param string $response XML sent back by the server
	 * @param integer $invoiceid Number of the order invoice sent to the fulfillment partner
	 * @throws MShop_Service_Exception If the response is invalid
	 */
	protected function _checkResponse( $response, $invoiceid )
	{
		$responseXSD = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR . 'order-response_v1.xsd';

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;

		if ( $dom->loadXML( $response ) !== true ) {
			throw new MShop_Service_Exception(
				sprintf( 'Loading XML response failed "%1$s"', $response ), parent::ERR_XML );
		}

		if( $dom->schemaValidate( $responseXSD ) !== true ) {
			throw new MShop_Service_Exception(
				sprintf( 'Schema validation with "%1$s" failed', $responseXSD ), parent::ERR_SCHEMA );
		}

		$xpath = new DOMXPath( $dom );

		$globalStatus = $xpath->query( '/response/error' )->item(0)->nodeValue;
		if( $globalStatus != 0 ) {
			throw new MShop_Service_Exception( sprintf( 'XML was rejected with code "%1$s"', $globalStatus ) );
		}

		$orderitemlist = $xpath->query( '/response/orderlist/orderitem' );

		foreach( $orderitemlist as $orderitem )
		{
			$id = $xpath->query( 'id', $orderitem )->item(0)->nodeValue;
			$status = $xpath->query( 'status', $orderitem )->item(0)->nodeValue;

			if( $id != $invoiceid ) {
				throw new MShop_Service_Exception(
					sprintf( 'Unknown order ID "%1$s" in response for order "%2$s"', $id, $invoiceid ) );
			}

			if( $status != 0 )
			{
				$msg = $xpath->query( 'message', $orderitem )->item(0)->nodeValue;
				throw new MShop_Service_Exception(
					sprintf( 'Order "%1$s" was rejected with code "%2$s": %3$s', $id, $status, $msg ), $status );
			}
		}
	}


	/**
	 * Builds a complete XML string including the order data
	 *
	 * @param MShop_Order_Item_Interface $invoice Order of the customer
	 * @return string Validated XML string with order data
	 * @throws MShop_Service_Exception If an error occurs
	 */
	public function buildXML( MShop_Order_Item_Interface $invoice )
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() )->getSubManager( 'base' );
		$criteria = $orderBaseManager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.id', $invoice->getBaseId() ) );
		$result = $orderBaseManager->searchItems( $criteria );

		if( ( $base = reset( $result ) ) === false )
		{
			throw new MShop_Order_Exception( sprintf(
				'No order base item for order ID "%1$d" available', $invoice->getId()
			) );
		}


		try
		{
			$dom = new DOMDocument('1.0', 'UTF-8');

			$orderlist = $dom->createElement( 'orderlist');
			$orderitem = $dom->createElement( 'orderitem');

			$this->_buildXMLHeader( $invoice, $base, $dom, $orderitem );
			$this->_buildXMLService( $orderBaseManager, $base, $dom, $orderitem );
			$this->_buildXMLPrice( $orderBaseManager, $base, $dom, $orderitem );
			$this->_buildXMLProducts( $orderBaseManager, $base, $dom, $orderitem );
			$this->_buildXMLAddresses( $orderBaseManager, $base, $dom, $orderitem );
			$this->_buildXMLAdditional( $orderBaseManager, $base, $dom, $orderitem );

			$orderlist->appendChild( $orderitem );
			$dom->appendChild( $orderlist );
		}
		catch( DOMException $e )
		{
			throw new MShop_Service_Exception( 'Creating XML failed: ' . $e->getMessage(), 0, $e );
		}

		$requestXSD = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR . 'order-request_v1.xsd';

		if( $dom->schemaValidate( $requestXSD ) !== true ) {
			throw new MShop_Service_Exception(
				sprintf('Schema validation with "%1$s" failed. domXML: "%2$s".', $requestXSD, $dom->saveXML()), parent::ERR_SCHEMA);
		}

		if ( ( $xml = $dom->saveXML() ) === false ) {
			throw new MShop_Service_Exception( 'XML DOM tree could not be converted to XML string', parent::ERR_XML );
		}

		return $xml;
	}


	/**
	 * Adds the header elements to the XML object
	 *
	 * @param MShop_Order_Item_Interface $invoice Order of the customer
	 * @param MShop_Order_Item_Base_Interface $base Order base item of the customer
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLHeader( MShop_Order_Item_Interface $invoice, MShop_Order_Item_Base_Interface $base,
		DOMDocument $dom, DOMElement $orderitem )
	{
		$regex = '/^(\d+)\-(\d+)\-(\d+) (\d+)\:(\d+)\:(\d+)$/i';
		$date = $invoice->getDatePayment();

		if ( ( $pdate = preg_replace( $regex, '$1-$2-$3T$4:$5:$6Z', $date ) ) === null ) {
				throw new MShop_Service_Exception( sprintf( 'Invalid date format for purchase date: "%1$s"', $date ) );
		}

		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['project'] ) ) {
			throw new MShop_Service_Exception(
				sprintf( 'Missing parameter "%1$s" in service config', "project" ), parent::ERR_TEMP );
		}

		$this->_appendChildCDATA( 'id', $invoice->getId(), $dom, $orderitem );
		$this->_appendChildCDATA( 'type', $invoice->getType(), $dom, $orderitem );
		$this->_appendChildCDATA( 'datetime', $pdate, $dom, $orderitem );

		if ( $invoice->getRelatedId() != null ) {
			$this->_appendChildCDATA( 'relatedid', $invoice->getRelatedId(), $dom, $orderitem );
		}

		$this->_appendChildCDATA( 'customerid', $base->getCustomerId(), $dom, $orderitem );
		$this->_appendChildCDATA( 'projectcode', $config['project'], $dom, $orderitem );
		$this->_appendChildCDATA( 'languagecode', strtoupper( $base->getLocale()->getLanguageId() ), $dom, $orderitem );
		$this->_appendChildCDATA( 'currencycode', $base->getPrice()->getCurrencyId(), $dom, $orderitem );
	}


	/**
	 * Adds the delivery/payment item to the XML object
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order base manager object
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLService( MShop_Common_Manager_Interface $orderBaseManager,
	MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$orderServiceManager = $orderBaseManager->getSubManager( 'service' );
		$criteria = $orderServiceManager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.service.baseid', $base->getId() ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.service.type' ) ) );
		$services = $orderServiceManager->searchItems( $criteria );

		foreach( $services as $service )
		{
			switch( $service->getType() )
			{
				case 'delivery':

					$deliveryitem = $dom->createElement( 'deliveryitem' );
					$this->_appendChildCDATA( 'code', $service->getCode(), $dom, $deliveryitem );
					$this->_appendChildCDATA( 'name', $service->getName(), $dom, $deliveryitem );

					$orderitem->appendChild( $deliveryitem );
					break;

				case 'payment':

					$paymentitem = $dom->createElement( 'paymentitem' );
					$this->_appendChildCDATA( 'code', $service->getCode(), $dom, $paymentitem );
					$this->_appendChildCDATA( 'name', $service->getName(), $dom, $paymentitem );

					$orderServiceAttrManager = $orderServiceManager->getSubManager( 'attribute' );
					$criteria = $orderServiceAttrManager->createSearch();
					$criteria->setConditions(
						$criteria->compare( '==', 'order.base.service.attribute.serviceid', $service->getId() ) );
					$criteria->setSortations( array( $criteria->sort( '+', 'order.base.service.attribute.code' ) ) );
					$attributes = $orderServiceAttrManager->searchItems( $criteria );

					$fieldlist = $dom->createElement( 'fieldlist' );
					foreach( $attributes as $attribute )
					{
						$fielditem = $dom->createElement( 'fielditem' );
						$this->_appendChildCDATA( 'name', $attribute->getCode(), $dom, $fielditem );
						$this->_appendChildCDATA( 'value', $attribute->getValue(), $dom, $fielditem );
						$fieldlist->appendChild( $fielditem );
					}

					$paymentitem->appendChild( $fieldlist );
					break;
			}
		}

		$orderitem->appendChild( $paymentitem );
	}


	/**
	 * Adds the price item to the XML object
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order base manager object
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLPrice( MShop_Common_Manager_Interface $orderBaseManager,
		MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$price = $base->getPrice();
		$total = $price->getValue() + $price->getShipping();

		$priceitem = $dom->createElement( 'priceitem' );
		$this->_appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
		$this->_appendChildCDATA( 'shipping', number_format( $price->getShipping(), 2, '.', '' ), $dom, $priceitem );
		$this->_appendChildCDATA( 'discount', number_format( 0.00, 2, '.', '' ), $dom, $priceitem );
		$this->_appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );

		$orderitem->appendChild( $priceitem );
	}


	/**
	 * Adds the product list to the XML object
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order base manager object
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLProducts( MShop_Common_Manager_Interface $orderBaseManager,
		MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );
		$criteria = $orderProductManager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.product.baseid', $base->getId() ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.product.position' ) ) );
		$products = $orderProductManager->searchItems( $criteria );

		$productlist = $dom->createElement( 'productlist' );

		foreach( $products as $product )
		{
			$price = $product->getPrice();
			$total = $price->getValue() + $price->getShipping();

			$productitem = $dom->createElement( 'productitem' );

			$this->_appendChildCDATA( 'position', $product->getPosition(), $dom, $productitem );
			$this->_appendChildCDATA( 'code', $product->getProductCode(), $dom, $productitem );
			$this->_appendChildCDATA( 'name', $product->getName(), $dom, $productitem );
			$this->_appendChildCDATA( 'quantity', $product->getQuantity(), $dom, $productitem );

			$priceitem = $dom->createElement( 'priceitem' );
			$this->_appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'shipping', number_format( $price->getShipping(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'discount', number_format( 0.00, 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );
			$productitem->appendChild( $priceitem );

			$productlist->appendChild( $productitem );
		}

		$orderitem->appendChild( $productlist );
	}


	/**
	 * Adds the address list to the XML object
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order base manager object
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLAddresses( MShop_Common_Manager_Interface $orderBaseManager,
		MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$orderAddressManager = $orderBaseManager->getSubManager( 'address' );
		$criteria = $orderAddressManager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.address.baseid', $base->getId() ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.address.type' ) ) );
		$addresses = $orderAddressManager->searchItems( $criteria );

		$addresslist = $dom->createElement( 'addresslist' );

		foreach( $addresses as $address ) {
			$this->_buildXMLAddress( $address, $dom, $addresslist );
		}

		$orderitem->appendChild( $addresslist );
	}


	/**
	 * Adds a single address item to the address list of the XML object
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $address Address object with personal information
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $addresslist DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLAddress( MShop_Order_Item_Base_Address_Interface $address,
		DOMDocument $dom, DOMElement $addresslist )
	{
		$addressitem = $dom->createElement( 'addressitem' );

		$this->_appendChildCDATA( 'type', $address->getType(), $dom, $addressitem );
		$this->_appendChildCDATA( 'salutation', $address->getSalutation(), $dom, $addressitem );
		$this->_appendChildCDATA( 'title', $address->getTitle(), $dom, $addressitem );
		$this->_appendChildCDATA( 'firstname', $address->getFirstname(), $dom, $addressitem );
		$this->_appendChildCDATA( 'lastname', $address->getLastname(), $dom, $addressitem );
		$this->_appendChildCDATA( 'company', $address->getCompany(), $dom, $addressitem );
		$this->_appendChildCDATA( 'address1', $address->getAddress1(), $dom, $addressitem );
		$this->_appendChildCDATA( 'address2', $address->getAddress2(), $dom, $addressitem );
		$this->_appendChildCDATA( 'address3', $address->getAddress3(), $dom, $addressitem );
		$this->_appendChildCDATA( 'postalcode', $address->getPostal(), $dom, $addressitem );
		$this->_appendChildCDATA( 'city', $address->getCity(), $dom, $addressitem );
		$this->_appendChildCDATA( 'state', $address->getState(), $dom, $addressitem );
		$this->_appendChildCDATA( 'countrycode', strtoupper( $address->getCountryId() ), $dom, $addressitem );
		$this->_appendChildCDATA( 'email', $address->getEmail(), $dom, $addressitem );
		$this->_appendChildCDATA( 'phone', $address->getTelephone(), $dom, $addressitem );

		$addresslist->appendChild( $addressitem );
	}


	/**
	 * Adds the "additional" section to the XML object
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order base manager object
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLAdditional( MShop_Common_Manager_Interface $orderBaseManager,
		MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$additional = $dom->createElement( 'additional' );
		$this->_appendChildCDATA( 'comment', '', $dom, $additional );

		$emptyCouponItem = $dom->createElement( 'discount' );

		$additional->appendChild( $emptyCouponItem );
		$orderitem->appendChild( $additional );
	}


	/**
	 * Add a child in CData flavour
	 *
	 * @param string $name Name of the new XML element
	 * @param string|integer $value Value of the new XML element
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $parent DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _appendChildCDATA( $name, $value, DOMDocument $dom, DOMElement $parent )
	{
		$child = $dom->createElement($name);
		$child->appendChild($dom->createCDATASection($value));
		$parent->appendChild($child);
	}

}

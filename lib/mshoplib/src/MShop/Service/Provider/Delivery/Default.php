<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
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
		'default.project' => array(
			'code' => 'default.project',
			'internalcode'=> 'default.project',
			'label'=> 'Project name',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'default.url' => array(
			'code' => 'default.url',
			'internalcode'=> 'default.url',
			'label'=> 'URL to webservice the HTTP request is sent to',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'default.username' => array(
			'code' => 'default.username',
			'internalcode'=> 'default.username',
			'label'=> 'Username',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'default.password' => array(
			'code' => 'default.password',
			'internalcode'=> 'default.password',
			'label'=> 'Password',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'default.ssl' => array(
			'code' => 'default.ssl',
			'internalcode'=> 'default.ssl',
			'label'=> 'SSL mode ("weak" for self signed certificates)',
			'type'=> 'string',
			'internaltype'=> 'integer',
			'default'=> 0,
			'required'=> false,
		),
	);


	/**
	 * Sends the order details to the ERP system for further processing.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object to process
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$logger = $this->_getContext()->getLogger();
		$xml = $this->buildXML( $order );

		$logger->log( __METHOD__ . ": XML request =\n" . $xml, MW_Logger_Abstract::INFO );

		$response = $this->_sendRequest( $xml );

		$logger->log( __METHOD__ . ": XML response =\n" . trim( $response ), MW_Logger_Abstract::INFO );

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

		if( !isset( $config['default.url'] ) ) {
			throw new MShop_Service_Exception(
				sprintf( 'Parameter "%1$s" for configuration not available', "url" ), parent::ERR_TEMP );
		}

		if( ( $curl = curl_init() )=== false ) {
			throw new MShop_Service_Exception( sprintf( 'Curl could not be initialized' ), parent::ERR_TEMP );
		}

		try
		{
			curl_setopt( $curl, CURLOPT_USERAGENT, 'MShop library' );

			curl_setopt( $curl, CURLOPT_URL, $config['default.url'] );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, array( 'xml' => $xml ) );

			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 25 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );   // return data as string
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false );   // don't allow redirects
			curl_setopt( $curl, CURLOPT_MAXREDIRS, 1 );   // maximum amount of redirects

			if( isset( $config['default.username'] ) && isset( $config['default.password'] ) )
			{
				$context->getLogger()->log( 'Using user name and password for authentication', MW_Logger_Abstract::NOTICE );
				curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
				curl_setopt( $curl, CURLOPT_USERPWD, $config['default.username'] . ':' . $config['default.password'] );
			}

			$urlinfo = parse_url($config['default.url']);
			if (isset($urlinfo['scheme']) && $urlinfo['scheme'] == 'https')
			{
				if( isset( $config['default.ssl'] ) && $config['default.ssl'] == 'weak' )
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
					sprintf( 'Sending order to delivery provider failed: "%1$s"', curl_error( $curl ) ), parent::ERR_TEMP );
			}

			$curlinfo = curl_getinfo( $curl );
			if( $curlinfo['http_code'] != '200' ) {
				throw new MShop_Service_Exception(
					sprintf( 'Sending order to delivery provider failed with HTTP status "%1$s"', $curlinfo['http_code'] ), parent::ERR_TEMP );
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
				sprintf( 'Loading of XML response "%1$s" from delivery provider failed', $response ), parent::ERR_XML );
		}

		if( $dom->schemaValidate( $responseXSD ) !== true ) {
			throw new MShop_Service_Exception(
				sprintf( 'Validation of XML response from delivery provider against schema "%1$s" failed', $responseXSD ), parent::ERR_SCHEMA );
		}

		$xpath = new DOMXPath( $dom );

		$globalStatus = $xpath->query( '/response/error' )->item(0)->nodeValue;
		if( $globalStatus != 0 ) {
			throw new MShop_Service_Exception( sprintf( 'Order data sent to delivery provider was rejected with code "%1$s" according to XML response', $globalStatus ) );
		}

		$orderitemlist = $xpath->query( '/response/orderlist/orderitem' );

		foreach( $orderitemlist as $orderitem )
		{
			$id = $xpath->query( 'id', $orderitem )->item(0)->nodeValue;
			$status = $xpath->query( 'status', $orderitem )->item(0)->nodeValue;

			if( $id != $invoiceid ) {
				throw new MShop_Service_Exception(
					sprintf( 'Order ID "%1$s" in XML response of delivery provider differs from stored invoice ID "%2$s" of the order', $id, $invoiceid ) );
			}

			if( $status != 0 )
			{
				$msg = $xpath->query( 'message', $orderitem )->item(0)->nodeValue;
				throw new MShop_Service_Exception(
					sprintf( 'Order with ID "%1$s" was rejected with code "%2$s": %3$s', $id, $status, $msg ), $status );
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
		$base = $this->_getOrderBase( $invoice->getBaseId(), MShop_Order_Manager_Base_Abstract::PARTS_ALL );

		try
		{
			$dom = new DOMDocument('1.0', 'UTF-8');

			$orderlist = $dom->createElement( 'orderlist');
			$orderitem = $dom->createElement( 'orderitem');

			$this->_buildXMLHeader( $invoice, $base, $dom, $orderitem );
			$this->_buildXMLService( $base, $dom, $orderitem );
			$this->_buildXMLPrice( $base, $dom, $orderitem );
			$this->_buildXMLProducts( $base, $dom, $orderitem );
			$this->_buildXMLAddresses( $base, $dom, $orderitem );
			$this->_buildXMLAdditional( $base, $dom, $orderitem );

			$orderlist->appendChild( $orderitem );
			$dom->appendChild( $orderlist );
		}
		catch( DOMException $e )
		{
			$msg = 'Creating XML file with order data for delivery provider failed: %1$s';
			throw new MShop_Service_Exception( sprintf( $msg, $e->getMessage() ), 0, $e );
		}

		$requestXSD = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR . 'order-request_v1.xsd';

		if( $dom->schemaValidate( $requestXSD ) !== true )
		{
			$msg = 'Validation of XML response from delivery provider against schema "%1$s" failed: %2$s';
			throw new MShop_Service_Exception( sprintf( $msg, $requestXSD, $dom->saveXML()), parent::ERR_SCHEMA );
		}

		if ( ( $xml = $dom->saveXML() ) === false )
		{
			$msg = 'DOM tree of XML response from delivery provider could not be converted to XML string';
			throw new MShop_Service_Exception( sprintf( $msg ), parent::ERR_XML );
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
				throw new MShop_Service_Exception( sprintf( 'Invalid characters in purchase date "%1$s"', $date ) );
		}

		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['default.project'] ) )
		{
			$msg = 'Parameter "%1$s" for configuration not available';
			throw new MShop_Service_Exception( sprintf( $msg, "project" ), parent::ERR_TEMP );
		}

		$this->_appendChildCDATA( 'id', $invoice->getId(), $dom, $orderitem );
		$this->_appendChildCDATA( 'type', $invoice->getType(), $dom, $orderitem );
		$this->_appendChildCDATA( 'datetime', $pdate, $dom, $orderitem );

		if ( $invoice->getRelatedId() !== null ) {
			$this->_appendChildCDATA( 'relatedid', $invoice->getRelatedId(), $dom, $orderitem );
		}

		$this->_appendChildCDATA( 'customerid', $base->getCustomerId(), $dom, $orderitem );
		$this->_appendChildCDATA( 'projectcode', $config['default.project'], $dom, $orderitem );
		$this->_appendChildCDATA( 'languagecode', strtoupper( $base->getLocale()->getLanguageId() ), $dom, $orderitem );
		$this->_appendChildCDATA( 'currencycode', $base->getPrice()->getCurrencyId(), $dom, $orderitem );
	}


	/**
	 * Adds the delivery/payment item to the XML object
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLService( MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		foreach( $base->getServices() as $service )
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

					$fieldlist = $dom->createElement( 'fieldlist' );
					foreach( $service->getAttributes() as $attribute )
					{
						$fielditem = $dom->createElement( 'fielditem' );
						$this->_appendChildCDATA( 'name', $attribute->getCode(), $dom, $fielditem );
						$this->_appendChildCDATA( 'value', $attribute->getValue(), $dom, $fielditem );
						$this->_appendChildCDATA( 'type', $attribute->getType(), $dom, $fielditem );
						$fieldlist->appendChild( $fielditem );
					}

					$paymentitem->appendChild( $fieldlist );
					$orderitem->appendChild( $paymentitem );
					break;
			}
		}
	}


	/**
	 * Adds the price item to the XML object
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLPrice( MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$price = $base->getPrice();
		$total = $price->getValue() + $price->getCosts();

		$priceitem = $dom->createElement( 'priceitem' );
		$this->_appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
		$this->_appendChildCDATA( 'shipping', number_format( $price->getCosts(), 2, '.', '' ), $dom, $priceitem );
		$this->_appendChildCDATA( 'discount', number_format( 0.00, 2, '.', '' ), $dom, $priceitem );
		$this->_appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );

		$orderitem->appendChild( $priceitem );
	}


	/**
	 * Adds the product list to the XML object
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLProducts( MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$productlist = $dom->createElement( 'productlist' );

		foreach( $base->getProducts() as $product )
		{
			$price = $product->getPrice();
			$total = $price->getValue() + $price->getCosts();

			$productitem = $dom->createElement( 'productitem' );

			$this->_appendChildCDATA( 'position', $product->getPosition(), $dom, $productitem );
			$this->_appendChildCDATA( 'code', $product->getProductCode(), $dom, $productitem );
			$this->_appendChildCDATA( 'name', $product->getName(), $dom, $productitem );
			$this->_appendChildCDATA( 'quantity', $product->getQuantity(), $dom, $productitem );

			$priceitem = $dom->createElement( 'priceitem' );
			$this->_appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'shipping', number_format( $price->getCosts(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'discount', number_format( 0.00, 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );
			$productitem->appendChild( $priceitem );

			if( $product->getType() === 'bundle' ) {
				$this->_buildXMLChildList( $product, $product->getProducts(), $dom, $productitem );
			}

			$productlist->appendChild( $productitem );
		}

		$orderitem->appendChild( $productlist );
	}


	/**
	 * Adds the list of child products to the bundle products in the XML object
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $parent The bundle product
	 * @param array $products List of child products attached to $parent
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $productelement DOM element to which the child products are added
	 */
	protected function _buildXMLChildList( MShop_Order_Item_Base_Product_Interface $parent, array $products, DOMDocument $dom, DOMElement $productelement )
	{
		$childlist = $dom->createElement( 'childlist' );

		foreach( $products as $product )
		{
			$price = $product->getPrice();
			$total = $price->getValue() + $price->getCosts();

			$childproductitem = $dom->createElement( 'productitem' );

			$this->_appendChildCDATA( 'position', $product->getPosition(), $dom, $childproductitem );
			$this->_appendChildCDATA( 'code', $product->getProductCode(), $dom, $childproductitem );
			$this->_appendChildCDATA( 'name', $product->getName(), $dom, $childproductitem );
			$this->_appendChildCDATA( 'quantity', $product->getQuantity(), $dom, $childproductitem );

			$priceitem = $dom->createElement( 'priceitem' );
			$this->_appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'shipping', number_format( $price->getCosts(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'discount', number_format( $price->getRebate(), 2, '.', '' ), $dom, $priceitem );
			$this->_appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );
			$childproductitem->appendChild( $priceitem );

			$childlist->appendChild( $childproductitem );
		}

		$productelement->appendChild( $childlist );
	}


	/**
	 * Adds the address list to the XML object
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLAddresses( MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$addresslist = $dom->createElement( 'addresslist' );

		foreach( $base->getAddresses() as $address ) {
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
		$this->_appendChildCDATA( 'vatid', $address->getVatID(), $dom, $addressitem );

		$addresslist->appendChild( $addressitem );
	}


	/**
	 * Adds the "additional" section to the XML object
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object
	 * @param DOMDocument $dom DOM document object with contains the XML structure
	 * @param DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function _buildXMLAdditional( MShop_Order_Item_Base_Interface $base, DOMDocument $dom, DOMElement $orderitem )
	{
		$additional = $dom->createElement( 'additional' );
		$this->_appendChildCDATA( 'comment', '', $dom, $additional );

		$couponItem = $dom->createElement( 'discount' );

		foreach( $base->getCoupons() as $code => $products ) {
			$this->_appendChildCDATA( 'code', $code, $dom, $couponItem );
		}

		$additional->appendChild( $couponItem );
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

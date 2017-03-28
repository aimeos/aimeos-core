<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * Default delivery provider implementation.
 *
 * @package MShop
 * @subpackage Service
 */
class Standard
	extends \Aimeos\MShop\Service\Provider\Delivery\Base
	implements \Aimeos\MShop\Service\Provider\Delivery\Iface
{

	private $beConfig = array(
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
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object to process
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$logger = $this->getContext()->getLogger();
		$xml = $this->buildXML( $order );

		$logger->log( __METHOD__ . ": XML request =\n" . $xml, \Aimeos\MW\Logger\Base::INFO );

		$response = $this->sendRequest( $xml );

		$logger->log( __METHOD__ . ": XML response =\n" . trim( $response ), \Aimeos\MW\Logger\Base::INFO );

		$this->checkResponse( $response, $order->getId() );

		$order->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		$list = [];

		foreach( $this->beConfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
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
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Send http request to a configured url
	 *
	 * @param string $xml Complete order data as valid XML string
	 * @return string response body of a http request
	 * @throws \Aimeos\MShop\Service\Exception If the request couldn't be sent
	 */
	protected function sendRequest( $xml )
	{
		$context = $this->getContext();
		$response = '';
		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['default.url'] ) ) {
			throw new \Aimeos\MShop\Service\Exception(
				sprintf( 'Parameter "%1$s" for configuration not available', "url" ), parent::ERR_TEMP );
		}

		if( ( $curl = curl_init() ) === false ) {
			throw new \Aimeos\MShop\Service\Exception( sprintf( 'Curl could not be initialized' ), parent::ERR_TEMP );
		}

		try
		{
			curl_setopt( $curl, CURLOPT_USERAGENT, 'MShop library' );

			curl_setopt( $curl, CURLOPT_URL, $config['default.url'] );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, array( 'xml' => $xml ) );

			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 25 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ); // return data as string
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false ); // don't allow redirects
			curl_setopt( $curl, CURLOPT_MAXREDIRS, 1 ); // maximum amount of redirects

			if( isset( $config['default.username'] ) && isset( $config['default.password'] ) )
			{
				$context->getLogger()->log( 'Using user name and password for authentication', \Aimeos\MW\Logger\Base::NOTICE );
				curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
				curl_setopt( $curl, CURLOPT_USERPWD, $config['default.username'] . ':' . $config['default.password'] );
			}

			$urlinfo = parse_url( $config['default.url'] );
			if( isset( $urlinfo['scheme'] ) && $urlinfo['scheme'] == 'https' )
			{
				if( isset( $config['default.ssl'] ) && $config['default.ssl'] == 'weak' )
				{
					$context->getLogger()->log( 'Using weak SSL options', \Aimeos\MW\Logger\Base::NOTICE );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, true );
				}
				else
				{
					$context->getLogger()->log( 'Using strict SSL options', \Aimeos\MW\Logger\Base::NOTICE );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
					curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 ); // check CN and match host name
				}
			}
			else
			{
				$context->getLogger()->log( 'Using no SSL encryption', \Aimeos\MW\Logger\Base::NOTICE );
			}

			if( ( $response = curl_exec( $curl ) ) === false ) {
				throw new \Aimeos\MShop\Service\Exception(
					sprintf( 'Sending order to delivery provider failed: "%1$s"', curl_error( $curl ) ), parent::ERR_TEMP );
			}

			$curlinfo = curl_getinfo( $curl );
			if( $curlinfo['http_code'] != '200' ) {
				throw new \Aimeos\MShop\Service\Exception(
					sprintf( 'Sending order to delivery provider failed with HTTP status "%1$s"', $curlinfo['http_code'] ), parent::ERR_TEMP );
			}

			curl_close( $curl );
		}
		catch( \Exception $e )
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
	 * @throws \Aimeos\MShop\Service\Exception If the response is invalid
	 */
	protected function checkResponse( $response, $invoiceid )
	{
		$responseXSD = __DIR__ . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR . 'order-response_v1.xsd';

		$dom = new \DOMDocument( '1.0', 'UTF-8' );
		$dom->preserveWhiteSpace = false;

		if( $dom->loadXML( $response ) !== true )
		{
			$msg = sprintf( 'Loading of XML response "%1$s" from delivery provider failed', $response );
			throw new \Aimeos\MShop\Service\Exception( $msg, parent::ERR_XML );
		}

		if( $dom->schemaValidate( $responseXSD ) !== true )
		{
			$msg = sprintf( 'Validation of XML response from delivery provider against schema "%1$s" failed', $responseXSD );
			throw new \Aimeos\MShop\Service\Exception( $msg, parent::ERR_SCHEMA );
		}

		$xpath = new \DOMXPath( $dom );

		$globalStatus = $xpath->query( '/response/error' )->item( 0 )->nodeValue;
		if( $globalStatus != 0 )
		{
			$msg = sprintf( 'Order data sent to delivery provider was rejected with code "%1$s" according to XML response', $globalStatus );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$orderitemlist = $xpath->query( '/response/orderlist/orderitem' );

		foreach( $orderitemlist as $orderitem )
		{
			$id = $xpath->query( 'id', $orderitem )->item( 0 )->nodeValue;
			$status = $xpath->query( 'status', $orderitem )->item( 0 )->nodeValue;

			if( $id != $invoiceid )
			{
				$msg = sprintf( 'Order ID "%1$s" in XML response of delivery provider differs from stored invoice ID "%2$s" of the order', $id, $invoiceid );
				throw new \Aimeos\MShop\Service\Exception( $msg );
			}

			if( $status != 0 )
			{
				$str = $xpath->query( 'message', $orderitem )->item( 0 )->nodeValue;
				$msg = sprintf( 'Order with ID "%1$s" was rejected with code "%2$s": %3$s', $id, $status, $str );
				throw new \Aimeos\MShop\Service\Exception( $msg, $status );
			}
		}
	}


	/**
	 * Builds a complete XML string including the order data
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $invoice Order of the customer
	 * @return string Validated XML string with order data
	 * @throws \Aimeos\MShop\Service\Exception If an error occurs
	 */
	public function buildXML( \Aimeos\MShop\Order\Item\Iface $invoice )
	{
		$base = $this->getOrderBase( $invoice->getBaseId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );

		try
		{
			$dom = new \DOMDocument( '1.0', 'UTF-8' );

			$orderlist = $dom->createElement( 'orderlist' );
			$orderitem = $dom->createElement( 'orderitem' );

			$this->buildXMLHeader( $invoice, $base, $dom, $orderitem );
			$this->buildXMLService( $base, $dom, $orderitem );
			$this->buildXMLPrice( $base, $dom, $orderitem );
			$this->buildXMLProducts( $base, $dom, $orderitem );
			$this->buildXMLAddresses( $base, $dom, $orderitem );
			$this->buildXMLAdditional( $base, $dom, $orderitem );

			$orderlist->appendChild( $orderitem );
			$dom->appendChild( $orderlist );
		}
		catch( \DOMException $e )
		{
			$msg = 'Creating XML file with order data for delivery provider failed: %1$s';
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $e->getMessage() ), 0, $e );
		}

		$requestXSD = __DIR__ . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR . 'order-request_v1.xsd';

		if( $dom->schemaValidate( $requestXSD ) !== true )
		{
			$msg = 'Validation of XML response from delivery provider against schema "%1$s" failed: %2$s';
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $requestXSD, $dom->saveXML() ), parent::ERR_SCHEMA );
		}

		if( ( $xml = $dom->saveXML() ) === false )
		{
			$msg = 'DOM tree of XML response from delivery provider could not be converted to XML string';
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg ), parent::ERR_XML );
		}

		return $xml;
	}


	/**
	 * Adds the header elements to the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $invoice Order of the customer
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base item of the customer
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLHeader( \Aimeos\MShop\Order\Item\Iface $invoice, \Aimeos\MShop\Order\Item\Base\Iface $base,
		\DOMDocument $dom, \DOMElement $orderitem )
	{
		$regex = '/^(\d+)\-(\d+)\-(\d+) (\d+)\:(\d+)\:(\d+)$/i';
		$date = $invoice->getDatePayment();

		if( ( $pdate = preg_replace( $regex, '$1-$2-$3T$4:$5:$6Z', $date ) ) === null ) {
				throw new \Aimeos\MShop\Service\Exception( sprintf( 'Invalid characters in purchase date "%1$s"', $date ) );
		}

		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['default.project'] ) )
		{
			$msg = 'Parameter "%1$s" for configuration not available';
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, "project" ), parent::ERR_TEMP );
		}

		$this->appendChildCDATA( 'id', $invoice->getId(), $dom, $orderitem );
		$this->appendChildCDATA( 'type', $invoice->getType(), $dom, $orderitem );
		$this->appendChildCDATA( 'datetime', $pdate, $dom, $orderitem );

		if( $invoice->getRelatedId() !== null ) {
			$this->appendChildCDATA( 'relatedid', $invoice->getRelatedId(), $dom, $orderitem );
		}

		$this->appendChildCDATA( 'customerid', $base->getCustomerId(), $dom, $orderitem );
		$this->appendChildCDATA( 'projectcode', $config['default.project'], $dom, $orderitem );
		$this->appendChildCDATA( 'languagecode', strtoupper( $base->getLocale()->getLanguageId() ), $dom, $orderitem );
		$this->appendChildCDATA( 'currencycode', $base->getPrice()->getCurrencyId(), $dom, $orderitem );
	}


	/**
	 * Adds the delivery/payment item to the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLService( \Aimeos\MShop\Order\Item\Base\Iface $base, \DOMDocument $dom, \DOMElement $orderitem )
	{
		foreach( $base->getServices() as $service )
		{
			switch( $service->getType() )
			{
				case 'delivery':

					$deliveryitem = $dom->createElement( 'deliveryitem' );
					$this->appendChildCDATA( 'code', $service->getCode(), $dom, $deliveryitem );
					$this->appendChildCDATA( 'name', $service->getName(), $dom, $deliveryitem );

					$orderitem->appendChild( $deliveryitem );
					break;

				case 'payment':

					$paymentitem = $dom->createElement( 'paymentitem' );
					$this->appendChildCDATA( 'code', $service->getCode(), $dom, $paymentitem );
					$this->appendChildCDATA( 'name', $service->getName(), $dom, $paymentitem );

					$fieldlist = $dom->createElement( 'fieldlist' );
					foreach( $service->getAttributes() as $attribute )
					{
						$fielditem = $dom->createElement( 'fielditem' );
						$this->appendChildCDATA( 'name', $attribute->getCode(), $dom, $fielditem );
						$this->appendChildCDATA( 'value', $attribute->getValue(), $dom, $fielditem );
						$this->appendChildCDATA( 'type', $attribute->getType(), $dom, $fielditem );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLPrice( \Aimeos\MShop\Order\Item\Base\Iface $base, \DOMDocument $dom, \DOMElement $orderitem )
	{
		$price = $base->getPrice();
		$total = $price->getValue() + $price->getCosts();

		$priceitem = $dom->createElement( 'priceitem' );
		$this->appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
		$this->appendChildCDATA( 'shipping', number_format( $price->getCosts(), 2, '.', '' ), $dom, $priceitem );
		$this->appendChildCDATA( 'discount', number_format( 0.00, 2, '.', '' ), $dom, $priceitem );
		$this->appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );

		$orderitem->appendChild( $priceitem );
	}


	/**
	 * Adds the product list to the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLProducts( \Aimeos\MShop\Order\Item\Base\Iface $base, \DOMDocument $dom, \DOMElement $orderitem )
	{
		$productlist = $dom->createElement( 'productlist' );

		foreach( $base->getProducts() as $product )
		{
			$price = $product->getPrice();
			$total = $price->getValue() + $price->getCosts();

			$productitem = $dom->createElement( 'productitem' );

			$this->appendChildCDATA( 'position', $product->getPosition(), $dom, $productitem );
			$this->appendChildCDATA( 'code', $product->getProductCode(), $dom, $productitem );
			$this->appendChildCDATA( 'name', $product->getName(), $dom, $productitem );
			$this->appendChildCDATA( 'quantity', $product->getQuantity(), $dom, $productitem );

			$priceitem = $dom->createElement( 'priceitem' );
			$this->appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
			$this->appendChildCDATA( 'shipping', number_format( $price->getCosts(), 2, '.', '' ), $dom, $priceitem );
			$this->appendChildCDATA( 'discount', number_format( 0.00, 2, '.', '' ), $dom, $priceitem );
			$this->appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );
			$productitem->appendChild( $priceitem );

			if( $product->getType() === 'bundle' ) {
				$this->buildXMLChildList( $product, $product->getProducts(), $dom, $productitem );
			}

			$productlist->appendChild( $productitem );
		}

		$orderitem->appendChild( $productlist );
	}


	/**
	 * Adds the list of child products to the bundle products in the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $parent The bundle product
	 * @param array $products List of child products attached to $parent
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $productelement DOM element to which the child products are added
	 */
	protected function buildXMLChildList( \Aimeos\MShop\Order\Item\Base\Product\Iface $parent, array $products, \DOMDocument $dom, \DOMElement $productelement )
	{
		$childlist = $dom->createElement( 'childlist' );

		foreach( $products as $product )
		{
			$price = $product->getPrice();
			$total = $price->getValue() + $price->getCosts();

			$childproductitem = $dom->createElement( 'productitem' );

			$this->appendChildCDATA( 'position', $product->getPosition(), $dom, $childproductitem );
			$this->appendChildCDATA( 'code', $product->getProductCode(), $dom, $childproductitem );
			$this->appendChildCDATA( 'name', $product->getName(), $dom, $childproductitem );
			$this->appendChildCDATA( 'quantity', $product->getQuantity(), $dom, $childproductitem );

			$priceitem = $dom->createElement( 'priceitem' );
			$this->appendChildCDATA( 'price', number_format( $price->getValue(), 2, '.', '' ), $dom, $priceitem );
			$this->appendChildCDATA( 'shipping', number_format( $price->getCosts(), 2, '.', '' ), $dom, $priceitem );
			$this->appendChildCDATA( 'discount', number_format( $price->getRebate(), 2, '.', '' ), $dom, $priceitem );
			$this->appendChildCDATA( 'total', number_format( $total, 2, '.', '' ), $dom, $priceitem );
			$childproductitem->appendChild( $priceitem );

			$childlist->appendChild( $childproductitem );
		}

		$productelement->appendChild( $childlist );
	}


	/**
	 * Adds the address list to the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLAddresses( \Aimeos\MShop\Order\Item\Base\Iface $base, \DOMDocument $dom, \DOMElement $orderitem )
	{
		$addresslist = $dom->createElement( 'addresslist' );

		foreach( $base->getAddresses() as $address ) {
			$this->buildXMLAddress( $address, $dom, $addresslist );
		}

		$orderitem->appendChild( $addresslist );
	}


	/**
	 * Adds a single address item to the address list of the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Address object with personal information
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $addresslist DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLAddress( \Aimeos\MShop\Order\Item\Base\Address\Iface $address,
		\DOMDocument $dom, \DOMElement $addresslist )
	{
		$addressitem = $dom->createElement( 'addressitem' );

		$this->appendChildCDATA( 'type', $address->getType(), $dom, $addressitem );
		$this->appendChildCDATA( 'salutation', $address->getSalutation(), $dom, $addressitem );
		$this->appendChildCDATA( 'title', $address->getTitle(), $dom, $addressitem );
		$this->appendChildCDATA( 'firstname', $address->getFirstname(), $dom, $addressitem );
		$this->appendChildCDATA( 'lastname', $address->getLastname(), $dom, $addressitem );
		$this->appendChildCDATA( 'company', $address->getCompany(), $dom, $addressitem );
		$this->appendChildCDATA( 'address1', $address->getAddress1(), $dom, $addressitem );
		$this->appendChildCDATA( 'address2', $address->getAddress2(), $dom, $addressitem );
		$this->appendChildCDATA( 'address3', $address->getAddress3(), $dom, $addressitem );
		$this->appendChildCDATA( 'postalcode', $address->getPostal(), $dom, $addressitem );
		$this->appendChildCDATA( 'city', $address->getCity(), $dom, $addressitem );
		$this->appendChildCDATA( 'state', $address->getState(), $dom, $addressitem );
		$this->appendChildCDATA( 'countrycode', strtoupper( $address->getCountryId() ), $dom, $addressitem );
		$this->appendChildCDATA( 'email', $address->getEmail(), $dom, $addressitem );
		$this->appendChildCDATA( 'phone', $address->getTelephone(), $dom, $addressitem );
		$this->appendChildCDATA( 'vatid', $address->getVatID(), $dom, $addressitem );

		$addresslist->appendChild( $addressitem );
	}


	/**
	 * Adds the "additional" section to the XML object
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Order base object
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $orderitem DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function buildXMLAdditional( \Aimeos\MShop\Order\Item\Base\Iface $base, \DOMDocument $dom, \DOMElement $orderitem )
	{
		$additional = $dom->createElement( 'additional' );
		$this->appendChildCDATA( 'comment', '', $dom, $additional );

		$couponItem = $dom->createElement( 'discount' );

		foreach( $base->getCoupons() as $code => $products ) {
			$this->appendChildCDATA( 'code', $code, $dom, $couponItem );
		}

		$additional->appendChild( $couponItem );
		$orderitem->appendChild( $additional );
	}


	/**
	 * Add a child in CData flavour
	 *
	 * @param string $name Name of the new XML element
	 * @param string|integer $value Value of the new XML element
	 * @param \DOMDocument $dom DOM document object with contains the XML structure
	 * @param \DOMElement $parent DOM element which will be the parent of the new child
	 * @throws DOMException If an error occures
	 */
	protected function appendChildCDATA( $name, $value, \DOMDocument $dom, \DOMElement $parent )
	{
		$child = $dom->createElement( $name );
		$child->appendChild( $dom->createCDATASection( $value ) );
		$parent->appendChild( $child );
	}

}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * JSON RPC frontend controller for ExtJS.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_JsonRpc
{
	private $classprefix = 'Controller_ExtJS';
	private $controllers = array();
	private $cntlPaths;
	private $context;


	/**
	 * Initializes the ExtJS frontend controller.
	 * Should not be instantiated directly. Use getInstance() instead.
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 */
	public function __construct( MShop_Context_Item_Iface $context, array $cntlPaths )
	{
		$this->cntlPaths = $cntlPaths;
		$this->context = $context;
	}


	/**
	 * Creates a JSON encoded list of item and search schemas.
	 *
	 * @return string JSON encoded list of item and search schemas
	 */
	public function getJsonItemSchemas()
	{
		$list = array();

		foreach( $this->getControllers() as $name => $controller ) {
			$list[$name] = $controller->getItemSchema();
		}

		if( ( $json = json_encode( $list ) ) === null ) {
			throw new Exception( 'Unable to encode schemas to JSON' );
		}

		return $json;
	}


	/**
	 * Creates a JSON encoded list of item and search schemas.
	 *
	 * @return string JSON encoded list of item and search schemas
	 */
	public function getJsonSearchSchemas()
	{
		$list = array();

		foreach( $this->getControllers() as $name => $controller ) {
			$list[$name] = $controller->getSearchSchema();
		}

		if( ( $json = json_encode( $list ) ) === null ) {
			throw new Exception( 'Unable to encode schemas to JSON' );
		}

		return $json;
	}


	/**
	 * Creates a JSON encoded service map description.
	 *
	 * @param string $clientUrl URL of the dispatcher action
	 * @return string JSON encoded service map description
	 */
	public function getJsonSmd( $clientUrl )
	{
		$services = array();
		$transport = array( 'envelope' => 'JSON-RPC-2.0', 'transport' => 'POST' );

		foreach( $this->getControllers() as $controller )
		{
			foreach( $controller->getServiceDescription() as $method => $entry ) {
				$services[$method] = $entry + $transport;
			}
		}

		$smd = array(
			'transport' => 'POST',
			'envelope' => 'JSON-RPC-2.0',
			'contentType' => 'application/json',
			'SMDVersion' => '2.0',
			'target' => $clientUrl,
			'services' => $services,
		);

		if( ( $json = json_encode( $smd ) ) === null ) {
			throw new Exception( 'Unable to encode service mapping description to JSON' );
		}

		return $json;
	}


	/**
	 * Single entry point for all ExtJS requests.
	 *
	 * @param array $reqparams Associative list of request parameters (usually $REQUEST)
	 * @param string $inputstream Name of the input stream (usually php://input)
	 * @return string|null JSON RPC 2.0 message response
	 */
	public function process( array $reqparams, $inputstream )
	{
		if( isset( $reqparams['jsonrpc'] ) || !isset( $reqparams['method'] ) ) {
			return $this->processStream( $inputstream );
		}

		return $this->processRequest( $reqparams );
	}


	/**
	 * Processes a request using the request paramters.
	 *
	 * @param array $reqparams Associative list of request parameters (usually $REQUEST)
	 * @return string|null JSON RPC 2.0 message response
	 */
	protected function processRequest( array $reqparams )
	{
		try
		{
			if( !isset( $reqparams['params'] ) || ( $params = json_decode( $reqparams['params'] ) ) === null ) {
				throw new Controller_ExtJS_Exception( 'Required parameters are missing or not JSON encoded' );
			}

			if( ( $result = $this->callMethod( $reqparams['method'], $params ) ) !== null ) {
				return json_encode( $result );
			}
		}
		catch( Exception $e )
		{
			$response = array(
				'error' => array(
					'code' => ( $e->getCode() != 0 ? $e->getCode() : -1 ),
					'message' => $e->getMessage(),
				),
				'status' => 'failure'
			);

			return json_encode( $response );
		}

		return null;
	}


	/**
	 * Processes a request using the input stream.
	 *
	 * @param string $inputstream Name of the input stream (usually php://input)
	 * @return string|null JSON RPC 2.0 message response
	 */
	protected function processStream( $inputstream )
	{
		$response = array();

		try
		{
			if( ( $raw = file_get_contents( $inputstream ) ) === false ) {
				throw new Controller_ExtJS_Exception( 'Unable to read JSON encoded request', -32700 );
			}

			if( ( $request = json_decode( $raw ) ) === null ) {
				throw new Controller_ExtJS_Exception( 'Invalid JSON encoded request', -32700 );
			}

			if( is_array( $request ) )
			{
				foreach( $request as $item )
				{
					if( ( $result = $this->processJson( $item ) ) !== null ) {
						$response[] = $result;
					}
				}
			}
			else if( is_object( $request ) )
			{
				if( ( $result = $this->processJson( $request ) ) !== null ) {
					$response = $result;
				}
			}
			else
			{
				throw new Controller_ExtJS_Exception( 'Invalid JSON RPC request', -32600 );
			}
		}
		catch( Exception $e )
		{
			$response = array(
				'error' => array(
					'code' => ( $e->getCode() != 0 ? $e->getCode() : -1 ),
					'message' => $e->getMessage(),
				),
				'id' => null,
			);
		}

		if( !empty( $response ) ) {
			return json_encode( $response );
		}

		return null;
	}


	/**
	 * Processes a single JSON RPC request.
	 *
	 * @param stdClass $request Object with attributes of JSON RPC request
	 * @return Associative array with JSON RPC response or NULL in case of JSON RPC notifications
	 */
	protected function processJson( stdClass $request )
	{
		$response = array(
			'jsonrpc' => '2.0',
		);

		try
		{
			if( !isset( $request->method ) )
			{
				$msg = sprintf( 'Required attribute "%1$s" missing in request', 'method' );
				throw new Controller_ExtJS_Exception( $msg, -32600 );
			}

			if( !isset( $request->params ) ) {
				throw new Controller_ExtJS_Exception( 'Required parameters are missing in request', -32600 );
			}

			$response['result'] = $this->callMethod( $request->method, $request->params );
		}
		catch( Exception $e )
		{
			$response['error'] = array(
				'code' => ( $e->getCode() != 0 ? $e->getCode() : -1 ),
				'message' => $e->getMessage(),
			);
		}

		if( !isset( $request->id ) ) {
			return null;
		}

		$response['id'] = $request->id;
		return $response;
	}


	/**
	 * Calls the givven method of the specified controller.
	 *
	 * @param string $classmethod Controller/method as "controller.method"
	 * @param stdClass $params Associative array of parameters
	 * @return array Array of results generated by the controller method
	 * @throws Exception If controller or method couldn't be found or an error occured
	 */
	protected function callMethod( $classmethod, stdClass $params )
	{
		$parts = explode( '.', $classmethod );

		if( count( $parts ) !== 2 ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid method "%1$s" in request', $classmethod ), -32602 );
		}

		$class = $parts[0];
		$method = $parts[1];

		$name = $this->getClassPrefix() . '_' . $class . '_Factory';

		if( preg_match( '/^[a-zA-Z0-9\_]+$/', $name ) !== 1 ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid controller factory name "%1$s"', $name ), -32602 );
		}

		if( class_exists( $name ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $name ) );
		}

		if( ( $controller = call_user_func_array( $name . '::createController', array( $this->context ) ) ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Factory "%1$s" not found', $name ), -32602 );
		}

		if( method_exists( $controller, $method ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Method "%1$s" not found', $classmethod ), -32602 );
		}

		return $controller->$method( $params );
	}


	/**
	 * Returns the used prefix for all classes.
	 *
	 * @return string Class prefix (default: "Controller_ExtJS")
	 */
	protected function getClassPrefix()
	{
		return $this->classprefix;
	}


	/**
	 * Returns all available controller instances.
	 *
	 * @return array Associative list of base controller name (Controller_ExtJS_Admin_Log_Default becomes Admin_Log)
	 * 	as key and the class instance as value
	 */
	protected function getControllers()
	{
		if( $this->controllers === array() )
		{
			$subFolder = str_replace( '_', DIRECTORY_SEPARATOR, $this->getClassPrefix() );

			foreach( $this->cntlPaths as $path => $list )
			{
				foreach( $list as $relpath )
				{
					$path .= DIRECTORY_SEPARATOR . $relpath . DIRECTORY_SEPARATOR . $subFolder;

					if( is_dir( $path ) ) {
						$this->addControllers( new DirectoryIterator( $path ) );
					}
				}
			}
		}

		return $this->controllers;
	}


	/**
	 * Instantiates all found factories and stores the controller instances in the class variable.
	 *
	 * @param DirectoryIterator $dir Iterator over the (sub-)directory which might contain a factory
	 * @param string $prefix Part of the class name between "Controller_ExtJS" and "Factory"
	 * @throws Controller_ExtJS_Exception If factory name is invalid or if the controller couldn't be instantiated
	 */
	protected function addControllers( DirectoryIterator $dir, $prefix = '' )
	{
		$classprefix = $this->getClassPrefix();

		foreach( $dir as $entry )
		{
			if( $entry->getType() === 'dir' && $entry->isDot() === false )
			{
				$subdir = new DirectoryIterator( $entry->getPathName() );
				$this->addControllers( $subdir, ( $prefix !== '' ? $prefix . '_' : '' ) . $entry->getBaseName() );
			}
			else if( $prefix !== '' && $entry->getType() === 'file'
				&& ( $name = $entry->getBaseName( '.php' ) ) === 'Factory' )
			{
				$name = $classprefix . '_' . $prefix . '_Factory';

				if( preg_match( '/^[a-zA-Z0-9\_]+$/', $name ) !== 1 ) {
					throw new Controller_ExtJS_Exception( sprintf( 'Invalid controller factory name "%1$s"', $name ) );
				}

				if( class_exists( $name ) === false ) {
					throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $name ) );
				}

				$controller = call_user_func_array( array( $name, 'createController' ), array( $this->context ) );

				if( $controller === false ) {
					throw new Controller_ExtJS_Exception( sprintf( 'Invalid factory "%1$s"', $name ) );
				}

				$this->controllers[$prefix] = $controller;
			}
		}
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Coupon_Code_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;
	private $_testdir;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$this->_testdir = $this->_context->getConfig()->get( 'controller/extjs/attribute/import/text/default/uploaddir', './tmp' );

		if( !is_dir( $this->_testdir ) && mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->_testdir ) );
		}

		$this->_object = new Controller_ExtJS_Coupon_Code_Default( $this->_context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'coupon.code.code' => 'OPQR' ) ),
					1 => array( '==' => (object) array( 'coupon.code.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'coupon.code.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( '2000000', $result['items'][0]->{'coupon.code.count'} );
	}


	public function testSaveDeleteItem()
	{
		$couponManager = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $couponManager->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.label', 'Unit test example' ) );
		$result = $couponManager->searchItems( $search );

		if( ( $couponItem = reset( $result ) ) === false ) {
			throw new Exception( 'No coupon item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'coupon.code.couponid' => $couponItem->getId(),
				'coupon.code.code' => 'zzzz',
				'coupon.code.count' => -1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'coupon.code.code' => 'zzzz' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => array($saved['items']->{'coupon.code.id'}) );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'coupon.code.id'} );
		$this->assertEquals( $saved['items']->{'coupon.code.couponid'}, $searched['items'][0]->{'coupon.code.couponid'} );
		$this->assertEquals( $saved['items']->{'coupon.code.code'}, $searched['items'][0]->{'coupon.code.code'} );
		$this->assertEquals( $saved['items']->{'coupon.code.count'}, $searched['items'][0]->{'coupon.code.count'} );
		$this->assertEquals( $saved['items']->{'coupon.code.datestart'}, $searched['items'][0]->{'coupon.code.datestart'} );
		$this->assertEquals( $saved['items']->{'coupon.code.dateend'}, $searched['items'][0]->{'coupon.code.dateend'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testAbstractInit()
	{
		$expected = array('success' => true);
		$actual = $this->_object->init( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractFinish()
	{
		$expected = array('success' => true);
		$actual = $this->_object->finish( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->_object->getItemSchema();
		$expected = array(
			'name' => 'Coupon_Code',
			'properties' => array(
				'coupon.code.id' => array(
					'description' => 'Coupon code ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.siteid' => array(
					'description' => 'Coupon code site ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.couponid' => array(
					'description' => 'Coupon ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.code' => array(
					'description' => 'Coupon code value',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.count' => array(
					'description' => 'Coupon code quantity',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.datestart' => array(
					'description' => 'Coupon code start date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.dateend' => array(
					'description' => 'Coupon code end date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.ctime' => array(
					'description' => 'Coupon code create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.mtime' => array(
					'description' => 'Coupon code modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.editor' => array(
					'description' => 'Coupon code editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->_object->getSearchSchema();
		$expected = array(
			'criteria' => array(
				'coupon.code.code' => array(
					'description' => 'Coupon code value',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.count' => array(
					'description' => 'Coupon code quantity',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.datestart' => array(
					'description' => 'Coupon code start date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.dateend' => array(
					'description' => 'Coupon code end date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.ctime' => array(
					'description' => 'Coupon code create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.mtime' => array(
					'description' => 'Coupon code modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.editor' => array(
					'description' => 'Coupon code editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->_object->getServiceDescription();

		$this->assertArrayHasKey( 'Coupon_Code.uploadFile', $actual );
		$this->assertArrayHasKey( 'Coupon_Code.importFile', $actual );
	}


	public function testUploadFile()
	{
		$config = $this->_context->getConfig();
		$config->set( 'controller/extjs/coupon/code/default/uploaddir', './tmp' );
		$config->set( 'controller/extjs/coupon/code/default/enablecheck', false );

		$cntlMock = $this->getMockBuilder( 'Controller_ExtJS_Admin_Job_Default' )
			->setMethods( array( 'saveItems' ) )->setConstructorArgs( array( $this->_context ) )->getMock();

		$cntlMock->expects( $this->once() )->method( 'saveItems' );


		$name = 'ControllerExtJSCouponCodeDefaultRun';
		$this->_context->getConfig()->set( 'classes/controller/extjs/admin/job/name', $name );

		Controller_ExtJS_Admin_Job_Factory::injectController( 'Controller_ExtJS_Admin_Job_' . $name, $cntlMock );


		$testfiledir = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$_FILES['unittest'] = array(
			'name' => 'coupon.zip',
			'tmp_name' => $this->_testdir . DIRECTORY_SEPARATOR . 'coupon.zip',
			'error' => UPLOAD_ERR_OK,
		);

		$params = new stdClass();
		$params->items = $this->_testdir . DIRECTORY_SEPARATOR . 'coupon.zip';
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->couponid = '-1';


		$result = $this->_object->uploadFile( $params );

		$this->assertTrue( file_exists( $result['items'] ) );
		unlink( $result['items'] );
	}


	public function testUploadFileExeptionNoFiles()
	{
		$params = new stdClass();
		$params->items = basename( $this->_testdir . DIRECTORY_SEPARATOR . 'coupon.zip' );
		$params->site = 'unittest';

		$_FILES = array();

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->uploadFile( $params );
	}


	public function testImportFile()
	{
		$codeMock = $this->getMockBuilder( 'MShop_Coupon_Manager_Code_Default' )
			->setConstructorArgs( array( $this->_context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$codeMock->expects( $this->exactly( 3 ) )->method( 'saveItem' );

		$mock = $this->getMockBuilder( 'MShop_Coupon_Manager_Default' )
			->setConstructorArgs( array( $this->_context ) )
			->setMethods( array( 'getSubManager' ) )
			->getMock();

		$mock->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $codeMock ) );

		$name = 'ControllerExtJSCouponCodeDefaultRun';
		$this->_context->getConfig()->set( 'classes/coupon/manager/name', $name );

		MShop_Coupon_Manager_Factory::injectManager( 'MShop_Coupon_Manager_' . $name, $mock );


		$testfiledir = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );


		$params = new stdClass();
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->items = $this->_testdir . DIRECTORY_SEPARATOR . 'coupon.zip';
		$params->couponid = '-1';

		$this->_object->importFile( $params );
	}
}
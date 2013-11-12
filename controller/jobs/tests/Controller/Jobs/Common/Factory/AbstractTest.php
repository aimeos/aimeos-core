<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Jobs_Common_Factory_AbstractTest.
 */
class Controller_Jobs_Common_Factory_AbstractTest extends MW_Unittest_Testcase
{
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_arcavias = TestHelper::getArcavias();
		$this->_context = TestHelper::getContext();
		$config = $this->_context->getConfig();

		$config->set( 'controller/jobs/common/decorators/default', array() );
		$config->set( 'controller/jobs/admin/decorators/global', array() );
		$config->set( 'controller/jobs/admin/decorators/local', array() );

	}


	public function testInjectController()
	{
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, $this->_arcavias, 'Default' );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', $cntl );

		$iCntl = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, $this->_arcavias, 'Default' );

		$this->assertSame( $cntl, $iCntl );
	}


	public function testInjectControllerReset()
	{
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, $this->_arcavias, 'Default' );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', $cntl );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', null );

		$new = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, $this->_arcavias, 'Default' );

		$this->assertNotSame( $cntl, $new );
	}

}
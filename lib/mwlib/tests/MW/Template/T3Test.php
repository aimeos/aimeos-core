<?php

/**
 * Test class for MW_Session_CMSLite.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Template_T3Test extends MW_Unittest_Testcase
{
	/**
	 * @var    MW_Template_CMSLite
	 * @access protected
	 */
	private $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$template = 'TYPO3 Template <!--###NAME-->Name<!--NAME###-->';

		$this->_object = new MW_Template_T3( $template );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}


	public function testToString()
	{
		$template = $this->_object->get('NAME');
		$this->assertInstanceOf( 'MW_Template_Interface', $template );

		$this->assertEquals( 'Name', $template->str() );
	}
}

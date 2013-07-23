<?php

/**
 * Test class for MW_Translation_Decorator_Memory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Translation_Decorator_MemoryTest extends MW_Unittest_Testcase
{
	private $_object;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_Translation_Decorator_MemoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$strings = array( 'domain' => array(
			'test singular' => array( 0 => 'translation singular' ),
			'test plural' => array(
				0 => 'plural translation singular',
				1 => 'plural translation plural',
				2 => 'plural translation plural (cs)',
			)
		) );

		$conf = new MW_Translation_None( 'cs' );
		$this->_object = new MW_Translation_Decorator_Memory( $conf, $strings );
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

	public function testDt()
	{
		$this->assertEquals( 'translation singular', $this->_object->dt( 'domain', 'test singular' ) );
	}

	public function testDtNone()
	{
		$this->assertEquals( 'test none', $this->_object->dt( 'domain', 'test none' ) );
	}

	public function testDn()
	{
		$translation = $this->_object->dn( 'domain', 'test plural', 'test plural 2', 1 );
		$this->assertEquals( 'plural translation singular', $translation );
	}

	public function testDnNone()
	{
		$translation = $this->_object->dn( 'domain', 'test none', 'test none plural', 0 );
		$this->assertEquals( 'test none plural', $translation );
	}

	public function testDnPlural()
	{
		$translation = $this->_object->dn( 'domain', 'test plural', 'test plural 2', 2 );
		$this->assertEquals( 'plural translation plural', $translation );
	}

	public function testDnPluralCs()
	{
		$translation = $this->_object->dn( 'domain', 'test plural', 'test plural 2', 5 );
		$this->assertEquals( 'plural translation plural (cs)', $translation );
	}
}

<?php


/**
 * Test class for MShop_Coupon_Provider_Example.
 */
class MShop_Coupon_Provider_ExampleTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_orderBase;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Coupon_Provider_ExampleTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$item = MShop_Coupon_Manager_Factory::createManager( $context )->createItem();

		$outer = null;
		$this->_object = new MShop_Coupon_Provider_Example( $context, $item, '1234', $outer );

		$priceManager = MShop_Price_Manager_Factory::createManager( $context );

		// Don't create order base item by createItem() as this would already register the plugins
		$this->_orderBase = new MShop_Order_Item_Base_Default( $priceManager->createItem(), $context->getLocale() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
		unset( $this->_orderBase );
	}


	public function testAddCoupon()
	{
		$this->_object->addCoupon( $this->_orderBase );
	}

	public function testDeleteCoupon()
	{
		$this->_object->deleteCoupon( $this->_orderBase );
	}

	public function testUpdateCoupon()
	{
		$this->_object->updateCoupon( $this->_orderBase );
	}
}

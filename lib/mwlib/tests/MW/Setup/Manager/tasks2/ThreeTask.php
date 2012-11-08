<?php



class MW_Setup_Task_ThreeTask extends MW_Setup_Task_Abstract
{
	public function getPreDependencies()
	{
		return array( 'TwoTask' );
	}


	public function getPostDependencies()
	{
		return array();
	}


	protected function _mysql()
	{
		$this->_msg( 'Executing ThreeTask' );
		$this->_status( 'OK' );
	}
}

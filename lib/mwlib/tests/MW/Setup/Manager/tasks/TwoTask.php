<?php



class MW_Setup_Task_TwoTask extends MW_Setup_Task_Abstract
{
	public function getPreDependencies()
	{
		return array();
	}


	public function getPostDependencies()
	{
		return array();
	}


	protected function _mysql()
	{
		$this->_msg( 'Executing TwoTask' );
		$this->_status( 'OK' );
	}
}

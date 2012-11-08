<?php



class MW_Setup_Task_OneTask extends MW_Setup_Task_Abstract
{
	public function getPreDependencies()
	{
		return array();
	}


	public function getPostDependencies()
	{
		return array( 'TwoTask');
	}


	protected function _mysql()
	{
		$this->_status( '' );
		$this->_msg( 'Executing OneTask' );
		$this->_status( 'OK' );
	}
}

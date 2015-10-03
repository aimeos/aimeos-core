<?php



class MW_Setup_Task_ThreeTask extends MW_Setup_Task_Base
{
	public function getPreDependencies()
	{
		return array( 'TwoTask' );
	}


	public function getPostDependencies()
	{
		return array();
	}


	protected function mysql()
	{
		$this->msg( 'Executing ThreeTask' );
		$this->status( 'OK' );
	}
}

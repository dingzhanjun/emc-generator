<?php
// class notify error to notice error when run generators to our customers 
class NotifyError
{
	protected $notify_content;
	
	public function __construct($notify_content)
	{
		$this->notify_content = $notify_content;
	}
	
	public function execute()
  	{
		$notify = new Notify();	
		$notify->content = $this->notify_content;
		$notify->save();
  	}
}

?>
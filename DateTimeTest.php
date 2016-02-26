<?php
class Holder {
	public function __construct() {
		$this->now = new DateTime('NOW');
		$this->lastSunday = new DateTime('Last Sunday');
		$this->nextFriday = new DateTime('Next Friday');
		$this->thisFriday = new DateTime('This Friday');
		$this->monthDayCheck = date('m-d');
		$this->thisSaturday = new DateTime('This Saturday');
		$this->thisSaturday = $this->thisSaturday->format('m-d');
	}
}

$holder = new Holder();
echo '<pre>',var_dump($holder),'</pre>';
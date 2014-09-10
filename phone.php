<?php

class Phone extends Main{
	
	function __construct($params){
		parent::__construct($params);

	}

	public function add_money($amount){
		if($amount > 100){
			throw new Exception("You cant pay more than 100 UAH", 1);
		}
		$this->balance += $amount;
		$this->save();
	}

	private function formate_phone_number(){

	}
}
?>
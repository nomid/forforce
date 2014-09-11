<?php
/**
* Класс телефонных номеров с методом пополнения счета
*/
class Phone extends Main{
	
	function __construct($params){
		parent::__construct($params);
		$this->formated_phone = $this->formate_phone();
	}

	public function add_money($amount){
		if($amount > 100){
			throw new Exception("You cant pay more than 100 UAH", 1);
		}
		$this->balance += $amount;
		$this->save();
	}

	private function formate_phone(){
		return '+'.substr($this->country_code, 0, 1).' ('.
			substr($this->country_code, 1).$this->operator_code.') '.
			substr($this->phone_number, 0, 3).'-'.
			substr($this->phone_number, 3, -2).'-'.
			substr($this->phone_number, 5);
	}
}
?>
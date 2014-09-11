<?php
/**
* Класс юзеров
*/
class User extends Main{
	
	/**
	* Конструктор создает объект со свойствами соответствующими таблице
	* юзеров и если юзер существует и имеет телефонные номера, то
	* сохраняет их в массив $this->phones
	*/
	function __construct($params=null){
		parent::__construct($params);
		if($this->is_exists()){
			$phones = array();
			$query =  ' SELECT * 
						FROM phones_tbl
						WHERE user_id='.$this->id.';';
			$result = DB::query($query);
			if($result->num_rows === 0){
				return;
			}
			$phones = DB::fetch();
	        foreach ($phones as &$phone) {
	        	$phone = new Phone($phone);
	        }
	        $this->phones = $phones;
		}
	}

	/**
	* Находит юзера по айди и возвращает объект с данными об этом юзере
	*/
	public static function find_by_id($id){
		DB::query('SELECT * FROM users_tbl WHERE id='.$id);
		$user = DB::fetch();

		return new self($user[0]);
	}

	/**
	* Создает, сохраняет в бд и возвращает юзера
	*/
	public static function create($params){
		$user = new User($params);
		$user->save();
		
		return $user;
	}

	/**
	* Добавляет юзеру номер телефона, принимая объект номера телефона,
	* ассоциативный массив с кодом страны, кодом оператора и т.д. или
	* строку с неформатированым номером телефона.
	* Сохраняет телефонный номер в бд и возвращает его в случае успеха или
	* false при неудаче 
	*/
	public function add_phone_number($phone){
		if(gettype($phone) === 'array'){
			$phone = new Phone($phone);
		}
		else if(gettype($phone) === 'string'){
			$params = array();
			$params['country_code']  = substr($phone, 0, 3);
			$params['operator_code'] = substr($phone, 3, -7);
			$params['phone_number']  = substr($phone, 5);
			$phone = new Phone($params);
		}
		$phone->user_id = $this->id;
		if($phone->save()){
			$this->phones[] = $phone;
			return $phone;	
		}

		return false;
	}
}

?>

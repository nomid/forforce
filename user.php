<?php
/**
* Class for users management
*/
class User extends Main{
	
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

	public static function find_by_id($id){
		DB::query('SELECT * FROM users_tbl WHERE id='.$id);
		$user = DB::fetch();

		return new self($user[0]);
	}

	public static function create($params){
		$user = new User($params);
		$user->save();
		
		return $user;
	}

	public function add_phone_number($phone){
		if(gettype($phone) === 'array'){
			$phone = new Phone($phone);

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

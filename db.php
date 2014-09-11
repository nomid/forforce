<?php
/*класс для работы с базой данных, создает лишь одно подключение к бд
и работает с ним напротяжении всего приложения*/
class DB{
	private static $db;
	private static $result;

	public static function query($query){
		if(empty(self::$db)){
			self::$db = new mysqli('localhost', 'root', '', 'forforce');
		}
		self::$result = self::$db->query($query);
		if(self::$result === false){
			throw new Exception(self::error(), 1);
		}

		return self::$result;
	}

	public static function inserted_id(){
		return self::$db->insert_id;
	}

	public static function error(){
		return self::$db->error;
	}

	public static function fetch(){
		if(empty(self::$result)){
			throw new Exception("nothing to fetch", 1);
		}
		$result = array();
		while ($row = self::$result->fetch_assoc()) {
          $result[] = $row;
        }
        
        return $result;
	}
}
?>
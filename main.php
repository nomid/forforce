<?php
/**
* Родительский класс от которого будут наследоваться класс юзеров и класс
* телефонов. 
*/
class Main{
	/**
	* Конструктор получает все столбцы таблицы и создает свойства объекта,
	* принимая значения из переданного в параметры ассоциативного массива
	*/
	function __construct($params = null){
		$this->table_name = strtolower(get_class($this)).'s_tbl';
		$this->fields = $this->get_fields();
        foreach ($this->fields as $field) {
        	if (isset($params[$field['Field']])){
        		$this->$field['Field'] = $params[$field['Field']] ;
        	}
        }
	}

	/**
	* Создает строку в бд с данными из свойств объекта или обновляет
	* какие-то значения, если строка существует
	*/
	public function save(){
		$fields = $this->fields;
		$columns = array();
		$values = array();
		$action = ($this->is_exists()) ? 'update' : 'create';
		foreach ($fields as $field) {
			if(!isset($this->$field['Field']) || empty($field['Field'])){
				if( $field['Null'] === 'NO' && 
					strpos($field['Extra'], 'auto_increment') === false &&
					$field['Default'] === NULL){
					throw new Exception('Field '.$field['Field'].' cant be blank', 1);	
				}
				continue;
			}

			$columns[]= $field['Field'];
			$values[] = (strpos(strtolower($field['Type']), 'int') === false) ?
						'"'.$this->$field['Field'].'"' : $this->$field['Field']; 
		}

		if($action === 'create'){
			$columns = implode($columns,', ');
			$values  = implode($values,', ');
			$query   = 'INSERT INTO '.$this->table_name.
						'('.$columns.')
						VALUES'.
						'('.$values.');';
		}
		else if($action === 'update'){
			$set = 'SET ';
			for($i=count($columns)-1; $i>=0; $i--){
				$set .=  $columns[$i].'='.$values[$i].', ';
			}
			$set = rtrim($set, ', ');
			$query =   	'UPDATE '.$this->table_name.' '.$set.
						' WHERE id='.$this->id;
		}

		DB::query($query);
		if($action === 'create'){
			$this->id = DB::inserted_id();
		}

		return $this;
	}

	/**
	* Удаляет строку в бд, соответствующую объекту
	*/
	public function destroy(){
		$fields = $this->fields;
		$query 	= 'DELETE FROM '.$this->table_name.' WHERE ';
		foreach ($fields as $field) {
			$value = (strpos(strtolower($field['Type']), 'int') === false) ?
						'"'.$this->$field['Field'].'"' : $this->$field['Field'];
		 	$query .= $field['Field'].'='.$value.' AND ';
		}
		$query = rtrim($query, ' AND ');

		return DB::query($query);
	}

	protected function is_exists(){
		if(!isset($this->id) || empty($this->id)){
			return false;
		}
		$result = DB::query('SELECT id FROM '.$this->table_name.' WHERE id='.$this->id);

		return ($result->num_rows === 0) ? false : true;
	}

	private function get_fields(){
		$result = DB::query('DESCRIBE '.$this->table_name);
		$fields = DB::fetch();

        return $fields;
	}

}
?>
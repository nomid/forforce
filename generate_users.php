<?php
//die('users generated');
$mysqli = new mysqli('localhost', 'root', '', 'forforce');
$test = 'test';

if ($mysqli->connect_error) {
    die('Connection error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
//clear tables to prevent errors
$mysqli->query('TRUNCATE TABLE IF EXISTS phones_tbl;');
$mysqli->query('DROP TABLE IF EXISTS phones_tbl;');
$mysqli->query('TRUNCATE TABLE IF EXISTS users_tbl;');
$mysqli->query('DROP TABLE IF EXISTS users_tbl;');

//create users table
$mysqli->query('CREATE TABLE IF NOT EXISTS users_tbl(
				id 		INT AUTO_INCREMENT PRIMARY KEY,
				name 	CHAR(20),
				birth 	DATE);');

//create phone numbers table
$mysqli->query('CREATE TABLE IF NOT EXISTS phones_tbl(
				id 				INT AUTO_INCREMENT PRIMARY KEY,
				user_id 		INT,
				country_code 	SMALLINT UNSIGNED NOT NULL,
				operator_code 	SMALLINT UNSIGNED NOT NULL,
				phone_number 	INT UNSIGNED NOT NULL,
				balance 		INT NOT NULL DEFAULT 0,
				UNIQUE KEY phone_number(country_code, operator_code, phone_number),
				FOREIGN KEY (user_id) 
			        REFERENCES users_tbl(id)
			        ON DELETE CASCADE);');

for($i=1; $i<=2000; $i++){
	generate_user($i);
}

//generate user using the cycle index as user id
function generate_user($n){
	global $mysqli;

	$name = 'user_'.$n;
	$birth = random_birth_date();

	//generate 1-3 phones for current user
	$number_of_phones = rand(1,3);
	$phones = generate_phones($number_of_phones);

	//insert generated data to database
	$mysqli->query('INSERT INTO users_tbl
					(name, birth)
					VALUES
					("'.$name.'", "'.$birth.'");');
	foreach ($phones as $phone) {
		$mysqli->query('INSERT INTO phones_tbl
						(user_id, country_code, operator_code, phone_number, balance)
						VALUES
						('.$n.', '.$phone['country_code'].', '.$phone['operator_code'].
						', '.$phone['phone_number'].', '.$phone['balance'].');');	
	}
}

function generate_phones($n){
	global $mysqli;

	$phones = array();
	for($i=0; $i<$n; $i++){
		//prevent the possibility of repeating of telephone number
		do{
			$operator_code 	= random_operator_code();
			$phone_number 	= random_phone_number();
			$check_number 	= $mysqli->query('SELECT phone_number 
											FROM phones_tbl
											WHERE phone_number = '.$phone_number.'
											AND country_code=380
											AND operator_code='.$operator_code.';');
		}
		while($check_number->num_rows !== 0);
		$balance = random_balance();
		
		$phone = array( 'country_code' 	=> '380',
						'operator_code'	=> $operator_code,
						'phone_number' 	=> $phone_number,
						'balance'		=> $balance );

		$phones[] = $phone;
	}
	return $phones;
}

function random_birth_date(){
	$begin = strtotime('-90 year');
	$end = strtotime('now');
	$random_date = rand($begin, $end);
	return date('Y-m-d', $random_date);
}

function random_phone_number(){
	return rand(1000000, 9999999);
}

function random_balance(){
	return rand(-50, 150);
}

function random_operator_code(){
	$operator_codes = array(50, 67, 63, 68);
	$random_index = rand(0,3);
	return $operator_codes[$random_index];
}
$mysqli->close();
echo 'Users successfull generated';
?>
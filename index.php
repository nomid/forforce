<?php
include 'main.php';
include 'user.php';
include 'phone.php';
include 'db.php';


// 1. зная ID пользователя получаем его имя, год рождения и список телефонных номеров;
echo '<h3> 1 </h3>';
$id = 14;
$user = User::find_by_id($id);
echo 'User\'s name:'.$user->name.'<br/>';
echo 'User\'s birth date:'.$user->birth.'<br/>';
echo 'Users\'s telephones:<ul>';
foreach ($user->phones as $phone) {
	echo '<li>'.$phone->formated_phone.'</li>';
}
echo '</ul>';

//2. возможность пополнить любой из номеров на сумму до 100 грн максимум за одно пополнение;
echo '<h3> 2 </h3>';
$phone = $user->phones[0];
echo 'Balance before: '.$phone->balance.' UAH<br/>';
$phone->add_money(43);
echo 'Balance after: '.$phone->balance.' UAH<br/>';

//попытка пополнения больше чем на 100 грн приведет к выбросу исключения
//$phone->add_money(170);

//3. возможность добавить нового пользователя;
//можно сделать разными способами
// 1ый: создать объект юзера и вызвать метод save()
echo '<h3> 3 </h3>';
$params = array('name'=>'new user 1', 'birth'=>'1991-07-25');
$user_1 = new User($params);
$user_1->save();
// 2ой: вызвать статический метод класса User create()
$params['name'] = 'new user 2';
$user_2 = User::create($params);

$result = DB::query('SELECT * FROM users_tbl WHERE id='.$user_1->id.';');
echo 'Try to find first created user:<br/>num rows:'.$result->num_rows;
$result = DB::query('SELECT * FROM users_tbl WHERE id='.$user_2->id.';');
echo '<br/>Try to find second created user:<br/>num rows:'.$result->num_rows;

//4. возможность добавить для пользователя номер мобильного телефона;
echo '<h3> 4 </h3>';
$user->add_phone_number('380953326461');
echo 'Users\'s telephones:<ul>';
foreach ($user->phones as $phone) {
	echo '<li>'.$phone->formated_phone.'</li>';
}
echo '</ul>';

//5. возможность удалить всю информацию о пользователе вместе с номерами телефонов;
echo '<h3> 5 </h3>';
$user->destroy();

$user_1->destroy();
$user_2->destroy();

$result = DB::query('SELECT * FROM users_tbl WHERE id='.$id);
echo 'Try find deleted user:<br/>num rows: '.$result->num_rows;
$result = DB::query('SELECT * FROM phones_tbl WHERE user_id='.$id);
echo '<br/>Try find deleted user\'s phone numbers:<br/>num rows: '.$result->num_rows;
?>
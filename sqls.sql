-- 1. total balance
SELECT SUM(balance) AS 'total balance:'
FROM phones_tbl;
-- 2. количество номеров телефонов по операторам
SELECT operator_code, COUNT(*)
FROM phones_tbl
GROUP BY operator_code;
-- 3. количество телефонов у каждого пользователя
SELECT users_tbl.name, COUNT(phones_tbl.id)
FROM users_tbl
INNER JOIN phones_tbl
ON users_tbl.id = phones_tbl.user_id
GROUP BY phones_tbl.user_id;
-- 4. вывести имена 10 пользователей с максимальным балансом на счету
SELECT users_tbl.name
FROM users_tbl
INNER JOIN phones_tbl
ON users_tbl.id = phones_tbl.user_id
ORDER BY phones_tbl.balance DESC
LIMIT 10;
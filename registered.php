<?php

session_name('tzLogin');
session_set_cookie_params(2*7*24*60*60);
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Только для зарегистрированных пользователей! | Демо сайта ruseller.com</title>
    
    <link rel="stylesheet" type="text/css" href="login_panel/css/style.css" media="screen" />
    
</head>

<body>

<div id="main">
  <div class="container">
    <h1>Только для зарегистриованных пользователей!</h1>
    <h2>Зарегистрируйтесь, чтобы получить доступ!</h2>
    </div>
    
    <div class="container">
    
    <?php
	if($_SESSION['id'])
	echo '<h1>Здравствуйте, '.$_SESSION['usr'].'! Добро пожаловать к данным закрытой части!</h1>';
	else echo '<h1>Пожалуйста, <a href="index.php">зарегистрируйтесь</a>, чтобы получить доступ!</h1>';
    ?>
    </div>

	<div class="container">
    <p>Вернуться к <a href="index.php">главной</a> странице демонстрации.</p>
    </div>
	
</body>
</html>

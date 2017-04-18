<?php

define('INCLUDE_CHECK',true);

require 'connect.php';
require 'functions.php';
// Данные два файла нужно включать только в случае определения INCLUDE_CHECK


session_name('tzLogin');
// Запуск сессии

session_set_cookie_params(2*7*24*60*60);
// Устанавливаем время жизни куки 2 недели

session_start();

if($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// Если вы вошли в систему, но куки tzRemember (рестарт браузера) отсутствует
	// и вы не отметили чекбокс 'Запомнить меня':

	$_SESSION = array();
	session_destroy();
	
	// Удалаяем сессию
}


if(isset($_GET['logoff']))
{
	$_SESSION = array();
	session_destroy();
	
	header("Location: index.php");
	exit;
}

if($_POST['submit']=='Войти')
{
	// Проверяем, что представлена форма Войти
	
	$err = array();
	// Запоминаем ошибки
	
	
	if(!$_POST['username'] || !$_POST['password'])
		$err[] = 'Все поля должны быть заполнены!';
	
	if(!count($err))
	{
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		$_POST['password'] = mysql_real_escape_string($_POST['password']);
		$_POST['rememberMe'] = (int)$_POST['rememberMe'];
		
		// Получаем все ввденые данные

		$row = mysql_fetch_assoc(mysql_query("SELECT id,usr FROM tz_members WHERE usr='{$_POST['username']}' AND pass='".md5($_POST['password'])."'"));

		if($row['usr'])
		{
			// Если все в порядке - входим в систему
			
			$_SESSION['usr']=$row['usr'];
			$_SESSION['id'] = $row['id'];
			$_SESSION['rememberMe'] = $_POST['rememberMe'];
			
			// Сохраняем некоторые данные сессии
			
			setcookie('tzRemember',$_POST['rememberMe']);
		}
		else $err[]='Ошибочный пароль или/и имя пользователя!';
	}
	
	if($err)
	$_SESSION['msg']['login-err'] = implode('<br />',$err);
	// Сохраняем сообщение об ошибке сессии

	header("Location: index.php");
	exit;
}
else if($_POST['submit']=='Зарегистрироваться')
{
	// Проверяем, что представлена форма Зарегистрироваться
	
	$err = array();
	
	if(strlen($_POST['username'])<4 || strlen($_POST['username'])>32)
	{
		$err[]='Имя пользователя должно содержать от 3 до 32 символов!';
	}
	
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['username']))
	{
		$err[]='Ваше имя пользователя сожержит недопустимые символы!';
	}
	
	if(!checkEmail($_POST['email']))
	{
		$err[]='Email не правильный!';
	}
	
	if(!count($err))
	{
		// Если нет ошибок
		
		$pass = substr(md5($_SERVER['REMOTE_ADDR'].microtime().rand(1,100000)),0,6);
		// Генерируем случайный пароль
		
		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['username'] = mysql_real_escape_string($_POST['username']);
		// Получаем введеные данные
		
		
		mysql_query("	INSERT INTO tz_members(usr,pass,email,regIP,dt)
						VALUES(
						
							'".$_POST['username']."',
							'".md5($pass)."',
							'".$_POST['email']."',
							'".$_SERVER['REMOTE_ADDR']."',
							NOW()
							
						)");
		
		if(mysql_affected_rows($link)==1)
		{
			send_mail(	'kmvcd@mail.ru',
						$_POST['email'],
						'Регистрация в системе демонстрации - Ваш новый пароль',
						'Ваш пароль: '.$pass);

			$_SESSION['msg']['reg-success']='Мы отправили вам письмо с вашим новым паролем!';
		}
		else $err[]='Данное имя пользователя уже занято!';
	}

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}	
	
	header("Location: index.php");
	exit;
}

$script = '';

if($_SESSION['msg'])
{
	// Скрипт ниже показывает выскаьзывающую панель
	
	$script = '
	<script type="text/javascript">
	
		$(function(){
		
			$("div#panel").show();
			$("#toggle a").toggle();
		});
	
	</script>';
	
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Выполнение задания по созданию сайта отзывов</title>
    
    <link rel="stylesheet" type="text/css" href="login_panel/css/style.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="login_panel/css/slide.css" media="screen" />
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    
    <!-- фиксация PNG для IE6 -->
    <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
    <!--[if lte IE 6]>
        <script type="text/javascript" src="login_panel/js/pngfix/supersleight-min.js"></script>
    <![endif]-->
    
    <script src="login_panel/js/slide.js" type="text/javascript"></script>
    
    <?php echo $script; ?>
</head>

<body>

<!-- Панель -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
												
			</div>
            
            
            <?php
			
			if(!$_SESSION['id']):
			
			?>
            
			<div class="left">
				<!-- Форма входа -->
				<form class="clearfix" action="" method="post">
					<h1>Пожалуйста представьтесь!</h1>
                    
                    <?php
						
						if($_SESSION['msg']['login-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['login-err'].'</div>';
							unset($_SESSION['msg']['login-err']);
						}
					?>
					
					<label class="grey" for="username">Имя пользователя:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="password">Пароль:</label>
					<input class="field" type="password" name="password" id="password" size="23" />
	            	<label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Запомнить меня</label>
        			<div class="clear"></div>
					<input type="submit" name="submit" value="Войти" class="bt_login" />
				</form>
			</div>
			<div class="left right">			
				<!-- Форма регистрации -->
				<form action="" method="post">
					<h1>Регистрация - Введите данные!</h1>		
                    
                    <?php
						
						if($_SESSION['msg']['reg-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
							unset($_SESSION['msg']['reg-err']);
						}
						
						if($_SESSION['msg']['reg-success'])
						{
							echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
							unset($_SESSION['msg']['reg-success']);
						}
					?>
                    		
					<label class="grey" for="username">Имя пользователя:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="email">Email:</label>
					<input class="field" type="text" name="email" id="email" size="23" />
					<label>Пароль будет отправлен Вам по почте.</label>
					<input type="submit" name="submit" value="Зарегистрироваться" class="bt_register" />
				</form>
			</div>
            
            <?php
			
			else:
			
			?>
            
            <div class="left">
            
            <h1>Для зарегистированных пользователей</h1>
            
            <p>Демонстрационные данные</p>
            <a href="registered.php">Перейти на страницу пользователя</a>
            <p>- или -</p>
            <a href="?logoff">Выйти из системы</a>
            
            </div>
            
            <div class="left right">
            </div>
            
            <?php
			endif;
			?>
		</div>
	</div> 

    <!-- Закладка наверху -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left">&nbsp;</li>
	        <li>Здравствуйте, <?php echo $_SESSION['usr'] ? $_SESSION['usr'] : 'Гость';?>!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#"><?php echo $_SESSION['id']?'Открыть панель':'Вход | Регистрация';?></a>
				<a id="close" style="display: none;" class="close" href="#">Закрыть панель</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> 
	
</div> 

<div class="pageContent">
    <div id="main">
      <div class="container">
        <h1>Система регистрации/входа</h1>
          <p>Для регистрации нужно нажать на кнопку <strong>Войти | Зарегистрироваться</strong>.  После регистрации вам будет выслано письмо с паролем.</p>
          <p><a href="registered.php">Демонстрационная страница</a>, доступная только для <strong>зарегистрированных пользователей</strong>.</p>
        </div>
        
        <div class="container">
          <h1>Доска отзывов</h1>
<?php if ($_SESSION['usr']) {

       echo('<form method="post" action="shout.php">
                Ваше имя: <input type="text" id="name" name="name" />
                Сообщение: <input type="text" id="message" name="message" class="message" /> <input type="submit" id="submit" value="Добавить отзыв" />
            </form>');}
       ?>
         <div id="shout"> </div>
          <div class="clear"> </div>
        </div>
</div>

    <script type="text/javascript">
        $(function() {
            refresh_shoutbox();
            setInterval("refresh_shoutbox()", 15000);

            $("#submit").click(function() {
                var name    = $("#name").val();
                var message = $("#message").val();
                var data            = 'name='+ name +'&message='+ message;

                $.ajax({
                    type: "POST",
                    url: "shout.php",
                    data: data,
                    success: function(html){
                        $("#shout").slideToggle(500, function(){
                            $(this).html(html).slideToggle(500);
                            $("#message").val("");
                        });
                    }
                });
                return false;
            });
        });

        function refresh_shoutbox() {
            var data = 'refresh=1';

            $.ajax({
                type: "POST",
                url: "shout.php",
                data: data,
                success: function(html){
                    $("#shout").html(html);
                }
            });
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Создание доски отзывов на PHP, MySQL и jQuery</title>

<link rel="stylesheet" type="text/css" href="default.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#login-trigger').click(function(){
                $(this).next('#login-content').slideToggle();
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
                else $(this).find('span').html('&#x25BC;')
            })
        });
    </script>

</head>

<body>

<header class="cf">
    <nav>
        <ul>
            <li id="login">
                <a id="login-trigger" href="#">
                    Войти <span>&#x25BC;</span>
                </a>
                <div id="login-content">
                    <form>
                        <fieldset id="inputs">
                            <input id="username" type="email" name="Email" placeholder="Ваш email адрес" required>
                            <input id="password" type="password" name="Password" placeholder="Пароль" required>
                        </fieldset>
                        <fieldset id="actions">
                            <input type="submit" id="submit" value="Войти">
                            <label><input type="checkbox" checked="checked"> Запомнить меня</label>
                        </fieldset>
                    </form>
                </div>
            </li>
            <li id="signup">
                <a href="">Регистрация</a>
            </li>
        </ul>
    </nav>
</header>

<div id="page">

    <div class="block rounded">
        <h1>Создание доски отзывов на PHP, MySQL и jQuery</h1>
    </div>
    
    <div class="block_main rounded">
        <h2>Доска отзывов</h2>
        
        <form method="post" action="shout.php">
            Ваше имя: <input type="text" id="name" name="name" /> <br />
            Сообщение: <input type="text" id="message" name="message" class="message" /><input type="submit" id="submit" value="Добавить отзыв" />
        </form>
        
        <div id="shout">
            
        </div>
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
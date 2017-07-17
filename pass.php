<!DOCTYPE html>
 <head>
  <meta charset="utf-8">
  <title>Генератор паролей SanyaFox</title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/font-awesome.min.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="shortcut icon" href="images/favicon/generate.png">
  <style>
   body {
			background:url(images/bd.jpg)  no-repeat;
    -moz-background-size: 100%; /* Firefox 3.6+ */
    -webkit-background-size: 100%; /* Safari 3.1+ и Chrome 4.0+ */
    -o-background-size: 100%; /* Opera 9.6+ */
    background-size: 100%; /* Современные браузеры */
   }
  </style>
 </head>
 <body>
<center><img src="images/safe-password-generator-logo.png" class="img-responsive"></center>
 <div class="container">
<form method=post>
<div class="form-group">
Генератор паролей
<p class="style1">вставьте в поле число количества знаков для с генерируемого пароля </p>
<p>
  <input class="form-control" type=text name=number value="10">
  
  <input  class="form-control" type=submit value="Генерировать">
</p>
<p>Сгенерированный пароль:
  </div>
<form>
  
  
<strong>
  <?php

  // Параметр $number - сообщает число 

  // символов в пароле

  echo generate_password($_POST['number']);

  function generate_password($number)

  {

    $arr = array('a','b','c','d','e','f',

                 'g','h','i','j','k','l',

                 'm','n','o','p','r','s',

                 't','u','v','x','y','z',

                 'A','B','C','D','E','F',

                 'G','H','I','J','K','L',

                 'M','N','O','P','R','S',

                 'T','U','V','X','Y','Z',

                 '1','2','3','4','5','6',

                 '7','8','9','0');

    // Генерируем пароль

    $pass = "";

    for($i = 0; $i < $number; $i++)

    {

      // Вычисляем случайный индекс массива

      $index = rand(0, count($arr) - 1);

      $pass .= $arr[$index];

    }

    return $pass;

  }

?>
</strong></p>
  <table width="20%" bordercolor="%">
    <tr>
      <td></td>
    </tr>
  </table>
<br />
<img src="/images/generate.png" class="img-responsive" style="max-width: 200px; width: 100%;"><h3>Надёжный Пароль</h3><p>Надёжный пароль - это ключ к защите вашей частной жизни и информации онлайн. 
Использование разных паролей для разных сайтов - паролей длинных, с символами разных типов (цифрами, буквами, спец.символами) - поможет Вам защититься от попыток проникновения в ваши 
аккаунты.</p><h3>Как это работает</h3><p>Пароль генерируется на Вашем компьютере, с помощью PHP. Эти пароли никогда не передаются в SanyaFox.ru. <a href="#">Исходный код страницы</a></p>
<h3>Решение проблемы паролей</h3><p>Устали запоминать пароли? Менеджеры паролей, например SanyaFox, помогают Вам хранить пароли в одном безопасном месте, где они зашифрованы и доступны 
только Вам. Встроенный генератор паролей также помогает Вам создавать надёжные пароли когда нужно. </p>

<footer id="footer" class="footer navbar-fixed-bottom"><!--Footer-->
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <p class="pull-left">Copyright © <?php echo  date("Y"); ?></p>
                <p class="pull-right">Александр Винокуров SanyaFox</p>
            </div>
        </div>
    </div>
</footer><!--/Footer-->
</div>
 </body>
</html>

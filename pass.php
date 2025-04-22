<!DOCTYPE html>
 <head>
  <meta charset="utf-8">
  <title>Генератор паролей VelFan'a</title>
  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="images/favicon.png">
 </head>
 <body>
<div class="table-container">
    <div class="table-block footer-push">

      <!-- Primary Page Layout
           –––––––––––––––––––––––––––––––––––––––––––––––––– -->
      <div class="container">
        <div class="row">
          <div class="one-half column" style="margin-top: 25%">
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

<br />
<img src="/images/generate.png" class="img-responsive" style="max-width: 200px; width: 100%;"><h3>Надёжный Пароль</h3><p>Надёжный пароль - это ключ к защите вашей частной жизни и информации онлайн. 
Использование разных паролей для разных сайтов - паролей длинных, с символами разных типов (цифрами, буквами, спец.символами) - поможет Вам защититься от попыток проникновения в ваши 
аккаунты.</p><h3>Как это работает</h3><p>Пароль генерируется на Вашем компьютере, с помощью PHP. Эти пароли никогда не передаются в VelFan'a.ru. <a href="#">Исходный код страницы</a></p>
<h3>Решение проблемы паролей</h3><p>Устали запоминать пароли? Менеджеры паролей, например VelFan'a, помогают Вам хранить пароли в одном безопасном месте, где они зашифрованы и доступны 
только Вам. Встроенный генератор паролей также помогает Вам создавать надёжные пароли когда нужно. </p>
          </div>
        </div>
      </div>  <!-- end primary div.container -->
    </div> <!-- end primary div.table-block -->
    <div class="table-block">
      <!-- Page Footer Layout
           –––––––––––––––––––––––––––––––––––––––––––––––––– -->
      <div class="container">
        <footer id="footer" class="twelve columns">
          Copyright © <?php echo  date("Y"); ?> | Александр Винокуров <a href="/">VelFan'a</a>
        </footer>
      </div> <!-- end footer div.container -->
    </div>  <!-- end footer div.table-block -->
  </div>
</footer><!--/Footer-->
</div>
 </body>
</html>

<!DOCTYPE html>
 <head>
  <meta charset="utf-8">
  <title>Обращения к сервису Whois на PHP </title>
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
<div class="table-container">
    <div class="table-block footer-push">

      <!-- Primary Page Layout
           –––––––––––––––––––––––––––––––––––––––––––––––––– -->
      <div class="container">
        <div class="row">
          <div class="one-half column" style="margin-top: 25%">
<center>
 <form method="post">
 <input type="text" class="form-control" name="ip" size="35">
 <input type="submit" class="form-control" value="Введите IP-адрес" value="<?= htmlspecialchars($_REQUEST['ip']); ?>">
 </form>
</center>
<?php
if(!empty($_POST['ip'])) echo whois("whois.arin.net",$_POST['ip']);
function whois($url,$ip)
{
  // Соединение с сокетом TCP, ожидающим на сервере "whois.arin.net" по 
  // 43 порту. В результате возвращается дескриптор соединения $sock.
  $sock = fsockopen($url, 43, $errno, $errstr);
  if (!$sock) exit("$errno($errstr)");
  else
  {
    echo $url."<br>";
    // Записываем строку из переменной $_POST["ip"] в дескриптор сокета.
    fputs ($sock, $ip."\r\n");
    // Осуществляем чтение из дескриптора сокета.
    $text = "";
    while (!feof($sock))
    {
      $text .= fgets ($sock, 128)."<br>";
    }
    // закрываем соединение
    fclose ($sock);
    // Ищем реферальный сервере
    $pattern = "|ReferralServer: whois://([^\n<:]+)|i";
    preg_match($pattern, $text, $out);
    if(!empty($out[1])) return whois($out[1], $ip);
    else return $text;
  }
}
?>
</p>
<br />
<h3>WHOIS</h3><p>WHOIS (от англ. who is — «кто это?») — сетевой протокол прикладного уровня, 
  базирующийся на протоколе TCP (порт 43). 
Основное применение — получение регистрационных данных о владельцах доменных имён, IP-адресов и автономных систем.</p><h3>Как это работает</h3><p>Мы обращаемся к сайту whois.arin.net и получаем по IP информацию <a href="https://github.com/VelFan/Brutus">Исходный код страницы</a></p>
          </div>
        </div>
      </div>  <!-- end primary div.container -->
    </div> <!-- end primary div.table-block -->

  
  


    <div class="table-block">
      <!-- Page Footer Layout
           –––––––––––––––––––––––––––––––––––––––––––––––––– -->
      <div class="container">
        <footer id="footer" class="twelve columns">
          Copyright © <?php echo  date("Y"); ?> | Александр Винокуров <a href="/">SanyaFox</a>
        </footer>
      </div> <!-- end footer div.container -->
    </div>  <!-- end footer div.table-block -->
  </div>
</footer><!--/Footer-->
</div>
 </body>
</html>

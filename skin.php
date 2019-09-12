<!DOCTYPE html>
<html lang="en">
<head>
<?php 
if(isset($_POST['username'])) {
	$profiles = file_get_contents('https://authserver.ely.by/api/users/profiles/minecraft/'.$_POST['username'].'');
	$info = file_get_contents('http://skinsystem.ely.by/textures/'.$_POST['username'].'');
	$info = json_decode($info, true);
	$profiles = json_decode($profiles, true);


  $uuid = preg_replace('#([a-z0-9]{8})([a-z0-9]{4})([a-z0-9]{4})([a-z0-9]{4})([a-z0-9]{12})#', '$1-$2-$3-$4-$5', $profiles['id']);
}
// Авторизация Debug
$password = '1111';
?>
  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Запрос  <?php if(isset($_POST['username'])) { echo "пользователя ".$profiles['name']; } else { echo "пользователя";} ?> -> Images</title>
  <meta name="description" content="">
  <meta name="author" content="">
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
   a.rollover {
    background: url("<?php echo "http://ely.by/services/skins-renderer?url=".$info['SKIN']['url']."&scale=8.65&slim=0'";?>"); /* Путь к файлу с исходным  рисунком */
    display: block; /* Рисунок как блочный элемент */
    width: 121px;
    height: 276px;
    background-position: -8px 0;
   }
   a.rollover:hover {
    background-position: -146px 0;
   }
  </style>
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
    <div class="row">
      <div class="eleven columns" style="margin-top: 2%">
        <h4>Запрос  <?php if(isset($_POST['username'])) { echo "пользователя ".$profiles['name']; } else { echo "пользователя";} ?> -> Images</h4>
        <p>Вводим ник пользователя и получаем иконку и картинку скина</p>
<?php
//print_r($profiles);

if(isset($_POST['username'])) {
    echo '<br><strong>ID:</strong> '.$profiles['id']. ' '; echo "<strong>UUID:</strong> ".$uuid;
	if($_POST['debug'] == $password) {
    echo ' <strong>ID:</strong> '.$profiles['id'];
		echo '<p><strong>HASH:</strong> '.$info['SKIN']['url'];
		echo "<br><strong>URL ICON:</strong> http://ely.by/services/skins-renderer?url=".$info['SKIN']['url']."&scale=18.9&renderFace=1";
    echo "<br><strong>URL SKIN:</strong> http://ely.by/services/skins-renderer?url=".$info['SKIN']['url']."&scale=8.65&slim=0";
		echo "<br><strong>URL Textures:</strong> ".$info['SKIN']['url']."";
}
	echo "<br><br><img src='http://ely.by/services/skins-renderer?url=".$info['SKIN']['url']."&scale=18.9&renderFace=1' style='width: 150px; height: 150px;'>";
  echo "<br><br><p><a class='rollover'></a></p>";
	echo "<br><br><img src='".$info['SKIN']['url']."'></p>";
}
?>  


				<form  action="oauth.php" method="post">
				  <div class="row">
            
					<div class="six columns">
				      <label for="usernametext">Ник пользователя</label>
				  		<input class="u-full-width" name="username" type="text" placeholder="SanyaFox" id="usernametext">
				  		<label for="debugtext">Доступ к Debug</label>
				  		<input class="u-full-width" name="debug" type="text" placeholder="Оставте пустым, если не знаете пароля" id="debugtext">
				  </div>
				  </div>
				  <input class="button-primary" name="submit"  type="submit" value="Проверить">
				</form>
      </div>
    </div>
  </div>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

</body>
</html>

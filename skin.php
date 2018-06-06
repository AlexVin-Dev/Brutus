<!DOCTYPE html>
<html lang="en">
<head>
<?php 
if(isset($_POST['username'])) {
	$profiles = file_get_contents('https://authserver.ely.by/api/users/profiles/minecraft/'.$_POST['username'].'');
	$info = file_get_contents('http://skinsystem.ely.by/textures/'.$_POST['username'].'');
	$info = json_decode($info, true);
	$profiles = json_decode($profiles, true);
}
// Авторизация Debug
$password = 'password';
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
	if($_POST['debug'] == $password) {
		echo '<p><strong>HASH:</strong> '.$info['SKIN']['hash'];
		echo ' <strong>ID:</strong> '.$profiles['id'];
		echo "<br><strong>URL ICON:</strong> http://ely.by/minecraft/skin_buffer/faces/".$info['SKIN']['hash'].".png ";
		echo "<br><strong>URL SKIN:</strong> http://ely.by/minecraft/skin_buffer/skins/".$info['SKIN']['hash'].".png";

}
	echo "<br><br><img src='http://ely.by/minecraft/skin_buffer/faces/".$info['SKIN']['hash'].".png' style='width: 150px; height: 150px;'>";
	echo "<br><br><img src='http://ely.by/minecraft/skin_buffer/skins/".$info['SKIN']['hash'].".png'></p>";
}
?>  
				<form  action="index.php" method="post">
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

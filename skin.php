<!DOCTYPE html>
<html lang="en">
<head>
<?php

if(isset($_POST['username'])) {
  $profiles = @file_get_contents('https://authserver.ely.by/api/users/profiles/minecraft/'.$_POST['username'].'');
  $profiles = json_decode($profiles, true);

  if (!$profiles != !$_POST['username']) {
       echo 'Пользователь не найден.';
      echo '<br><a href="#" OnClick="history.back();">Назад</a>';
      exit();
   }
  else {
    $info = @file_get_contents('http://skinsystem.ely.by/textures/'.$_POST['username'].'');
    $info = json_decode($info, true);
    $uuid = preg_replace('#([a-z0-9]{8})([a-z0-9]{4})([a-z0-9]{4})([a-z0-9]{4})([a-z0-9]{12})#', '$1-$2-$3-$4-$5', $profiles['id']);
  }
}

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
  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/fix.css">
  <link rel="stylesheet" href="css/style.css">
  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

  <link rel="icon" type="image/png" href="images/favicon.png">
  <script src="https://kit.fontawesome.com/94f30fc4e2.js"></script>
<style>
   a.rollover {
    background: url("<?php echo "http://ely.by/services/skins-renderer?url=".$info['SKIN']['url']."&scale=8.65&slim=0";?>"); /* Путь к файлу с исходным  рисунком */
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
<div class="layout-center-wrap pad40">
<h1 class="t-blue">Запрос  <?php if(isset($_POST['username'])) { echo "пользователя ".$profiles['name']; } else { echo "пользователя";} ?> -> Images</h1>
<p class="italic mar10-t">Вводим ник пользователя и получаем иконку, картинку скина и UUID</p>
<div class="flex mar30-t">
    <div class="w100">
<?php
if(isset($_POST['username'])) {
    echo '<br><strong>ID:</strong> '.$profiles['id']. ' '; echo "<strong>UUID:</strong> ".$uuid;
    echo "<br>";

  if (!$info != !$_POST['username']) {
      echo '<br><br><strong>Голова</strong><br><br><img src="http://ely.by/images/skins/steve-face.png" alt="">';
  } else {
    echo "<br><br><strong>Голова</strong><br><br><img src='http://ely.by/services/skins-renderer?url=".$info['SKIN']['url']."&scale=18.9&renderFace=1' style='width: 150px; height: 150px;'>";
    echo "<br><br><strong>Скин</strong><br><br><a href='http://ely.by/skins?uploader=".$profiles['name']."' target='_blank' class='rollover'></a></p>";
    echo "<br><br><strong>Тикстура</strong><br><br><img src='".$info['SKIN']['url']."'></p>";
  }
}
?>
<form action="skin.php" method="post">
  <p><b>Ник пользователя</b></p>
  <input name="username" type="text" placeholder="Ник" id="usernametext">
  <button name="submit"  type="submit" class="button i-check">Проверить</button>
</form>

    </div>
  </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Football News</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<header class="header">
  <div class="header_left">
    <button class="menu_toggle" type="button" onclick="toggleMenu()">Меню</button>
  </div>

  <div class="header_center">World Football News</div>

  <div class="header_right">
  <?php
    if (!isset($_SESSION["user_id"])) {
        echo '<a href="index.php?action=login" class="btn btn_login">Увійти</a>';
        echo '<a href="index.php?action=registration" class="btn btn_register">Зареєструватися</a>';
    } else {
        echo '<a href="index.php?action=logout" class="btn btn_login">Вийти</a>';
    }
  ?>
</div>
  
</header>
<main class="container">
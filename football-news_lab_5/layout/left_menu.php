<aside class="left_menu" id="leftMenu">
  <h2>Меню</h2>
  <ul>
    <li><a href="index.php?action=main">Головна</a></li>
    <li><a href="index.php?action=news">Новини</a></li>
    <li><a href="index.php?action=about">Про сайт</a></li>

    <?php
      if (!isset($_SESSION["user_id"])) {
          echo '<li><a href="index.php?action=login">Увійти</a></li>';
      } else {
          echo '<li><a href="index.php?action=create_news">Додати новину</a></li>';
          echo '<li><a href="index.php?action=logout">Вийти</a></li>';
      }
    ?>
  </ul>
</aside> 
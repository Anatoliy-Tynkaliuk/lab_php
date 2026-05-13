<?php
require_once("db.php");

$isAdmin = isset($_SESSION["admin"]) && (int)$_SESSION["admin"] === 1;

if ($isAdmin) {
    $sql = "SELECT news.*, users.login AS author_login
            FROM news
            INNER JOIN users ON news.author_id = users.id
            ORDER BY news.date DESC";
    $stmt = $mysqli->prepare($sql);
} else {
    $sql = "SELECT news.*, users.login AS author_login
            FROM news
            INNER JOIN users ON news.author_id = users.id
            WHERE news.visible = 1
            ORDER BY news.date DESC";
    $stmt = $mysqli->prepare($sql);
}

$newsList = [];

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $newsList[] = $row;
    }

    $stmt->close();
}
?>

<section class="content">

  <div class="intro">
    <h2>Футбольні новини</h2>
    <p>На цій сторінці розміщено список футбольних новин.</p>
  </div>

  <?php if (count($newsList) === 0): ?>

    <div class="news_empty">
      <p>Новин поки що немає.</p>
    </div>

  <?php else: ?>

    <div class="news_list">

      <?php foreach ($newsList as $news): ?>

        <article class="news_item">

          <h3><?php echo htmlspecialchars($news["title"]); ?></h3>

          <p>
            <strong>Категорія:</strong>
            <?php echo htmlspecialchars($news["category"]); ?>
          </p>

          <p>
            <strong>Автор:</strong>
            <?php echo htmlspecialchars($news["author_login"]); ?>
          </p>

          <p>
            <strong>Дата додавання:</strong>
            <?php echo htmlspecialchars($news["date"]); ?>
          </p>

          <?php if ($isAdmin): ?>
            <p>
              <strong>Статус:</strong>
              <?php echo ((int)$news["visible"] === 1) ? "Опубліковано" : "Не опубліковано"; ?>
            </p>
          <?php endif; ?>

          <div class="news_text">
            <?php echo htmlspecialchars($news["short_text"]); ?>
          </div>

          <div class="actions">
            <a class="btn btn_login" href="index.php?action=view_news&id=<?php echo (int)$news["id"]; ?>">
              Перегляд
            </a>

            <?php if ($isAdmin): ?>
              <a class="btn btn_register" href="index.php?action=update_news&id=<?php echo (int)$news["id"]; ?>">
                Редагувати
              </a>

              <a
                class="btn btn_delete"
                href="index.php?action=delete_news&id=<?php echo (int)$news["id"]; ?>"
                onclick="return confirm('Ви дійсно хочете видалити цю новину?');"
              >
                Видалити
              </a>
            <?php endif; ?>
          </div>

        </article>

      <?php endforeach; ?>

    </div>

  <?php endif; ?>

</section>
<?php
require_once("db.php");

$id = (int)($_GET["id"] ?? 0);
$isAdmin = isset($_SESSION["admin"]) && (int)$_SESSION["admin"] === 1;

if ($id <= 0) {
    echo '<section class="content"><div class="intro"><h2>Помилка</h2><p>Такої сторінки не існує.</p></div></section>';
    return;
}

if ($isAdmin) {
    $stmt = $mysqli->prepare(
        "SELECT news.*, users.login AS author_login
         FROM news
         INNER JOIN users ON news.author_id = users.id
         WHERE news.id = ?
         LIMIT 1"
    );
} else {
    $stmt = $mysqli->prepare(
        "SELECT news.*, users.login AS author_login
         FROM news
         INNER JOIN users ON news.author_id = users.id
         WHERE news.id = ? AND news.visible = 1
         LIMIT 1"
    );
}

$news = null;

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $news = $result->fetch_assoc();
    $stmt->close();
}

if (!$news) {
    echo '<section class="content"><div class="intro"><h2>Помилка</h2><p>Такої новини не існує або вона не опублікована.</p></div></section>';
    return;
}
?>

<section class="content">
  <div class="intro form_left">
    <h2><?php echo htmlspecialchars($news["title"]); ?></h2>

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

    <hr class="separator">

    <p>
      <?php echo nl2br(htmlspecialchars($news["content"])); ?>
    </p>

    <p class="success_link_wrap">
      <a href="index.php?action=news" class="btn btn_login">Назад до новин</a>

      <?php if ($isAdmin): ?>
        <a href="index.php?action=update_news&id=<?php echo (int)$news["id"]; ?>" class="btn btn_register">
          Редагувати
        </a>
      <?php endif; ?>
    </p>
  </div>
</section>
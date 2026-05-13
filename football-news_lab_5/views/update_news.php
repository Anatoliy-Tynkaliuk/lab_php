<?php
require_once("db.php");

$isAdmin = isset($_SESSION["admin"]) && (int)$_SESSION["admin"] === 1;

if (!$isAdmin) {
    echo '<section class="content"><div class="intro"><h2>Доступ заборонено</h2><p>Редагувати новини може лише адміністратор.</p></div></section>';
    return;
}

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    echo '<section class="content"><div class="intro"><h2>Помилка</h2><p>Такої сторінки не існує.</p></div></section>';
    return;
}

$stmt = $mysqli->prepare("SELECT * FROM news WHERE id = ? LIMIT 1");

$news = null;

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $news = $result->fetch_assoc();
    $stmt->close();
}

if (!$news) {
    echo '<section class="content"><div class="intro"><h2>Помилка</h2><p>Новини з таким ID не існує.</p></div></section>';
    return;
}

$title = $news["title"];
$category = $news["category"];
$short_text = $news["short_text"];
$content = $news["content"];
$visible = (int)$news["visible"];

$titleError = "";
$categoryError = "";
$shortTextError = "";
$contentError = "";
$dbError = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $short_text = trim($_POST["short_text"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $visible = isset($_POST["visible"]) ? 1 : 0;

    $hasErrors = false;

    if (mb_strlen($title) < 5) {
        $titleError = "Заголовок має містити не менше 5 символів.";
        $hasErrors = true;
    }

    if (mb_strlen($category) < 3) {
        $categoryError = "Категорія має містити не менше 3 символів.";
        $hasErrors = true;
    }

    if (mb_strlen($short_text) < 10) {
        $shortTextError = "Короткий опис має містити не менше 10 символів.";
        $hasErrors = true;
    }

    if (mb_strlen($content) < 20) {
        $contentError = "Повний текст новини має містити не менше 20 символів.";
        $hasErrors = true;
    }

    if (!$hasErrors) {
        $stmtUpdate = $mysqli->prepare(
            "UPDATE news
             SET title = ?, category = ?, short_text = ?, content = ?, visible = ?
             WHERE id = ?"
        );

        if ($stmtUpdate) {
            $stmtUpdate->bind_param("ssssii", $title, $category, $short_text, $content, $visible, $id);

            if ($stmtUpdate->execute()) {
                $successMessage = "Новину успішно оновлено.";
            } else {
                $dbError = "Помилка при оновленні новини.";
            }

            $stmtUpdate->close();
        } else {
            $dbError = "Помилка підготовки SQL-запиту.";
        }
    }
}
?>

<section class="content">
  <div class="intro">
    <h2>Редагування футбольної новини</h2>
    <p>На цій сторінці адміністратор може змінити інформацію про новину.</p>
  </div>

  <div class="intro form_left">

    <?php if ($successMessage !== ""): ?>
      <p class="success_message"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <?php if ($dbError !== ""): ?>
      <p class="form_error"><?php echo htmlspecialchars($dbError); ?></p>
    <?php endif; ?>

    <form action="index.php?action=update_news&id=<?php echo (int)$id; ?>" method="post">

      <p class="form_group">
        <label for="title"><strong>Заголовок новини</strong></label><br>
        <input
          type="text"
          id="title"
          name="title"
          value="<?php echo htmlspecialchars($title); ?>"
          class="form_input"
        >
        <?php if ($titleError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($titleError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label for="category"><strong>Категорія</strong></label><br>
        <input
          type="text"
          id="category"
          name="category"
          value="<?php echo htmlspecialchars($category); ?>"
          class="form_input"
        >
        <?php if ($categoryError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($categoryError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label for="short_text"><strong>Короткий опис</strong></label><br>
        <textarea
          id="short_text"
          name="short_text"
          class="form_input textarea_small"
        ><?php echo htmlspecialchars($short_text); ?></textarea>
        <?php if ($shortTextError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($shortTextError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label for="content"><strong>Повний текст новини</strong></label><br>
        <textarea
          id="content"
          name="content"
          class="form_input textarea_big"
        ><?php echo htmlspecialchars($content); ?></textarea>
        <?php if ($contentError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($contentError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label>
          <input
            type="checkbox"
            name="visible"
            value="1"
            <?php if ($visible === 1) echo "checked"; ?>
          >
          <strong>Опублікувати новину на сайті</strong>
        </label>
      </p>

      <p>
        <button type="submit" class="btn btn_register">Зберегти зміни</button>
        <a href="index.php?action=news" class="btn btn_login">Назад</a>
      </p>

    </form>
  </div>
</section>
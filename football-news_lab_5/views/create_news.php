<?php
require_once("db.php");

if (!isset($_SESSION["user_id"])) {
    echo '<section class="content"><div class="intro"><h2>Доступ заборонено</h2><p>Додавати новини можуть лише авторизовані користувачі.</p></div></section>';
    return;
}

$title = "";
$category = "";
$short_text = "";
$content = "";

$titleError = "";
$categoryError = "";
$shortTextError = "";
$contentError = "";
$dbError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $short_text = trim($_POST["short_text"] ?? "");
    $content = trim($_POST["content"] ?? "");

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
        $author_id = (int)$_SESSION["user_id"];

        if (isset($_SESSION["admin"]) && (int)$_SESSION["admin"] === 1) {
            $visible = 1;
        } else {
            $visible = 0;
        }

        $stmt = $mysqli->prepare(
            "INSERT INTO news (title, category, short_text, content, visible, author_id) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param("ssssii", $title, $category, $short_text, $content, $visible, $author_id);

            if ($stmt->execute()) {
                header("Location: index.php?action=news");
                exit;
            } else {
                $dbError = "Помилка при додаванні новини до бази даних.";
            }

            $stmt->close();
        } else {
            $dbError = "Помилка підготовки SQL-запиту.";
        }
    }
}
?>

<section class="content">
  <div class="intro">
    <h2>Додавання футбольної новини</h2>
    <p>Заповніть форму для створення нової новини.</p>
  </div>

  <div class="intro form_left">
    <?php if ($dbError !== ""): ?>
      <p class="form_error"><?php echo htmlspecialchars($dbError); ?></p>
    <?php endif; ?>

    <form action="index.php?action=create_news" method="post">

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
          placeholder="Наприклад: Трансфери, Ліга чемпіонів, Україна"
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

      <p>
        <button type="submit" class="btn btn_register">Додати новину</button>
      </p>

    </form>
  </div>
</section>
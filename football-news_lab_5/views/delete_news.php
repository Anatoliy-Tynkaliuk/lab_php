<?php
require_once("db.php");

$isAdmin = isset($_SESSION["admin"]) && (int)$_SESSION["admin"] === 1;

if (!$isAdmin) {
    echo '<section class="content"><div class="intro"><h2>Доступ заборонено</h2><p>Видаляти новини може лише адміністратор.</p></div></section>';
    return;
}

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    echo '<section class="content"><div class="intro"><h2>Помилка</h2><p>Такої сторінки не існує.</p></div></section>';
    return;
}

$stmt = $mysqli->prepare("SELECT id, title FROM news WHERE id = ? LIMIT 1");

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

$stmtDelete = $mysqli->prepare("DELETE FROM news WHERE id = ?");

if ($stmtDelete) {
    $stmtDelete->bind_param("i", $id);
    $stmtDelete->execute();
    $stmtDelete->close();

    echo '<section class="content">';
    echo '<div class="intro">';
    echo '<h2>Новину видалено</h2>';
    echo '<p>Новину <strong>' . htmlspecialchars($news["title"]) . '</strong> успішно видалено.</p>';
    echo '<p class="success_link_wrap"><a href="index.php?action=news" class="btn btn_login">Повернутися до списку новин</a></p>';
    echo '</div>';
    echo '</section>';
} else {
    echo '<section class="content"><div class="intro"><h2>Помилка</h2><p>Не вдалося підготувати запит для видалення.</p></div></section>';
}
?>
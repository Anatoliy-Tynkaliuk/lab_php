<?php session_start(); ?>
<?php require_once("layout/header.php"); ?>

<?php require_once("layout/left_menu.php"); ?>

<?php
  $allowedPages = [
      "main",
      "about",
      "login",
      "logout",
      "registration",
      "registration_successful",

      "news",
      "create_news",
      "view_news",
      "update_news",
      "delete_news"
  ];

  $action = $_GET["action"] ?? "main";

  if (in_array($action, $allowedPages)) {
      require_once("views/" . $action . ".php");
  } else {
      require_once("views/main.php");
  }
?>

<?php require_once("layout/footer.php"); ?>
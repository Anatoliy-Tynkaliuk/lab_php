<?php
require_once("db.php");

$login = "";
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $mysqli->prepare("SELECT id, login, password, admin FROM users WHERE login = ? LIMIT 1");

    if ($stmt) {
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["login"] = $row["login"];
                $_SESSION["admin"] = $row["admin"];

                header("Location: index.php?action=main");
                exit;
            } else {
                $loginError = "Невірний логін або пароль.";
            }
        } else {
            $loginError = "Невірний логін або пароль.";
        }

        $stmt->close();
    } else {
        $loginError = "Помилка підготовки запиту до бази даних.";
    }
}
?>

<section class="content">
  <div class="intro">
    <h2>Авторизація користувача</h2>
    <p>Введіть логін і пароль.</p>
  </div>

  <div class="intro form_left">

    <form action="index.php?action=login" method="post">

      <p class="form_group">
        <label for="login"><strong>Логін</strong></label><br>
        <input
          type="text"
          id="login"
          name="login"
          value="<?php echo $login ; ?>"
          class="form_input"
        >
      </p>

      <p class="form_group">
        <label for="password"><strong>Пароль</strong></label><br>
        <input
          type="password"
          id="password"
          name="password"
          class="form_input"
        >
      </p>

      <?php
        if ($loginError !== "") {
            echo '<p class="form_error">' . $loginError . '</p>';
        }
      ?>

      <p>
        <button type="submit" class="btn btn_register">Увійти</button>
      </p>

    </form>
  </div>
</section>
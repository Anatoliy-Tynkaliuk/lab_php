<?php
require_once ("db.php");

$login = "";
$email = "";
$country = "";
$loginError = "";
$passwordError = "";
$passwordRepeatError = "";
$emailError = "";
$countryError = "";
$dbError = "";
$countries = [];
$file = "data/countries.csv";

if (file_exists($file)) {
    $handle = fopen($file, "r");

    if ($handle) {
        while (($row = fgetcsv($handle, 0, ";")) !== false) {
            if (count($row) === 2) {
                $code = trim($row[0]);
                $name = trim($row[1]);
                $countries[$code] = $name;
            }
        }
        fclose($handle);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";
    $password_repeat = $_POST["password_repeat"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $country = trim($_POST["country"] ?? "");
    $hasErrors = false;

    if (!preg_match('/^[A-Za-zА-Яа-яІіЇїЄєҐґ0-9_-]{4,}$/u', $login)) {
        $loginError = "Логін має містити не менше 4 символів і може містити лише латинські або кириличні літери, цифри, дефіс та нижнє підкреслення.";
        $hasErrors = true;
    }

    if (!preg_match('/^(?=.*[a-zа-яіїєґ])(?=.*[A-ZА-ЯІЇЄҐ])(?=.*[0-9]).{7,}$/u', $password)) {
        $passwordError = "Пароль має містити не менше 7 символів, хоча б одну велику літеру, одну малу літеру та одну цифру.";
        $hasErrors = true;
    }

    if ($password !== $password_repeat) {
        $passwordRepeatError = "Повтор пароля має співпадати з полем Пароль.";
        $hasErrors = true;
    }

    if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)) {
        $emailError = "Введіть коректну електронну пошту, наприклад: name@gmail.com";
        $hasErrors = true;
    }

    if (!preg_match('/^[A-Z]{2}$/', $country) || !isset($countries[$country])) {
        $countryError = "Потрібно обрати країну зі списку. Допустиме значення — дві великі латинські літери, наприклад UA.";
        $hasErrors = true;
    }

    if (!$hasErrors) {
        $stmtCheck = $mysqli->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
        $stmtCheck->bind_param("ss", $login, $email);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $dbError = "Користувач із таким логіном або електронною поштою вже існує.";
            $hasErrors = true;
        }

        $stmtCheck->close();
    }

    if (!$hasErrors) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $mysqli->prepare("INSERT INTO users (login, password, email, country) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $login, $hashedPassword, $email, $country);

        if ($stmt->execute()) {
            header("Location: index.php?action=registration_successful");
            exit;
        } else {
            $dbError = "Помилка при додаванні користувача до бази даних.";
        }

        $stmt->close();
    }
}
?>

<section class="content">
  <div class="intro">
    <h2>Реєстрація користувача</h2>
    <p>Заповніть форму нижче.</p>
  </div>

  <div class="intro form_left">
    <?php if ($dbError !== ""): ?>
      <p class="form_error"><?php echo htmlspecialchars($dbError); ?></p>
    <?php endif; ?>

    <form action="index.php?action=registration" method="post">

      <p class="form_group">
        <label for="login"><strong>Логін</strong></label><br>
        <input
          type="text"
          id="login"
          name="login"
          value="<?php echo htmlspecialchars($login); ?>"
          class="form_input"
        >
        <?php if ($loginError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($loginError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label for="password"><strong>Пароль</strong></label><br>
        <input
          type="password"
          id="password"
          name="password"
          class="form_input"
        >
        <?php if ($passwordError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($passwordError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label for="password_repeat"><strong>Повторіть пароль</strong></label><br>
        <input
          type="password"
          id="password_repeat"
          name="password_repeat"
          class="form_input"
        >
        <?php if ($passwordRepeatError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($passwordRepeatError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group">
        <label for="email"><strong>Електронна пошта</strong></label><br>
        <input
          type="text"
          id="email"
          name="email"
          value="<?php echo htmlspecialchars($email); ?>"
          class="form_input"
        >
        <?php if ($emailError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($emailError); ?></span>
        <?php endif; ?>
      </p>

      <p class="form_group_select">
        <label for="country"><strong>Країна</strong></label><br>
        <select id="country" name="country" class="form_select">
          <option value="">Оберіть країну</option>
          <?php foreach ($countries as $code => $name): ?>
            <option value="<?php echo htmlspecialchars($code); ?>" <?php if ($country === $code) echo "selected"; ?>>
              <?php echo htmlspecialchars($name); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if ($countryError !== ""): ?>
          <span class="form_error"><?php echo htmlspecialchars($countryError); ?></span>
        <?php endif; ?>
      </p>

      <p>
        <button type="submit" class="btn btn_register">Зареєструватися</button>
      </p>

    </form>
  </div>
</section>
<?php
require_once './php_components/header.php';
require_once 'connection.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_DEFAULT);
    $email = htmlspecialchars(trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
      try {
        // Check if the username and email are unique
        $checkUsername = $db_conn->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $checkUsername->execute(['username' => $username]);
        $usernameCount = $checkUsername->fetchColumn();

        if ($usernameCount > 0) {
          $errors[] = "Username already exists.";
        }

        $checkEmail = $db_conn->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $checkEmail->execute(['email' => $email]);
        $emailCount = $checkEmail->fetchColumn();

        if ($emailCount > 0) {
          $errors[] = "Email already exists.";
        }

        if (empty($errors)) {
          $newUser = $db_conn->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');
          $newUser->execute(
            [
              'username' => $username,
              'password' => $password,
              'email' => $email
            ]
          );
          echo "<h3 class='success'>Your account has been created!</h3>";
        }
      } catch (Exception $e) {
        $errors[] = "An error occurred while creating the account.";
      }
    }
  } else {
    $errors[] = "All fields are required!";
  }
}
?>

<div class="container">
  <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
    <div>
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>
    </div>
    <div>
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>
    </div>
    <div>
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required>
    </div>
    <button type="submit" class="button primary-btn">Register</button>
  </form>

  <form action="/index.php">
    <button type="submit" class="button secondary-btn">Go back</button>
  </form>
</div>
<?php
if (!empty($errors)) {
  foreach ($errors as $error) {
    echo "<p class='error'>Error: $error</p>";
  }
}

require_once './php_components/footer.php';
?>

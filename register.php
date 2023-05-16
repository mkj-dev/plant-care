<?php
require_once './database/connection.php';
require_once './database/UserRepository.php';
require_once './php_components/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $email = htmlspecialchars(trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format.";
    }

    try {
      $userRepository = new UserRepository($db_conn);

      // Check if username or email already exists
      $existingUser = $userRepository->getUserByUsername($username);
      $existingEmail = $userRepository->getUserByEmail($email);

      if ($existingUser) {
        $errors[] = "Username already exists.";
      }

      if ($existingEmail) {
        $errors[] = "Email already exists.";
      }
    } catch (Exception $e) {
      // TODO log the exception to a file
      $errors[] = "An error occurred while creating the account.";
    }
  }
} else {
  $errors[] = "All fields are required!";
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
<?php
if (!empty($errors)) {
  foreach ($errors as $error) {
    echo "<h3 class='error'>Error: $error</h3>";
  }
} else {
  $userRepository->createUser($username, $password, $email);
  echo "<h3 class='success'>Your account has been created!</h3>";
}
?>
</div>

<?php require_once './php_components/footer.php'; ?>

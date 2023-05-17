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

    // Form validation
    if (strlen($username) < 3 || strlen($username) > 50) {
      $errors[] = "Username length must be between 3 and 50 characters long.";
    }
    
    if (strlen($password) < 8 || strlen($password) > 255) {
      $errors[] = "Password length must be between 8 and 255 characters long.";
    } 
    
    if (strlen($email) < 6 || strlen($email) > 100) {
      $errors[] = "Email length must be between 6 and 100 characters long.";
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

    if (empty($errors)) {
      $userRepository->createUser($username, $password, $email);
      echo "<h3 class='success'>Your account has been created!</h3>";
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
      <input type="text" name="username" id="username" minlength="3" maxlength="50" required>
    </div>
    <div>
      <label for="password">Password</label>
      <input type="password" name="password" id="password" minlength="8" maxlength="255" required>
    </div>
    <div>
      <label for="email">Email</label>
      <input type="email" name="email" id="email" minlength="6" maxlength="100" required>
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
}
?>
</div>

<?php require_once './php_components/footer.php'; ?>

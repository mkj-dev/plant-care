<?php
require_once './database/connection.php';
require_once './database/UserRepository.php';
require_once './php_components/header.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    $userRepository = new UserRepository($db_conn);
    $user = $userRepository->getUserByEmail($email);
    
    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      header('Location: dashboard.php');
      exit();
    } else {
      $errors[] = "Incorrect email or password!";
    }
  } else {
      $errors[] = "All fields are required!";
  }
}
?>
<div class="container">
  <form method="post">
    <div>
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required>
    </div>  
    <div>
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>
    </div>
    <button type="submit" class="button primary-btn">Log In</button>
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

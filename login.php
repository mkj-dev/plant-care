<?php
require_once './php_components/header.php';
require_once 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    $getUser = $db_conn->prepare('SELECT * FROM users WHERE email = :email');
    $getUser->execute(['email' => $email]);
    $user = $getUser->fetch();
    
    if ($user) {
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
      } else {
        echo "<p class='error'>Incorrect password.";
      }
    } else {
      echo "<p class='error'>User not found.</p>";
    }
  } else {
    echo "<p class='error'>All fields are required!</p>";
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
</div>
<?php require_once './php_components/footer.php'; ?>

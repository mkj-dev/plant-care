<?php
require_once './php_components/header.php';
require_once 'connection.php';
session_start();

// Check if user is logged in, redirect to login.php if not
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$userId = $_SESSION['user_id'];

try {
  $getUser = $db_conn->prepare('SELECT * FROM users WHERE id = :id');
  $getUser->execute(['id' => $userId]);
  $user = $getUser->fetch();
} catch (Exception $e) {
  echo "<h3 class='error'>An error occurred while retrieving user information. Please try again later.</h3>";
  exit();
}

if ($user) {
  $username = $user['username'];
  $email = $user['email'];
} else {
  echo "<h3 class='error'>User not found...</h3>";
}
?>

<div class="container">
  <h2>Hello, <?= htmlspecialchars($username) ?></h2>
  <form action="logout.php" method="post">
    <button type="submit" class="button logout-btn">Logout</button>
  </form>
</div>

<?php require_once './php_components/footer.php'; ?>

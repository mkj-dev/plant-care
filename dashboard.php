<?php
require_once './database/connection.php';
require_once './database/UserRepository.php';
require_once './database/PlantRepository.php';
require_once './php_components/header.php';
session_start();

// Check if user is logged in, redirect to login.php if not
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$userId = $_SESSION['user_id'];
$errors = [];

try {
  $userRepository = new UserRepository($db_conn);
  $plantRepository = new PlantRepository($db_conn);
  $user = $userRepository->getUserById($userId);
} catch (Exception $e) {
  // TODO log the exception to a file
  $errors[] = "An error occurred while retrieving user information. Please try again later.";
  exit();
}

if ($user) {
  $username = $user['username'];
  $email = $user['email'];
} else {
  $errors[] = "User not found...";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(!empty($_POST['plant_name']) && !empty($_POST['plant_description']) && isset($_FILES['plant_photo']) && $_FILES['plant_photo']['error'] === UPLOAD_ERR_OK ) {
    $plantName = htmlspecialchars(trim($_POST['plant_name']));
    $plantDescription = htmlspecialchars(trim($_POST['plant_description']));
    $plantPhoto = $_FILES['plant_photo'];

    // Validate photo
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $uploadPath = "./uploads/plant_photos/user/" . $userId . "/";
    $fileExtension = strtolower(pathinfo($plantPhoto['name'], PATHINFO_EXTENSION));

    // Create new directory if it doesn't exist
    if (!is_dir($uploadPath)) {
      mkdir($uploadPath, 0644, true);
    }
  
    if (!in_array($fileExtension, $allowedExtensions)) {
      $errors[] = "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
    } else {
      $fileName = uniqid() . '.' . $fileExtension;
      $targetPath = $uploadPath . $fileName;

      if (!move_uploaded_file($plantPhoto['tmp_name'], $targetPath)) {
        $errors[] = "Error uploading photo. Please try again.";
      }
    }

    if (empty($errors)) {
      try {
        $plantId = $plantRepository->createPlant($userId, $plantName, $plantDescription, $targetPath);

        if ($plantId) {
          echo "<h3 class='success'>Plant added successfully!</h3>";
        } else {
          $errors[] = "An error occurred while adding the plant.";
        }
      } catch (Exception $e) {
          // TODO log the exception to a file
          $errors[] = "An error occurred while adding the plant.";
      }
    }
  } else {
    $errors[] = "All fields are required!";
  }
}

// Fetch plants by user ID
try {
  $plants = $plantRepository->getPlantsByUserId($userId);
} catch (Exception $e) {
  // TODO log the exception to a file
  $errors[] = "An error occurred while retrieving plants.";
}

if (!empty($errors)) {
  foreach ($errors as $error) {
    echo "<h3 class='error'>Error: $error</h3>";
  }
}
?>

<div class="container">
  <div class="flex-container">
    <h2>Hello, <?= htmlspecialchars($username) ?></h2>
    <form action="logout.php">
      <button type="submit" class="button logout-btn">Logout</button>
    </form>
  </div>

  <details>
    <summary>Add new plant</summary>
    <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
      <div>
        <label for="plant_name">Name</label>
        <input type="text" name="plant_name" id="plant_name" minlength="3" maxlength="50" required>
      </div>
      <div>
        <label for="plant_description">Description</label>
        <textarea name="plant_description" id="plant_description" maxlength="1000" required></textarea>
      </div>
      <div>
        <label for="plant_photo">Photo</label>
        <input type="file" name="plant_photo" id="plant_photo" accept="image/jpg, image/jpeg, image/png" required>
      </div>
      <button type="submit" class="button primary-btn">Add Plant</button>
    </form>
  </details>

  <details>
    <summary>Your Plants</summary>
    <div class="plant-container">
    <?php
    if (!empty($plants)) {
      foreach ($plants as $plant) {
        echo "<div class='plant-card'>";
        echo "<h4>" . htmlspecialchars($plant['plant_name']) . "</h4>";
        echo "<p>" . htmlspecialchars($plant['plant_description']) . "</p>";
        echo "<img src='" . $plant['photo_src'] . "' alt='" . htmlspecialchars($plant['plant_name']) . "'>";
        echo "</div>";
      }
    } else {
      echo "<h4>No plants found.</h4>";
    }
    ?>
    </div>
  </details>
</div>

<?php require_once './php_components/footer.php'; ?>

<?php
class UserRepository {
  private $db_conn;

  public function __construct($db_conn) {
    $this->db_conn = $db_conn;
  }

  public function createUser($username, $password, $email) {
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $existingUsername = $this->getUserByUsername($username);
    $existingEmail = $this->getUserByEmail($email);

    if ($existingUsername || $existingEmail) {
      return false; // Indicate that the username or email already exists
    }

    $newUser = $this->db_conn->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');
    $newUser->execute([
      'username' => $username,
      'password' => $hashedPassword,
      'email' => $email
    ]);

    return true; // Indicate successful user creation
  }

  public function getUserByUsername($username) {
    $getUser = $this->db_conn->prepare('SELECT * FROM users WHERE username = :username');
    $getUser->execute(['username' => $username]);
    
    return $getUser->fetch();
  }

  public function getUserByEmail($email) {
    $getUser = $this->db_conn->prepare('SELECT * FROM users WHERE email = :email');
    $getUser->execute(['email' => $email]);
    
    return $getUser->fetch();
  }
  
  public function getUserById($id) {
    $getUser = $this->db_conn->prepare('SELECT * FROM users WHERE id = :id');
    $getUser->execute(['id' => $id]);
    
    return $getUser->fetch();    
  }
}

<?php
class PlantRepository {
  private $db_conn;

  public function __construct($db_conn) {
    $this->db_conn = $db_conn;
  }

  public function createPlant($userId, $plantName, $plantDescription, $photoSrc) {
    $createPlant = $this->db_conn->prepare('INSERT INTO plants (user_id, plant_name, plant_description, photo_src) 
    VALUES (:user_id, :plant_name, :plant_description, :photo_src)');

    $createPlant->execute([
      'user_id' => $userId,
      'plant_name' => $plantName,
      'plant_description' => $plantDescription,
      'photo_src' => $photoSrc
    ]);

    return $this->db_conn->lastInsertId();
  }

  public function getPlantById($plantId) {
    $getPlant = $this->db_conn->prepare('SELECT * FROM plants WHERE id = :id'); 
    $getPlant->execute(['id' => $plantId]);

    return $getPlant->fetch();
  }

  public function updatePlant($plantId, $plantName, $plantDescription, $photoSrc) {
    $updatePlant = $this->db_conn->prepare('UPDATE plants SET plant_name = :plant_name, plant_description = :plant_description, photo_src = :photo_src WHERE id = :id');
    
    $updatePlant->execute([
      'id' => $plantId,
      'plant_name' => $plantName,
      'plant_description' => $plantDescription,
      'photo_src' => $photoSrc
    ]);

    return $updatePlant->rowCount() > 0;
  }

  public function deletePlant($plantId) {
    $deletePlant = $this->db_conn->prepare('DELETE FROM plants WHERE id = :id');
    $deletePlant->execute(['id' => $plantId]);

    return $deletePlant->rowCount() > 0;
  }

  public function getPlantsByUserId($userId) {
    $getPlants = $this->db_conn->prepare('SELECT * FROM plants WHERE user_id = :user_id');
    $getPlants->execute(['user_id' => $userId]);

    return $getPlants->fetchAll();
  }
}
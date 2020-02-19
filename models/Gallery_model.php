<?php
class Gallery_model extends Model {
  
  const str_error_album_name_format = 'Le nom d\'un album doit commencer par une lettre et contenir uniquement des lettres, des chiffres et des espaces.';
  const str_error_photo_name_format = 'Le nom d\'une photo doit commencer par une lettre et contenir uniquement des lettres, des chiffres et des espaces.';
  const str_error_album_does_not_exist = 'L\'album n\'existe pas.';
  const str_error_photo_does_not_exist = 'La photo n\'existe pas.';
  const str_error_photo_format = 'La photo n\'a pas pu être sauvegardée.';
  const str_error_database = 'Problème avec la base de données.';
  
  public function albums() {
    try {
      $statement = $this->db->prepare("select * from albums");
      $statement->execute();
      return $statement->fetchAll();
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  public function create_album($album_name) {
    try {
      $this->check_album_name($album_name);
      $statement = $this->db->prepare("insert into albums(album_name) values (:album_name)"); 
      $statement->execute(['album_name' => $album_name]); 
      return $this->db->lastInsertId(); 
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  public function delete_album($album_id) {
    try {
      $statement = $this->db->prepare("delete from albums where album_id = :album_id");
      $statement->execute(['album_id' => $album_id]);
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  public function check_if_album_exists($album_id) {
    try {
      $statement = $this->db->prepare("select * from albums where album_id = :album_id");
      $statement->execute(['album_id' => $album_id]);
      $result = $statement->fetchAll();
      if (count($result) == 0) throw new Exception(self::str_error_album_does_not_exist);
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }

  public function album_name($album_id) {
    try {
      $statement = $this->db->prepare("select album_name from albums where album_id = :album_id");
      $statement->execute(['album_id' => $album_id]);
      $result = $statement->fetchAll();
      if (count($result) == 0) throw new Exception(self::str_error_album_does_not_exist);
      return $result[0]['album_name'];
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  public function photos($album_id) {
    try {
      $statement = $this->db->prepare("select photo_id, photo_name from photos where album_id = :album_id");
      $statement->execute(['album_id' => $album_id]);
      return $statement->fetchAll();
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  public function add_photo($album_id, $photo_name, $tmp_file) {
    try {
      $this->check_photo_name($photo_name);
      $statement = $this->db->prepare("insert into photos(album_id, photo_name, fullsize, thumbnail) 
                                              values (:album_id, :photo_name, :fullsize, :thumbnail)");
      $statement->execute(['album_id'=> $album_id, 
                           'photo_name'=>$photo_name,
                           'fullsize'=>$this->create_fullsize($tmp_file),
                           'thumbnail'=>$this->create_thumbnail($tmp_file)]);
      return $this->db->lastInsertId();
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    } catch ( ImagickException $e ) {
      throw new Exception ( self::str_error_photo_format );
    }
  }
  
  public function photo_name($photo_id) {
    try {
      $statement = $this->db->prepare("select photo_name from photos where photo_id = :photo_id");
      $statement->execute(['photo_id' => $photo_id]);
      $result = $statement->fetchAll();
      if (count($result) == 0) throw new Exception(self::str_error_photo_does_not_exist);
      return $result[0]['photo_name'];
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  public function delete_photo($photo_id) {
    try {
      $statement = $this->db->prepare("delete from photos where photo_id = :photo_id");
      $statement->execute(['photo_id' => $photo_id]);
    } catch (PDOException $e) {
      echo "lol";
      var_dump($e);
      exit;
      throw new Exception(self::str_error_database);
    }
  }

  public function fullsize($photo_id) {
    try {
      $statement = $this->db->prepare("select fullsize from photos where photo_id = :photo_id");
      $statement->execute(['photo_id' => $photo_id]);
      $result = $statement->fetchAll();
      if (count($result) == 0) throw new Exception(self::str_error_photo_does_not_exist);
      return $result[0]['fullsize'];
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }  
  }
  
  public function thumbnail($photo_id) {
    try {
      $statement = $this->db->prepare("select thumbnail from photos where photo_id = :photo_id");
      $statement->execute(['photo_id' => $photo_id]);
      $result = $statement->fetchAll();
      if (count($result) == 0) throw new Exception(self::str_error_photo_does_not_exist);
      return $result[0]['thumbnail'];
    } catch (PDOException $e) {
      throw new Exception(self::str_error_database);
    }
  }
  
  private function check_album_name($album_name) {
    $result = filter_var ( $album_name, FILTER_VALIDATE_REGEXP, array (
        'options' => array (
            'regexp' => '/^[a-zA-Z][0-9a-zA-Z ]*$/'
        )
    ) );
    if ($result === false || $result === null) {
      throw new Exception ( self::str_error_album_name_format );
    }
  }
  
  private function check_photo_name($photo_name) {
    $result = filter_var ( $photo_name, FILTER_VALIDATE_REGEXP, array (
        'options' => array (
            'regexp' => '/^[a-zA-Z][0-9a-zA-Z ]*$/'
        )
    ) );
    if ($result === false || $result === null) {
      throw new Exception ( self::str_error_photo_name_format );
    }
  }
  
  private function create_fullsize($tmp_file) {
    $image = new Imagick ( $tmp_file );
    try {
      $image->setImageFormat("jpeg");
      return $image->getimageBlob();
    } finally {
      $image->destroy ();
    }
  }
  
  private function create_thumbnail($tmp_file) {
    $image = new Imagick ( $tmp_file );
    try {
      $image->setImageFormat("jpeg");
      $this->resize_to_thumbnail ( $image );
      return $image->getimageBlob();
    } finally {
      $image->destroy ();
    }
  }
  
  private function resize_to_thumbnail($image) {
    $geometry = $image->getImageGeometry ();
    if ($geometry ['width'] > $geometry ['height']) {
      $image->thumbnailImage ( 150, 0 );
    } else {
      $image->thumbnailImage ( 0, 150 );
    }
  }
}
?>
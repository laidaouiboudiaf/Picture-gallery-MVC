<?php
class Gallery extends Controller {
  public function index() {
    $this->albums();
  }
  
  public function albums() {
    $this->loader->load('albums', ['title'=>'Albums', 'albums'=>$this->gallery->albums()]);
  }

  public function albums_new() {
    if ($this->redirect_unlogged_user()) return;
    $this->loader->load('albums_new', ['title'=>'Création d\'un albumf']);
  }
  
  public function albums_create() {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_name = filter_input(INPUT_POST, 'album_name');
      $this->gallery->create_album($album_name);
      header('Location: /index.php/gallery/albums'); /* redirection du client vers la liste des albums. */
    } catch (Exception $e) {
      $this->loader->load('albums_new', ['title'=>'Création d\'un album', 'error_message' => $e->getMessage()]);
    }
  }
  
  public function albums_delete($album_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_id = filter_var($album_id);
      $this->gallery->delete_album($album_id);
    } catch (Exception $e) { }
    header('Location: /index.php/gallery/albums');
  }

  public function albums_show($album_id) {
    try {
      $album_id = filter_var($album_id);
      $this->gallery->check_if_album_exists($album_id);
      $album_name = $this->gallery->album_name($album_id);
      $this->loader->load('albums_show', 
                          ['title'=>$album_name, 
                           'album'=>$album_id,
                           'photos'=>$this->gallery->photos($album_id)]);
    } catch (Exception $e) {
      header("Location: /index.php/gallery/albums");
    }
  }
  
  public function photos_new($album_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_id = filter_var($album_id);
      $this->gallery->check_if_album_exists($album_id);
      $album_name = $this->gallery->album_name($album_id);
      $this->loader->load('photos_new', 
                        ['title'=>"Ajout d'une photo dans l'album $album_name",
                        'album'=>$album_id,
                        'album_name'=>$this->gallery->album_name($album_id)]);
    }
    catch (Exception $e) { header("Location: /index.php/gallery/albums");}
  }

  public function photos_add($album_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_id = filter_var($album_id);
      $this->gallery->check_if_album_exists($album_id);
      $album_name = $this->gallery->album_name($album_id);
    } catch (Exception $e) { header("Location: /index.php");}
    try {
      $photo_name = filter_input(INPUT_POST, 'photo_name');
      if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {     
        throw new Exception('Vous devez choisir une photo.');
        }
      $this->gallery->add_photo($album_id, $photo_name, $_FILES['photo']['tmp_name']);
      header("Location: /index.php/gallery/albums_show/$album_id");
    } catch (Exception $e) {
      $this->loader->load('photos_new', ['album_name'=>$album_name, 'album'=>$album_id,
                          'title'=>"Ajout d'une photo dans l'album $album_name", 
                                 'error_message' => $e->getMessage()]);
    }
  }
  
  public function photos_delete($album_id, $photo_id) {
    if ($this->redirect_unlogged_user()) return;
    try {
      $album_id = filter_var($album_id);
      $photo_id = filter_var($photo_id);
      $this->gallery->delete_photo($photo_id);
      $this->gallery->check_if_album_exists($album_id);
      $this->albums_show($album_id);
    } catch (Exception $e) { header("Location: /index.php"); }
  }
  
  public function photos_show($album_id, $photo_id) {
    try {
      $album_id = filter_var($album_id);
      $photo_id = filter_var($photo_id);
      $this->gallery->check_if_album_exists($album_id);
      $album_name = $this->gallery->album_name($album_id);
      $photo_name = $this->gallery->photo_name($photo_id);
      $this->loader->load('photos_show', ['title'=>"$album_name / $photo_name",
          'album'=>$album_name,
          'photo'=>$this->gallery->photo($album_name, $photo_name)
      ]);
    } catch (Exception $e) {
      header("Location: /index.php");
    }
  }

  public function photos_get($photo_id) {
    try {
      $photo_id = filter_var($photo_id);
      if (isset($_GET['thumbnail'])) { $data = $this->gallery->thumbnail($photo_id); }
      else { $data =  $this->gallery->fullsize($photo_id); }
      header("Content-Type: image/jpeg"); // modification du header pour changer le format des données retourné au client
      echo $data;                          // écriture du binaire de l'image vers le client
    } catch (Exception $e) { header("Location: /index.php"); }
  }

  private function redirect_unlogged_user() {
    if (!$this->sessions->user_is_logged()) {
      header('Location: /index.php/sessions/sessions_new');
      return true;
    }
    return false;
  }

}


?>
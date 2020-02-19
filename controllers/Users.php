<?php
class Users extends Controller {

  public function index() {
    $this->loader->load();
  }
  
  public function users_new() {
    $this->loader->load('users_new', ['title'=>'S\'inscrire']);
  }
  
  public function users_create() {
    try {
      $username = filter_input(INPUT_POST, 'username');
      $password = filter_input(INPUT_POST, 'password');
      $user = $this->users->create_user($username, $password);
      $this->sessions->login($user);
      header("Location: /index.php");
    } catch (Exception $e) {
      $data = ['error' => $e->getMessage(), 'title'=>'S\'inscrire'];
      $this->loader->load('users_new', $data );
    }
  }
}
?>
<?php
class Sessions extends Controller {

  public function sessions_new() {
    $this->loader->load('sessions_new', ['title'=>'Se connecter']);
  }
  
  public function sessions_create() {
    try {
      $username = filter_input(INPUT_POST, 'username');
      $password = filter_input(INPUT_POST, 'password');
      $user = $this->users->user_from_username($username);
      if ($user===null) throw new Exception("Ce compte n'existe pas.");
      if (!$user->password_is_valid($password))
        throw new Exception("Mot de passe incorrect.");
      $this->sessions->login($user);
      header("Location: /index.php");
    } catch (Exception $e) {
      $data = ['error' => $e->getMessage(), 'title'=>'Se connecter'];
      $this->loader->load('sessions_new', $data );
    }
  }
  
  public function sessions_destroy() {
    $this->sessions->logout();
    header("Location: /index.php");
  }
}
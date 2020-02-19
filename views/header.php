<!DOCTYPE html>
<html lang="en">
 <head>
	<meta charset="utf-8">
	<title><?=$title ?></title>
	<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" />
	<script src="/assets/js/jquery.min.js" ></script>
 </head>
 <body>
 <nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><?=$title ?></a>
    </div>

    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav navbar-right">
        <?php if ($user_is_logged) { ?>
          <li>
            <a href="/index.php/sessions/sessions_destroy">Se déconnecter</a>
           </li>
           <li><button type="button" class="btn btn-default navbar-btn"><?=$logged_user->username ?></button></li>
        <?php } else  { ?>
          <li><a href="/index.php/sessions/sessions_new">Se connecter</a></li>
          <li><a href="/index.php/users/users_new">S'inscrire</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>


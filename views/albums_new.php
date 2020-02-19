<?php if (!empty($error_message)) { ?>
<div class="alert alert-warning" role="alert"><?= $error_message ?></div>
<?php } ?>
<form method="post" action="/index.php/gallery/albums_create">
  <div class="form-group">
    <label for="album_name">Nom de l'album</label>
    <input type="text" class="form-control" name="album_name" value="<?=set_value('album_name');?>" id="album_name" autofocus>
  </div>
  <button type="submit" class="btn btn-success">Créer mon album</button>
  <a href="/index.php" class="btn btn-danger">Annuler</a>
</form>
<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "03.01.2024"
 * Время создания = "10:13"
 **/
?>
<main role="main">
  <div class="container">
    <div class="d-flex justify-content-between">
      <h1 class="mt-1">Загрузка новых бюджетных операций</h1>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
        <li class="breadcrumb-item "><a href="<?=PATH;?>/budget">Бюджетные операции</a></li>
        <li class="breadcrumb-item active" aria-current="page">Загрузка данных</li>
      </ol>
    </nav>
    <?php unset($_SESSION['file']); ?>
    <form action="budget/upload" id="upload-file" class="was-validated" method="post" enctype="multipart/form-data">
      <div class="col-12 has-feedback">
        <div id="file" class="upload"></div>
      </div>
      <div class="form-group text-center">
        <button type="submit" class="btn btn-primary mt-3 mb-3">Загрузить</button>
      </div>
    </form>
    <?php if (isset($_SESSION['success'])) : ?>
      <div class="alert alert-success" role="alert">
        <?=$_SESSION['success'];unset($_SESSION['success']);?>
      </div>
    <?php endif;  ?>
    <?php if (isset($_SESSION['error'])) : ?>
      <div class="alert alert-danger" role="alert">
        <?=$_SESSION['error'];unset($_SESSION['error']);?>
      </div>
    <?php endif;  ?>
  </div>
</main>
<script type="text/javascript" src="assets/Dropzone/dropzone.min.js"></script>
<script>
  $(function () {
    let myDropzone = new Dropzone('div#file', {
      paramName: "file",
      url: "/budget/upload-file",
      maxFiles: 1,
      success: function (file, responce) {
        this.defaultOptions.success(file);
        console.log(responce);
      },
      init: function () {
        $(this.element).html(this.options.dictDefaultMessage);
      },
      processing: function () {
        $('.dz-message').remove();
      },
      dictDefaultMessage: '<div class="dz-message">Нажмите здесь или перетащите сюда файлы для загрузки</div>',
      dictMaxFilesExceeded: 'Достигнут лимит загрузки файлов - разрешено {{maxFiles}}',
    });
  });
</script>


<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "05.01.2024"
 * Время создания = "10:51"
 **/
?>
<main>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php if (isset($_SESSION['errors'])) : ?>
          <div class="alert alert-danger">
            <?php echo $_SESSION['errors']; unset($_SESSION['errors']); ?>
          </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])) : ?>
          <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <h1 class="mt-1">Добавление нового контрагента</h1>
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
         aria-label="breadcrumb" class="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
        <li class="breadcrumb-item"><a href="<?=PATH;?>/partner">Список контрагентов</a></li>
        <li class="breadcrumb-item active" aria-current="page">Добавление нового контрагента</li>
      </ol>
    </nav>
    <div class="row d-flex justify-content-center">
      <div class="col-9">
        <form method="post" action="partner/add" id="ka_add" class="row g-3 was-validated">
          <div class="col-7 has-feedback">
            <label for="name"><strong>Наименование контрагента</strong></label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?=isset($_SESSION['form_data']['name']) ? h($_SESSION['form_data']['name']) : '';?>" required>
          </div>
          <div class="col-5 has-feedback">
            <label for="type"><strong>Тип</strong></label>
            <select class="partner-type-select" name="type_id" id="type" data-placeholder="Выберите тип контрагента...">
              <option value="<?= null;?>"></option>
              <?php /** @var array $types */
              foreach ($types as $type) : ?>
                <option value="<?= $type['id'];?>"<?php if (isset($_SESSION['form_data']['type_id']) && $_SESSION['form_data']['type_id']==$type['id']) echo ' selected'?>><?= $type['name'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-4 has-feedback">
            <label for="alias"><strong>Код</strong></label>
            <input type="text" name="alias" class="form-control" id="alias" placeholder="Код" value="<?=isset($_SESSION['form_data']['alias']) ? h($_SESSION['form_data']['alias']) : '';?>" required>
          </div>
          <div class="col-4 has-feedback">
            <label for="inn"><strong>ИНН</strong></label>
            <input type="text" name="inn" class="form-control" id="inn" placeholder="ИНН" value="<?=isset($_SESSION['form_data']['inn']) ? h($_SESSION['form_data']['inn']) : '';?>" required>
          </div>
          <div class="col-4 has-feedback">
            <label for="kpp"><strong>КПП</strong></label>
            <input type="text" name="kpp" class="form-control" id="kpp" placeholder="КПП" value="<?=isset($_SESSION['form_data']['kpp']) ? h($_SESSION['form_data']['kpp']) : '';?>">
          </div>
          <div class="col-5 has-feedback">
            <label for="bank"><strong>Наименование обслуживающего банка</strong></label>
            <input type="text" name="bank" class="form-control" id="bank" placeholder="Наименование банка" value="<?=isset($_SESSION['form_data']['bank']) ? h($_SESSION['form_data']['bank']) : null;?>">
          </div>
          <div class="col-3 has-feedback">
            <label for="bic"><strong>БИК</strong></label>
            <input type="text" name="bic" class="form-control" id="bic" placeholder="БИК" value="<?=isset($_SESSION['form_data']['bic']) ? h($_SESSION['form_data']['bic']) : null;?>">
          </div>
          <div class="col-4 has-feedback">
            <label for="account"><strong>Номер расчетного счета</strong></label>
            <input type="text" name="account" class="form-control" id="account" placeholder="Номер расчетного счета" value="<?=isset($_SESSION['form_data']['account']) ? h($_SESSION['form_data']['account']) : null;?>">
          </div>
          <div class="col-12 has-feedback">
            <label for="address"><strong>Юридический адрес</strong></label>
            <input type="text" name="address" class="form-control" id="address" placeholder="Юридический адрес" value="<?=isset($_SESSION['form_data']['address']) ? h($_SESSION['form_data']['address']) : null;?>">
          </div>
          <div class="has-feedback col-3">
            <label for="phone"><strong>Телефоны</strong></label>
            <input type="text" name="phone" class="form-control" id="phone" placeholder="Телефоны" value="<?=isset($_SESSION['form_data']['phone']) ? h($_SESSION['form_data']['phone']) : null;?>">
          </div>
          <div class="has-feedback col-3">
            <label for="email"><strong>E-mail</strong></label>
            <input type="text" name="email" class="form-control" id="email" placeholder="E-mail" value="<?=isset($_SESSION['form_data']['email']) ? h($_SESSION['form_data']['email']) : null;?>">
          </div>
          <div class="has-feedback col-3">
            <label for="delay"><strong>Отсрочка</strong></label>
            <input type="number" name="delay" class="form-control" id="delay" placeholder="Отсрочка" value="<?=isset($_SESSION['form_data']['delay']) ? h($_SESSION['form_data']['delay']) : null;?>">
          </div>
          <div class="has-feedback col-3">
            <label for="vat"><strong>Ставка НДС</strong></label>
            <select class="vat-select" name="vat" id="vat" data-placeholder="Выберите ставку...">
              <option value="<?= null;?>"></option>
              <?php if (!empty($_SESSION['form_data']['vat'])) : ?>
                <?php if ($_SESSION['form_data']['vat'] == '1.20') : ?>
                  <option value="1.20" selected>20%</option>
                  <option value="1.00">Без НДС</option>
                <?php elseif ($_SESSION['form_data']['vat'] == '1.00') : ?>
                  <option value="1.20">20%</option>
                  <option value="1.00" selected>Без НДС</option>
                <?php endif; ?>
              <?php else : ?>
                <option value="1.20">20%</option>
                <option value="1.00">Без НДС</option>
              <?php endif; ?>
            </select>
          </div>
          <div class="col-12 d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-primary">Сохранить данные КА</button>
          </div>
        </form>
        <?php if(isset($_SESSION['form_data'])) unset($_SESSION['form_data']); ?>
      </div>
    </div>
  </div>
</main>
<script src="assets/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script>
  $(function () {
    $(".partner-type-select").chosen({
      disable_search_threshold: 10,
      width: "100%"
    }).change(function() {
      checkInputField(this);
    }).triggerHandler('change');
    $(".vat-select").chosen({
      disable_search_threshold: 10,
      width: "100%"
    }).change(function() {
      checkInputField(this);
    }).triggerHandler('change');

    function checkInputField(my_choices) {
      if ($(my_choices).val()) {
        $(my_choices).removeClass('is-invalid');
        $(my_choices).addClass('is-valid');
      } else {
        $(my_choices).addClass('is-invalid');
        $(my_choices).removeClass('is-valid');
      }
    }
  });
</script>

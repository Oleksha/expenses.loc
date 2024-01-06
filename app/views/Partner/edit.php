<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "05.01.2024"
 * Время создания = "11:04"
 **/
?>
<div class="col-7 has-feedback">
  <label for="name" class="form-label"><strong>Наименование контрагента</strong></label>
  <input type="text" class="form-control" id="name" name="name" placeholder="Наименование КА" value="<?=isset($_SESSION['form_data']['name']) ? h($_SESSION['form_data']['name']) : '';?>" required>
</div>
<div class="col-5 has-feedback">
  <label for="type" class="form-label"><strong>Тип</strong></label>
  <select class="partner-type-select" name="type_id" id="type"  data-placeholder="Выберите тип...">
    <option value="<?= null;?>"></option>
    <?php /** @var array $types */
    foreach ($types as $type) : ?>
      <option value="<?= $type['id'];?>"<?php if (isset($_SESSION['form_data']['type_id']) && $_SESSION['form_data']['type_id']==$type['id']) echo ' selected'?>><?= $type['name'];?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="col-4 has-feedback">
  <label for="alias" class="form-label"><strong>Код</strong></label>
  <input type="text" name="alias" class="form-control" id="alias" placeholder="Код" value="<?=isset($_SESSION['form_data']['alias']) ? h($_SESSION['form_data']['alias']) : '';?>" disabled>
</div>
<div class="col-4 has-feedback">
  <label for="inn" class="form-label"><strong>ИНН</strong></label>
  <input type="text" name="inn" class="form-control" id="inn" placeholder="ИНН" value="<?=isset($_SESSION['form_data']['inn']) ? h($_SESSION['form_data']['inn']) : '';?>" disabled>
</div>
<div class="col-4 has-feedback">
  <label for="kpp" class="form-label"><strong>КПП</strong></label>
  <input type="text" name="kpp" class="form-control" id="kpp" placeholder="КПП" value="<?=isset($_SESSION['form_data']['kpp']) ? h($_SESSION['form_data']['kpp']) : '';?>">
</div>

<div class="col-5 has-feedback">
  <label for="bank" class="form-label"><strong>Наименование банка</strong></label>
  <input type="text" name="bank" class="form-control" id="bank" placeholder="Наименование банка" value="<?=isset($_SESSION['form_data']['bank']) ? h($_SESSION['form_data']['bank']) : '';?>">
</div>
<div class="col-3 has-feedback">
  <label for="bic" class="form-label"><strong>БИК</strong></label>
  <input type="text" name="bic" class="form-control" id="bic" placeholder="БИК" value="<?=isset($_SESSION['form_data']['bic']) ? h($_SESSION['form_data']['bic']) : '';?>">
</div>
<div class="col-4 has-feedback">
  <label for="account" class="form-label"><strong>Номер расчетного счета</strong></label>
  <input type="text" name="account" class="form-control" id="account" placeholder="Номер расчетного счета" value="<?=isset($_SESSION['form_data']['account']) ? h($_SESSION['form_data']['account']) : '';?>">
</div>
<div class="col-12 has-feedback">
  <label for="address" class="form-label"><strong>Юридический адрес</strong></label>
  <input type="text" name="address" class="form-control" id="address" placeholder="Юридический адрес" value="<?=isset($_SESSION['form_data']['address']) ? h($_SESSION['form_data']['address']) : '';?>">
</div>
<div class="has-feedback col-md-3">
  <label for="phone" class="form-label"><strong>Телефоны</strong></label>
  <input type="text" name="phone" class="form-control" id="phone" placeholder="Телефоны" value="<?=isset($_SESSION['form_data']['phone']) ? h($_SESSION['form_data']['phone']) : '';?>">
</div>
<div class="has-feedback col-md-3">
  <label for="email" class="form-label"><strong>E-mail</strong></label>
  <input type="text" name="email" class="form-control" id="email" placeholder="E-mail" value="<?=isset($_SESSION['form_data']['email']) ? h($_SESSION['form_data']['email']) : '';?>">
</div>
<div class="has-feedback col-md-3">
  <label for="delay" class="form-label"><strong>Отсрочка</strong></label>
  <input type="text" name="delay" class="form-control" id="delay" placeholder="Отсрочка" value="<?=isset($_SESSION['form_data']['delay']) ? h($_SESSION['form_data']['delay']) : '';?>">
</div>
<div class="has-feedback col-md-3">
  <label for="vat" class="form-label"><strong>Ставка НДС</strong></label>
  <select class="vat-select" name="vat" id="vat" data-placeholder="Выберите ставку...">
    <option value="<?= null;?>"></option>
    <?php if (!empty($_SESSION['form_data']['vat'])) : ?>
      <option value="1.20" <?php if ($_SESSION['form_data']['vat'] == '1.20') echo 'selected' ?>>20%</option>
      <option value="1.00" <?php if ($_SESSION['form_data']['vat'] == '1.00') echo 'selected' ?>>Без НДС</option>
    <?php else : ?>
      <option value="<?= null; ?>">Выберите...</option>
      <option value="1.20">20%</option>
      <option value="1.00">Без НДС</option>
    <?php endif; ?>
  </select>
</div>
<input type="hidden" name="alias" value="<?=isset($_SESSION['form_data']['alias']) ? h($_SESSION['form_data']['alias']) : '';?>">
<input type="hidden" name="inn" value="<?=isset($_SESSION['form_data']['inn']) ? h($_SESSION['form_data']['inn']) : '';?>">
<input type="hidden" name="partner_id" value="<?=isset($_SESSION['form_data']['id']) ? h($_SESSION['form_data']['id']) : '';?>">
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

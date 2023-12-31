<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "02.01.2024"
 * Время создания = "11:18"
 **/
?>
<input type="hidden" name="id" value="<?= /** @var array $budget содержит все данные о приходе */ $budget['id'];?>">
<?php
$date = date_create($budget['scenario']);
$_monthsList = array(
  "1"=>"Январь","2"=>"Февраль","3"=>"Март",
  "4"=>"Апрель","5"=>"Май", "6"=>"Июнь",
  "7"=>"Июль","8"=>"Август","9"=>"Сентябрь",
  "10"=>"Октябрь","11"=>"Ноябрь","12"=>"Декабрь");

$scenario = $_monthsList[date_format($date, "n")].'&nbsp;'.date_format($date, "Y");
?>
<div class="col-12 has-feedback">
  <label for="scenario"><strong>Сценарий</strong></label>
  <input type="text" name="scenario" class="form-control text-center" id="scenario" value="<?=$scenario;?>" disabled>
</div>
<div class="has-feedback col-md-6">
  <label for="month_exp"><strong>Месяц расхода</strong></label>
  <input type="date" name="month_exp" class="form-control" id="month_exp" placeholder="01.01.2021" value="<?=$budget['month_exp'];?>" required>
</div>
<div class="has-feedback col-md-6">
  <label for="month_pay"><strong>Месяц оплаты</strong></label>
  <input type="date" name="month_pay" class="form-control" id="month_pay" placeholder="Номер" value="<?=$budget['month_pay'];?>" required>
</div>
<div class="has-feedback col-md-6">
  <label for="summa"><strong>Сумма</strong></label>
  <input type="number" name="summa" class="form-control" id="summa"  placeholder="" step="0.01" value="<?=$budget['summa'];?>" required>
</div>
<div class="has-feedback col-md-6">
  <label for="vat"><strong>НДС</strong></label>
  <select class="form-control" name="vat" id="vat">
    <option value="1.20" <?php if ($budget['vat'] == '1.20') { echo ' selected';} ?>>20%</option>
    <option value="1.00" <?php if ($budget['vat'] == '1.00') { echo ' selected';} ?>>Без НДС</option>
  </select>
</div>
<div class="col-12 has-feedback">
  <label for="budget_item"><strong>Статья расхода</strong></label>
  <select class="budget_item_select" name="budget_item_id" id="budget_item" data-placeholder="Выберите статью расхода...">
    <option value="<?= null;?>"></option>
    <?php /** @var array $budget_items статьи расхода*/
    foreach ($budget_items as $item) : ?>
      <option value="<?= (int)$item['id']; ?>" <?php if ($budget['name_budget_item'] == $item['name_budget_item']) { echo ' selected';} ?>><?= $item['name_budget_item']; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="col-12 has-feedback">
  <label for="description"><strong>Комментарий</strong></label>
  <input type="text" name="description" class="form-control" id="description" value="<?=$budget['description'];?>">
</div>
<input type="hidden" name="scenario" value="<?=$budget['scenario'];?>">
<input type="hidden" name="number" value="<?=$budget['number'];?>">
<input type="hidden" name="status" value="<?=$budget['status'];?>">
<script src="assets/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script>
  $(function () {
    $(".budget_item_select").chosen({
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
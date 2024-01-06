<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "06.01.2024"
 * Время создания = "10:33"
 **/
?>
<div class="col-12 has-feedback">
  <label for="name" class="form-label"><strong>Наименование контрагента</strong></label>
  <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?= /** @var array $partner  Наименование КА */ $partner['name'];?>" disabled>
</div>
<div class="col-12 has-feedback">
  <label for="type" class="form-label"><strong>Тип документа для оплаты</strong></label>
  <select class="receipt_type-select" name="type" id="type" data-placeholder="Выберите тип поступления..." required>
    <option value="<?= null; ?>"></option>
    <option value="ПТ">Поступление товаров и услуг</option>
    <option value="ЗП">Заказ поставщику</option>
    <option value="АО">Авансовый отчет</option>
  </select>
</div>
<div class="col-6 has-feedback">
  <label for="date" class="form-label"><strong>Дата прихода</strong></label>
  <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021" value="<?=date("Y-m-d");?>" required>
</div>
<div class="col-6 has-feedback">
  <label for="number" class="form-label"><strong>Номер прихода</strong></label>
  <input type="text" name="number" class="form-control" id="number" placeholder="Номер" value="<?=null;?>" required>
</div>
<div class="col-6 has-feedback">
  <label for="sum" class="form-label"><strong>Сумма прихода</strong></label>
  <input type="number" name="sum" class="form-control" id="sum"  placeholder="" step="0.01" value="<?=null;?>" required>
</div>
<div class="col-6 has-feedback">
  <label for="vat" class="form-label"><strong>Ставка НДС</strong></label>
  <select class="vat-select" name="vat_id" id="vat" data-placeholder="Выберите ставку...">
    <option value="<?= null; ?>"></option>
    <?php /** @var array $vats */
    foreach ($vats as $vat) : ?>
      <option value="<?= $vat['id'];?>"<?php if ($partner['vat'] == $vat['vat']) { echo " selected";} ?>><?= $vat['name'];?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="col-6 has-feedback">
  <label for="num_doc" class="form-label"><strong>Номер вх.документа</strong></label>
  <input type="text" name="num_doc" class="form-control" id="num_doc" placeholder="Номер документа" value="<?=null;?>" required>
</div>
<div class="col-6 has-feedback">
  <label for="date_doc" class="form-label"><strong>Дата вх.документа</strong></label>
  <input type="date" name="date_doc" class="form-control" id="date_doc" placeholder="" value="<?=null;?>" required>
</div>
<div class="col-12 has-feedback">
  <label for="note" class="form-label"><strong>Комментарий</strong></label>
  <input type="text" name="note" class="form-control" id="note" placeholder="Комментарий" value="<?=null;?>">
</div>
<input type="hidden" name="id_partner" value="<?=$partner['id'];?>">
<script src="assets/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script>
  $(function () {
    $(".receipt_type-select").chosen({
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

    $(document).on('click', '.submit_data', function() {
      checkInputField($(".receipt_type-select"));
    });

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
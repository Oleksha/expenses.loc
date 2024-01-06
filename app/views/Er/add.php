<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "05.01.2024"
 * Время создания = "12:22"
 **/

if (!empty($partner)) : ?>
  <input type="hidden" name="id_partner" value="<?= isset($partner['id']) ? h($partner['id']) : ''; ?>">
  <div class="col-12 has-feedback">
    <label for="name" class="form-label"><strong>Наименование контрагента</strong></label>
    <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА"
           value="<?= isset($partner['name']) ? h($partner['name']) : ''; ?>" disabled>
  </div>
  <div class="col-12 has-feedback">
    <label for="number" class="form-label"><strong>Номер</strong></label>
    <input type="text" name="number" class="form-control" id="number" placeholder="Номер ЕР" required>
  </div>
  <div class="col-12 has-feedback">
    <label for="id_budget_item" class="form-label"><strong>Статья расхода</strong></label>
    <select name="id_budget_item" class="budget-item-select" id="id_budget_item"
            data-placeholder="Выберите статью расхода..." required>
      <option value="<?= null; ?>"></option>
      <?php /** @var array $budget */
      foreach ($budget as $item) : ?>
        <option value="<?= $item['id']; ?>"><?= $item['name_budget_item']; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group has-feedback col-md-6">
    <label for="data_start" class="form-label"><strong>Дата начала действия</strong></label>
    <input type="date" name="data_start" class="form-control" id="data_start" required>
  </div>
  <div class="form-group has-feedback col-md-6">
    <label for="data_end" class="form-label"><strong>Дата окончания действия</strong></label>
    <input type="date" name="data_end" class="form-control" id="data_end" required>
  </div>
  <div class="form-group has-feedback col-md-6">
    <label for="delay" class="form-label"><strong>Отсрочка платежа в календарных днях</strong></label>
    <input type="number" name="delay" class="form-control" id="delay" required>
  </div>
  <div class="form-group has-feedback col-md-6">
    <label for="summa" class="form-label"><strong>Сумма единоличного решения</strong></label>
    <div class="input-group">
      <div class="input-group-prepend">
        <div class="input-group-text">₽</div>
      </div>
      <input type="number" name="summa" class="form-control" id="summa" placeholder="" step="0.01" required>
    </div>
  </div>
<?php endif; ?>
<script src="assets/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script>
  $(function () {
    $(".budget-item-select").chosen({
      disable_search_threshold: 10,
      width: "100%"
    }).change(function () {
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
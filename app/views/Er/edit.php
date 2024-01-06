<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "05.01.2024"
 * Время создания = "12:38"
 **/

if (!empty($er)) : ?>
  <input type="hidden" name="id" value="<?= isset($er['id']) ? h($er['id']) : ''; ?>">
  <input type="hidden" name="id_partner" value="<?= isset($er['id_partner']) ? h($er['id_partner']) : ''; ?>">
  <div class="col-12 has-feedback">
    <label for="name" class="form-label"><strong>Наименование контрагента</strong></label>
    <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА"
           value="<?= isset($partner['name']) ? h($partner['name']) : ''; ?>" disabled>
  </div>
  <div class="col-12 has-feedback">
    <label for="number" class="form-label"><strong>Номер</strong></label>
    <input type="text" name="number" class="form-control" id="number" placeholder="Номер ЕР"
           value="<?= isset($er['number']) ? h($er['number']) : ''; ?>" required>
  </div>
  <div class="col-12 has-feedback">
    <label for="id_budget_item" class="form-label"><strong>Статья расхода</strong></label>
    <select name="id_budget_item" class="budget-item-select" id="id_budget_item" required>
      <?php /** @var array $budget */
      foreach ($budget as $item) : ?>
        <option value="<?= $item['id']; ?>"
          <?php if ($item['id'] == $er['id_budget_item']) : ?>
            selected
          <?php endif; ?>
        ><?= $item['name_budget_item']; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-6 has-feedback">
    <label for="data_start" class="form-label"><strong>Дата начала действия</strong></label>
    <input type="date" name="data_start" class="form-control" id="data_start" placeholder=""
           value="<?= isset($er['data_start']) ? h($er['data_start']) : ''; ?>" required>
  </div>
  <div class="col-6 has-feedback">
    <label for="data_end" class="form-label"><strong>Дата окончания действия</strong></label>
    <input type="date" name="data_end" class="form-control" id="data_end" placeholder=""
           value="<?= isset($er['data_end']) ? h($er['data_end']) : ''; ?>" required>
  </div>
  <div class="col-6 has-feedback">
    <label for="delay" class="form-label"><strong>Отсрочка платежа в календарных днях</strong></label>
    <input type="number" name="delay" class="form-control" id="delay" placeholder=""
           value="<?= isset($er['delay']) ? h($er['delay']) : ''; ?>" required>
  </div>
  <div class="col-6 has-feedback">
    <label for="summa" class="form-label"><strong>Сумма единоличного решения</strong></label>
    <div class="input-group">
      <div class="input-group-prepend">
        <div class="input-group-text">₽</div>
      </div>
      <input type="number" name="summa" class="form-control" id="summa" placeholder="" step="0.01"
             value="<?= isset($er['summa']) ? h($er['summa']) : ''; ?>" required>
    </div>
  </div>
<?php else : ?>
  <div class="col-12">
    <h3>Что-то пошло не так. Не получены данные.</h3>
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
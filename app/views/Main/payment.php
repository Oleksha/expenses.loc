<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "31.12.2023"
 * Время создания = "19:37"
 **/
?>
<div class="col-12 has-feedback">
  <label for="date"><strong>Дата фактической оплаты</strong></label>
  <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021" value="<?=date("Y-m-d");?>" required>
</div>
<input type="hidden" name="id" value="<?= /** @var int $id */ $id;?>">
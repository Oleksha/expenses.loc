<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "19:36"
 **/

namespace expenses\base;

/**
 * Класс отвечающий за работу с данными
 */
abstract class Model
{

  /**
   * Массив свойств модели (идентичен полям базы данных)
   * @var array
   */
  public $attributes = [];

  /**
   * Массив возникших ошибок
   * @var array
   */
  public $errors = [];

  /**
   * Массив правил проверки данных
   * @var array
   */
  public $rules = [];

  /**
   * Конструктор класса
   */
  public function __construct()
  {
    /**
     * подключение к базе данных
     */
    Db::instance();
  }

  /**
   * Автоматическая загрузка данных из форм ввода
   * @param $data array полученный набор данных
   */
  public function load($data)
  {
    /**
     * Обрабатываем все поля БД
     */
    foreach ($this->attributes as $name => $value) {
      /**
       * Если в полученных данных $data есть наименование поля таблицы
       */
      if (isset($data[$name])) {
        /**
         * Запоминаем для этого поля его значение
         */
        $this->attributes[$name] = $data[$name];
      }
    }
  }

  /**
   * Сохраняет данные в БД
   * @param $table string Имя таблицы в которой будут сохранены данные
   * @return int 0 если произошла ошибка, и ID новой записи если все хорошо
   */
  public function save(string $table)
  {
    /**
     * Подключаем таблицу БД с полученным именем - table
     */
    $tbl = \R::dispense($table);
    /**
     * Обрабатываем все имеющиеся атрибуты (поля БД)
     */
    foreach ($this->attributes as $name => $value) {
      /**
       * Вносим в соответсвующее поле таблицы сохраняемое значение
       */
      $tbl->$name = $value;
    }
    return \R::store($tbl);
  }

  /**
   * Редактирование данных в БД
   * @param $table string Наименование таблицы в БД
   * @param $id int Идентификатор редактируемой записи
   * @return int 0 если произошла ошибка, и ID редактируемой записи если все хорошо
   */
  public function edit($table, $id)
  {
    /**
     * Подключаем таблицу БД с полученным именем - table
     */
    $tbl = \R::load($table, $id);
    /**
     * Обрабатываем все имеющиеся атрибуты (поля БД)
     */
    foreach ($this->attributes as $name => $value) {
      /**
       * Вносим в соответсвующее поле таблицы новое значение
       */
      $tbl->$name = $value;
    }
    return \R::store($tbl);
  }

  /**
   * Проверка корректности переданных данных согласно установленным правилам
   * @param $data array полученный набор данных
   * @return boolean TRUE если проверка пройдена, и FALSE если нет
   */
  public function validate($data) {
    /** Русифицируем сообщения валидатора */
    Validator::lang('ru');
    /** создаем объект установленного vlucas/valitron */
    $v = new Validator($data);
    /** передаем ему массив установленных нами правил */
    $v->rules($this->rules);
    if ($v->validate()) {
      /** если проверка пройдена */
      return true;
    }
    /** запоминаем ошибки */
    $this->errors = $v->errors();
    /** проверка не пройдена */
    return false;
  }

  /**
   * Формирует HTML-код вывода ошибок проверки заполненных полей формы
   * @return void
   */
  public function getErrors()
  {
    $errors = '<ul style="margin-bottom:  0;">';
    foreach ($this->errors as $error) {
      /** проходимся в цикле по всем полям для заполнения и получаем массив ошибок в каждом */
      foreach ($error as $item) {
        /** выводим каждую ошибку для каждого поля формы */
        $errors .= "<li>$item</li>";
      }
    }
    $errors .= '</ul>';
    /** записываем оформленный HTML-код в сессию */
    $_SESSION['errors'] = $errors;
  }

}
<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "26.12.2023"
 * Время создания = "10:40"
 **/

namespace expenses;

class Registry
{

  use TSingletone;

  /**
   * Содержит все свойства
   * @var array
   */
  protected static $properties = [];

  /**
   * Добавляет в массив свойство с ключем и значением
   * @param string $name ключ
   * @param mixed $value значение
   */
  public function setProperty($name, $value) {
    self::$properties[$name] = $value;
  }

  /**
   * Получает значение свойства по ключу
   * @param string $name ключ
   * @return mixed|null
   */
  public function getProperty($name) {
    if (isset(self::$properties[$name])) {
      // если свойство существует возвращаем его
      return self::$properties[$name];
    }
    return null; // иначе ничего не возвращаем
  }

  /**
   * Возвращает массив со свойствами
   * @return array
   */
  public function getProperties() {
    return self::$properties;
  }

}
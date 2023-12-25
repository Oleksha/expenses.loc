<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "25.12.2023"
 * Время создания = "22:32"
 **/

define("ROOT", dirname(__DIR__));
const DEBUG = 1; // 1 - режим Разработки, 0 - режим Релиза
const WWW = ROOT . '/public';
const APP = ROOT . '/app';
const CORE = ROOT . '/vendor/expenses/core';
const LIBS = ROOT . '/vendor/expenses/core/libs';
const CACHE = ROOT . '/tmp/cache';
const CONF = ROOT . '/config';
const LAYOUT = 'expenses';

// http://expenses.loc/public/index.php
$app_path = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
// http://expenses.loc/public/
$app_path = preg_replace("#[^/]+$#", '', $app_path);
// http://expenses.loc
$app_path = str_replace('/public/', '', $app_path);

define("PATH", $app_path);
const ADMIN = PATH . '/admin';

require_once ROOT . '/vendor/autoload.php';
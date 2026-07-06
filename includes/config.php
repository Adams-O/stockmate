<?php
declare(strict_types=1);

const APP_NAME = 'StockMate POS';
const DB_HOST = '127.0.0.1';
const DB_NAME = 'stockmate';
const DB_USER = 'root';
const DB_PASS = '';

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = preg_replace('#/(includes|products|categories|sales|reports)$#', '', rtrim($scriptDir, '/'));

define('BASE_URL', $basePath === '/' ? '' : $basePath);

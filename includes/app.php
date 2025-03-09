<?php

require 'funciones.php';
require 'database.php';
require __DIR__ . '/../vendor/autoload.php';

// ─── Conexión A La DB ────────────────────────────────────────────────────────
use Model\ActiveRecord;
ActiveRecord::setDB($db);
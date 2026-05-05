<?php
$hashAdmin = '$2y$10$yacFOEtDrS0FpDg1xnlr8.VgUTmz5I2LUcInuFTcARUbZ7GDwQLWG';
$hashPharm = '$2y$10$7vCQxt5DNBM4XLedYy.sWOljx3gpYJj7ffv979GVkVE/1xFYyL0vy';
var_dump(password_verify('Admin123!', $hashAdmin));
var_dump(password_verify('Pharmacien123!', $hashPharm));

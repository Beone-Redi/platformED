<?php

// Inicio la variables de sesion.
if (!isset($_SESSION)) 
{
    session_start();
}

$_SESSION = [];
session_destroy();

if (isset($_SESSION['ACTIVO']) <> "2019") 
{
    header("Location: login");
}

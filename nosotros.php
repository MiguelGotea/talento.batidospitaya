<?php
// nosotros.php - Redireccionador 301 a index.php (Sobre Nosotros)
// Esta página fue renombrada a index.php para que sea la página de inicio por defecto.
// Se mantiene este archivo para preservar URLs existentes (SEO, enlaces externos, etc.)
header("HTTP/1.1 301 Moved Permanently");
header("Location: index.php");
exit();

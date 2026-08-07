<?php
// Configuración de las credenciales de la base de datos
$host = "localhost"; // [cite: 593]
$db_name = "sistema_inventario"; // 
$username = "root"; // [cite: 595]
$password = ""; // Vacío por defecto en XAMPP [cite: 596]

// Habilitar el reporte de errores de mysqli para usar excepciones (try...catch) [cite: 597]
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // [cite: 597]

try {
    // 1. Instanciar el objeto mysqli (Esto inicia la conexión) [cite: 601]
    $conn = new mysqli($host, $username, $password, $db_name); // [cite: 602]
    
    // 2. Configurar el juego de caracteres a UTF-8 para admitir tildes y eñes [cite: 603]
    $conn->set_charset("utf8"); // [cite: 604]

} catch (mysqli_sql_exception $e) {
    // Captura el error y detiene el script con un mensaje controlado de seguridad [cite: 608]
    die("Error crítico: No se pudo establecer la conexión segura con el servidor de datos."); // [cite: 609]
}
?>
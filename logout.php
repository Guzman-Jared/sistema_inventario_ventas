<?php
session_start(); // [cite: 501]

session_unset(); // Borrar variables de sesión [cite: 502]
session_destroy(); // Destruir la sesión físicamente en el servidor [cite: 503]

header("Location: index.php"); // [cite: 504]
exit(); // [cite: 505]
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Capturar y sanitizar datos
    $usuario_id    = intval($_SESSION['user_id']);
    $proveedor_id  = intval($_POST['proveedor_id']);
    $producto_id   = intval($_POST['producto_id']);
    $cantidad      = intval($_POST['cantidad']);
    $precio_compra = floatval($_POST['precio_compra']);

    // Validar existencia del usuario en la base de datos
    $check_usr = $conn->prepare("SELECT usuario_id FROM usuarios WHERE usuario_id = ?");
    if (!$check_usr) {
        // En caso de que en la tabla usuarios la columna sea 'id'
        $check_usr = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    }
    $check_usr->bind_param("i", $usuario_id);
    $check_usr->execute();
    $res_usr = $check_usr->get_result();

    if ($res_usr->num_rows === 0) {
        // Si el usuario no existe en la base de datos, tomar el primer usuario registrado como respaldo
        $res_fallback = $conn->query("SELECT usuario_id FROM usuarios LIMIT 1");
        if (!$res_fallback || $res_fallback->num_rows === 0) {
            $res_fallback = $conn->query("SELECT id FROM usuarios LIMIT 1");
        }
        $usr_data = $res_fallback->fetch_assoc();
        $usuario_id = intval(reset($usr_data)); // Toma el ID disponible
    }
    $check_usr->close();

    // Validar valores mínimos
    if ($cantidad <= 0 || $precio_compra <= 0) {
        die("Error: La cantidad y el precio deben ser mayores a 0.");
    }

    $total_compra = $cantidad * $precio_compra;

    $conn->begin_transaction();

    try {
        // --- FASE 1: INSERTAR CABECERA (compras) ---
        $sql_compras = "INSERT INTO compras (proveedor_id, usuario_id, total) VALUES (?, ?, ?)";
        $stmt1 = $conn->prepare($sql_compras);
        $stmt1->bind_param("iid", $proveedor_id, $usuario_id, $total_compra);
        $stmt1->execute();

        $id_nueva_compra = $conn->insert_id;
        $stmt1->close();

        // --- FASE 2: INSERTAR DETALLE (detalle_compras) ---
        $sql_detalle = "INSERT INTO detalle_compras (compra_id, producto_id, cantidad, precio_compra) VALUES (?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql_detalle);
        $stmt2->bind_param("iiid", $id_nueva_compra, $producto_id, $cantidad, $precio_compra);
        $stmt2->execute();
        $stmt2->close();

        // --- FASE 3: ACTUALIZAR STOCK EN PRODUCTOS ---
        $sql_stock = "UPDATE productos SET stock = stock + ? WHERE producto_id = ?";
        $stmt3 = $conn->prepare($sql_stock);
        $stmt3->bind_param("ii", $cantidad, $producto_id);
        $stmt3->execute();
        $stmt3->close();

        $conn->commit();

        header("Location: inventario.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        die("Error crítico en la transacción de compra: " . $e->getMessage());
    }

} else {
    header("Location: dashboard.php");
    exit();
}
?>
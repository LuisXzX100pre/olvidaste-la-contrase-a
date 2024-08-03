<?php
include 'conexion.php';

function generarToken($longitud = 32) {
    return bin2hex(random_bytes($longitud));
}

if (isset($_POST['correo'])) {
    $correo = $_POST['correo'];

    $sql = "SELECT id_usuario FROM usuarios WHERE Correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id_usuario);
        $stmt->fetch();

        $token = generarToken();
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "INSERT INTO tokens_restablecimiento (token, id_usuario, fecha_expiracion) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sis', $token, $id_usuario, $fecha_expiracion);
        $stmt->execute();

        $enlace = "http://tu-dominio.com/restablecer_contraseña.php?token=" . $token;
        $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña: " . $enlace;
        mail($correo, "Restablecimiento de Contraseña", $mensaje);
        
        echo "Se ha enviado un enlace para restablecer tu contraseña.";
    } else {
        echo "El correo no está registrado.";
    }
}
?>

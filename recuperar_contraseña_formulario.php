<?php
include 'conexion.php';

// Verificar si se ha proporcionado un token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Preparar la consulta para verificar el token
    $consulta = "SELECT id_usuario FROM tokens_restablecimiento WHERE token = ? AND fecha_expiracion > NOW()";
    if ($stmt = $conexion->prepare($consulta)) {
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // El token es válido, mostrar el formulario de restablecimiento de contraseña
            if (isset($_POST['restablecer'])) {
                $nueva_contraseña = $_POST['nueva_contraseña'];
                $id_usuario = null;

                $stmt->bind_result($id_usuario);
                $stmt->fetch();

                // Actualizar la contraseña
                $consulta_update = "UPDATE usuarios SET Contraseña = ? WHERE id_usuario = ?";
                if ($stmt_update = $conexion->prepare($consulta_update)) {
                    $stmt_update->bind_param('si', $nueva_contraseña, $id_usuario);
                    $stmt_update->execute();

                    if ($stmt_update->affected_rows > 0) {
                        echo 'Contraseña restablecida exitosamente.';
                    } else {
                        echo 'Error al restablecer la contraseña.';
                    }
                    
                    $stmt_update->close();
                } else {
                    echo 'Error al preparar la consulta para actualizar la contraseña.';
                }
                
                // Eliminar el token después de usarlo
                $consulta_delete = "DELETE FROM tokens_restablecimiento WHERE token = ?";
                if ($stmt_delete = $conexion->prepare($consulta_delete)) {
                    $stmt_delete->bind_param('s', $token);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                } else {
                    echo 'Error al preparar la consulta para eliminar el token.';
                }
            }
        } else {
            echo 'Token inválido o expirado.';
        }

        $stmt->close();
    } else {
        echo 'Error al preparar la consulta para verificar el token.';
    }

    $conexion->close();
} else {
    echo 'Token no proporcionado.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Recuperar Contraseña</h2>
        <form action="" method="POST">
            <label for="nueva_contraseña">Nueva contraseña:</label>
            <input type="password" id="nueva_contraseña" name="nueva_contraseña" required>
            <button type="submit" name="restablecer">Restablecer Contraseña</button>
        </form>
    </div>
</body>
</html>

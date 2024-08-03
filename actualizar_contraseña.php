<?php
include 'conexion.php';

if (isset($_POST['token']) && isset($_POST['nueva_contraseña'])) {
    $token = $_POST['token'];
    $nueva_contraseña = password_hash($_POST['nueva_contraseña'], PASSWORD_DEFAULT);

    $sql = "SELECT id_usuario FROM tokens_restablecimiento WHERE token = ? AND fecha_expiracion > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id_usuario);
        $stmt->fetch();

        $sql = "UPDATE usuarios SET Contraseña = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $nueva_contraseña, $id_usuario);
        $stmt->execute();

        $sql = "DELETE FROM tokens_restablecimiento WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();

        echo "Contraseña restablecida exitosamente.";
    } else {
        echo "El enlace de restablecimiento es inválido o ha expirado.";
    }
}
?>

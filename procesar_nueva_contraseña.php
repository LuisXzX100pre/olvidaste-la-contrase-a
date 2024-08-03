<?php
session_start();
include('conexion.php');

if (isset($_POST['token']) && isset($_POST['nueva_contraseña'])) {
    $token = trim($_POST['token']);
    $nueva_contraseña = trim($_POST['nueva_contraseña']);

    if (empty($token) || empty($nueva_contraseña)) {
        header("Location: nueva_contraseña.php?error=Todos los campos son requeridos");
        exit();
    } else {
        // Verificar el token
        $sql = "SELECT * FROM usuarios WHERE token = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $sql = "UPDATE usuarios SET Contraseña = ?, token = NULL WHERE token = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $nueva_contraseña, $token);
            mysqli_stmt_execute($stmt);

            header("Location: index.php?message=La contraseña se ha actualizado exitosamente");
            exit();
        } else {
            header("Location: nueva_contraseña.php?error=Token inválido");
            exit();
        }
    }
} else {
    header("Location: nueva_contraseña.php");
    exit();
}
?>

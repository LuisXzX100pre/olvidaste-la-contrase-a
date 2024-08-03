<?php
session_start();
include('conexion.php');

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        header("Location: restablecer_contraseña.php?error=El correo es requerido");
        exit();
    } else {
        // Verificar si el correo existe en la base de datos
        $sql = "SELECT * FROM usuarios WHERE Correo = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            // Generar un token de restablecimiento
            $token = bin2hex(random_bytes(50));
            $sql = "UPDATE usuarios SET token = ? WHERE Correo = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $token, $email);
            mysqli_stmt_execute($stmt);

            // Enviar el correo con el enlace de restablecimiento
            $resetLink = "http://tusitio.com/restablecer_contraseña.php?token=" . $token;
            $subject = "Restablecimiento de Contraseña";
            $message = "Para restablecer su contraseña, haga clic en el siguiente enlace: " . $resetLink;
            mail($email, $subject, $message);

            header("Location: index.php?message=Se ha enviado un enlace de restablecimiento a su correo");
            exit();
        } else {
            header("Location: restablecer_contraseña.php?error=El correo no está registrado");
            exit();
        }
    }
} else {
    header("Location: restablecer_contraseña.php");
    exit();
}
?>

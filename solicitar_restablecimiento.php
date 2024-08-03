<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Restablecimiento de Contraseña</title>
</head>
<body>
    <h2>Solicitar Restablecimiento de Contraseña</h2>
    <form action="enviar_enlace.php" method="post">
        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" required>
        <input type="submit" value="Enviar Enlace de Restablecimiento">
    </form>
</body>
</html>

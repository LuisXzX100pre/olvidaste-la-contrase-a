<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer Nueva Contraseña - Magus</title>
    <link rel="stylesheet" href="styles_nueva_contraseña.css">
</head>
<body>
    <main>
        <div class="container">
            <h1>Establecer Nueva Contraseña</h1>
            <form action="procesar_nueva_contraseña.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <input type="password" name="nueva_contraseña" placeholder="Nueva contraseña" required>
                <input type="submit" value="Actualizar Contraseña">
            </form>
        </div>
    </main>
</body>
</html>

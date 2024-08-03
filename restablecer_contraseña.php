<?php
require 'vendor/autoload.php';

use \Mailjet\Resources;

// Configuración de Mailjet
$apiKey = '35047eeec58814a7f62c87e09955d0cf'; // Reemplaza con tu API Key
$apiSecret = '566df2d215aaa92f1d826a2c6222d25d'; // Reemplaza con tu API Secret
$mailjet = new \Mailjet\Client($apiKey, $apiSecret, true, ['version' => 'v3.1']);

// Conexión a la base de datos
include 'conexion.php';

// Función para generar un token aleatorio
function generarToken($longitud = 50) {
    return bin2hex(random_bytes($longitud));
}

// Verifica si se ha enviado el formulario
if (isset($_POST['enviar'])) {
    $correo = $_POST['correo'];

    // Verifica si el correo está vacío
    if (empty($correo)) {
        echo "Por favor, ingrese su correo electrónico.";
        exit;
    }

    // Verifica si el correo existe en la base de datos
    $sql = "SELECT id_usuario FROM usuarios WHERE Correo = ?";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        
        // Verifica si el correo existe
        if ($stmt->num_rows > 0) {
            // Genera un token
            $token = generarToken();
            $id_usuario = null;
            
            $stmt->bind_result($id_usuario);
            $stmt->fetch();
            
            // Inserta el token en la base de datos
            $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $sql = "INSERT INTO tokens_restablecimiento (token, id_usuario, fecha_expiracion) VALUES (?, ?, ?)";
            if ($stmt = $conexion->prepare($sql)) {
                $stmt->bind_param("sis", $token, $id_usuario, $fecha_expiracion);
                $stmt->execute();
            } else {
                echo "Error al preparar la consulta.";
                exit;
            }
            
            // Envía el correo electrónico utilizando Mailjet
            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => "maguslugumu@gmail.com",
                            'Name' => "Magus"
                        ],
                        'To' => [
                            [
                                'Email' => $correo,
                                'Name' => "User"
                            ]
                        ],
                        'Subject' => "Restablecimiento de contraseña",
                        'HTMLPart' => "<h3>Para restablecer su contraseña, haga clic en el siguiente enlace:</h3><p><a href='http://localhost/MAGUS/recuperar_contraseña_formulario.php?token=" . urlencode($token) . "'>Restablecer Contraseña</a></p>"
                    ]
                ]
            ];

            $response = $mailjet->post(Resources::$Email, ['body' => $body]);

            if ($response->success()) {
                echo "Se ha enviado un enlace de restablecimiento a su correo.";
            } else {
                $statusCode = $response->getStatus();
                $errorMessage = $response->getReasonPhrase();
                $errorDetails = $response->getBody();
                echo "Error al enviar el correo. Código de estado: $statusCode. Mensaje: $errorMessage. Detalles: $errorDetails";
            }
        } else {
            echo "El correo electrónico no está registrado.";
        }
        $stmt->close();
    } else {
        echo "Error al preparar la consulta.";
    }
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="restablecer_contraseña.css">
</head>
<body>
    <div class="container">
        <h2>Restablecer Contraseña</h2>
        <form method="post" action="">
            <label for="correo">Correo electrónico:</label>
            <input type="email" id="correo" name="correo" required>
            <button type="submit" name="enviar">Enviar enlace de restablecimiento</button>
        </form>
    </div>
</body>
</html>

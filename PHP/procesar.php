<?php
function limpiar_entrada($data) {
    return htmlspecialchars(strip_tags($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create uploads directory if it doesn't exist
    $directorio_subida = __DIR__ . '/uploads/';
    if (!file_exists($directorio_subida)) {
        mkdir($directorio_subida, 0755, true);
    }

    $nombre = limpiar_entrada($_POST['nombre_personaje']);
    $apodo = limpiar_entrada($_POST['apodo']);
    $edad = intval($_POST['edad_personaje']);
    $mensaje_error = '';
    $imagen_error = 'img/calavera.jpg';
    $mensaje_imagen = ''; 

    if (isset($_FILES['imagen_personaje']) && $_FILES['imagen_personaje']['error'] === UPLOAD_ERR_OK) {
        $ruta_temporal = $_FILES['imagen_personaje']['tmp_name'];
        $nombre_archivo = $_FILES['imagen_personaje']['name'];
        $tamano_archivo = $_FILES['imagen_personaje']['size'];
        $tipo_archivo = $_FILES['imagen_personaje']['type'];

        // Sanitize filename
        $nombre_archivo = preg_replace("/[^a-zA-Z0-9.]/", "_", $nombre_archivo);
        
        if ($tipo_archivo === 'image/png' && $tamano_archivo <= 10240) {
            $ruta_destino = $directorio_subida . $nombre_archivo;

            if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
                $imagen_error = 'uploads/' . $nombre_archivo;
                $mensaje_imagen = "Imagen subida correctamente";
            } else {
                $mensaje_error = 'Error al subir la imagen. Error: ' . error_get_last()['message'];
            }
        } else {
            $mensaje_error = 'La imagen debe ser PNG y no exceder 10KB.';
        }
    }

    $equipamiento = isset($_POST['equipamiento']) ? $_POST['equipamiento'] : [];

    $magia = isset($_POST['habilidad_magica']) ? limpiar_entrada($_POST['habilidad_magica']) : 'No especificado';

    echo "<div style='display: flex; justify-content: center; align-items: flex-start;'>";
    echo "<div style='text-align: left; margin-right: 20px;'>";
    echo "<h1>Datos del Personaje</h1>";
    echo "<p>Nombre: $nombre</p>";
    echo "<p>Apodo: $apodo</p>";
    echo "<p>Edad: $edad</p>";

    if (!empty($equipamiento)) {
        echo "<p>Equipamiento: " . implode(', ', $equipamiento) . "</p>";
    } else {
        echo "<p>Sin equipamiento.</p>";
    }

    echo "<p>Habilidades Mágicas: $magia</p>";
    echo "</div>";

    echo "<div style='margin-left: 20px;'>";
    
    if ($imagen_error !== 'img/calavera.jpg') {
        echo "<p>$mensaje_imagen</p>";
        echo "<img src='$imagen_error' alt='imagen_personaje' style='max-width:200px;'><br>";
    } else {
        echo "<p>Sin imagen.</p>";
        echo "<img src='img/calavera.jpg' alt='calavera' style='max-width:200px;'><br>";
        if ($mensaje_error) {
            echo "<p>$mensaje_error</p>";
        }
    }

    echo "</div>";
    echo "</div>";
} else {
    header("Location: index.html");
    exit();
}
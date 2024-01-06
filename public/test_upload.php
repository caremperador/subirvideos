<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);

    // Verificar si el directorio de subidas existe.
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Intenta crear el directorio si no existe.
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "El archivo ha sido subido con éxito.";
    } else {
        echo "Hubo un error al subir el archivo.";
        // Añadiendo depuración de errores.
        echo "\nError: " . $_FILES['file']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Prueba de Subida de Archivos</title>
</head>
<body>
    <form action="test_upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <button type="submit">Subir Archivo</button>
    </form>
</body>
</html>

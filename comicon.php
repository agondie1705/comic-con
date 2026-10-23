<?php
//mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);
try {
$nombre = "";
$edad = 0;
$entrada = "";
$talleres = [];
$precioTotal = 0;
//depuracion nombre
if (isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    //no puede estar vacio
    if (empty($nombre)) {
        throw new Exception("El nombre no puede estar vacío.");
        //solo puede tener letras y espacios
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $nombre)) {
        throw new Exception("El nombre solo puede contener letras y espacios.");
    } else {
        echo "Nombre válido: " . htmlspecialchars($nombre);
    }
}
//depuracion edad
if (isset($_POST['edad'])) {
    $edad = $_POST['edad'];
    //no puede estar vacio
    if (empty($edad)) {
        throw new Exception("La edad no puede estar vacía.");
        //solo puede tener numeros
    } elseif (!is_numeric($edad)) {
        throw new Exception("La edad solo puede contener números.");
    }else if($edad<0 || $edad>120){
        throw new Exception("La edad debe estar entre 0 y 120.");
    }else {
        echo "Edad válida: " . htmlspecialchars($edad);
    }
}
//depuracion tipo de entrada
if (isset($_POST['entrada'])) {
    $entrada = $_POST['entrada'];
    if ($entrada != "general" && $entrada != "VIP" && $entrada != "infantil") {
        throw new Exception("Tipo de entrada no válido.");
    } else {
        echo "Entrada válida: " . htmlspecialchars($entrada);
    }
}
//restriccion de edad
if ($edad < 18 && $entrada == "VIP") {
    throw new Exception("La entrada VIP está restringida a mayores de edad");
}
//depuracion talleres
$talleres = [];
if (isset($_POST['taller_videojuegos'])) {
    $talleres['videojuegos'] = 10;
}
if (isset($_POST['taller_cosplay'])) {
    $talleres['cosplay'] = 15;
}
if (isset($_POST['taller_rol'])) {
    $talleres['rol'] = 5;
}
//precio base
function calcularPrecioBase(string $tipo): int {
    switch ($tipo) {
        case "general":
            return 30;
        case "VIP":
            return 60;
        case "infantil":
            return 10;
        default:
            return 0;
    }
}
//calculo precio total
function procesarInscripcion(string $entrada, array $talleres): int {
    $precioTotal = calcularPrecioBase($entrada);
    foreach ($talleres as $precio) {
        $precioTotal = $precioTotal + $precio;
    }
    return $precioTotal;
}
$precioTotal = procesarInscripcion($entrada, $talleres);
echo "Precio total: " . $precioTotal . "€";
//registro en log
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    error_log(date("Y-m-d H:i:s") . " - Inscripción procesada");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="comicon.php" method="POST">
    <div>
        <label for="nombre">nombre</label>
        <input type="text" id="nombre" name="nombre">
    </div>
    <div>
        <label for="edad">edad</label>
        <input type="number" id="edad" name="edad">
    </div><br>
    <label for="entrada">tipo de entrada: </label>
    <select name="entrada" id="entrada">
        <option value="general">general</option>
        <option value="VIP">VIP</option>
        <option value="infantil">infantil</option>
    </select><br><br>
    <input type="checkbox" id="taller_videojuegos" name="taller_videojuegos" value="taller_videojuegos">
    <label for="talleres_videojuegos">torneo videojuegos (10€)</label> <br>
    <input type="checkbox" id="taller_cosplay" name="taller_cosplay" value="taller_cosplay">
    <label for="talleres_cosplay">torneo cosplay (15€)</label> <br>
    <input type="checkbox" id="taller_rol" name="taller_rol" value="taller_rol">
    <label for="taller_rol">rol en vivo (5€)</label> <br>
    <button type="submit">enviar</button>
    </form>
</body>
</html>
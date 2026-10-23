<?php
//mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);
$nombre = "";
$edad = 0;
$entrada = "";
$talleres = [];
$precioTotal = 0;
$inscripcionCorrecta = false;
$estado = "fallo";
try {
//depuracion nombre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? "");
    //no puede estar vacio
    if ($nombre === "") {
        throw new Exception("El nombre no puede estar vacío.");
        //solo puede tener letras y espacios
    } elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúÑñ ]+$/u", $nombre)) {
        throw new Exception("El nombre solo puede contener letras y espacios.");
    } else {
        $nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    }
//depuracion edad
    $edad = $_POST['edad'] ?? "";
    //no puede estar vacio
    if ($edad === "") {
        throw new Exception("La edad no puede estar vacía.");
    //no puede tener decimales
    }elseif (filter_var($edad, FILTER_VALIDATE_INT) === false) {
        throw new Exception("La edad debe ser un número entero.");
    }else if($edad<1 || $edad>120){
        throw new Exception("La edad debe estar entre 1 y 120.");
    }else {
        echo "Edad válida: " . htmlspecialchars($edad);
    }
//depuracion tipo de entrada
    $entrada = $_POST['entrada'] ?? "";
    if ($entrada != "general" && $entrada != "VIP" && $entrada != "infantil") {
        throw new Exception("Tipo de entrada no válido.");
    } else {
        echo "Entrada válida: " . htmlspecialchars($entrada);
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
    $inscripcionCorrecta = true;
    $estado = "éxito";
}
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    error_log(date("Y-m-d H:i:s") . " - Inscripción: " . $estado);
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
    <label for="taller_videojuegos">torneo videojuegos (10€)</label> <br>
    <input type="checkbox" id="taller_cosplay" name="taller_cosplay" value="taller_cosplay">
    <label for="taller_cosplay">torneo cosplay (15€)</label> <br>
    <input type="checkbox" id="taller_rol" name="taller_rol" value="taller_rol">
    <label for="taller_rol">rol en vivo (5€)</label> <br>
    <button type="submit">enviar</button>
    </form>
    <!--mostramos todo por pantalla-->
    <?php if ($inscripcionCorrecta): ?>
        <h2>Inscripción realizada correctamente</h2>
        <p>Nombre: <?php echo $nombre; ?></p>
        <p>Edad: <?php echo $edad; ?></p>
        <p>Tipo de entrada: <?php echo htmlspecialchars($entrada); ?></p>
        <p>Precio base: <?php echo calcularPrecioBase($entrada); ?>€</p>
        <p>Talleres seleccionados:</p>
        <?php foreach ($talleres as $nombreTaller => $precio): ?>
            <p><?php echo htmlspecialchars($nombreTaller); ?>: <?php echo $precio; ?>€</p>
        <?php endforeach; ?>
        <p>Precio total: <?php echo $precioTotal; ?>€</p>
    <?php endif; ?>
</body>
</html>
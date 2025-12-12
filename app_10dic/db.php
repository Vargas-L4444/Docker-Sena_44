<?php
$host = 'sqlricki';
$db   = 'db_proyect';
$user = 'userapp';
$pass = 'passapp';
$charset = 'utf8mb4';


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];


try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Error de conexión a la DB: " . $e->getMessage());
}



// Creación de la tabla 'usuarios' (Si no existe, se crea automáticamente al iniciar)
/*$sql_create_table = "
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    edad INT NOT NULL,
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL
);
";
$pdo->exec($sql_create_table);*/

?>
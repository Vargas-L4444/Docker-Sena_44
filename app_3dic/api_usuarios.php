<?php
require_once 'usuarios.php';

header('Content-Type: application/json');

$crud = new UsuariosCRUD($pdo);
$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;
$respuesta = [];

switch ($accion) {
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
            $telefono = $_POST['telefono'] ?? null;

            if (!$nombre || !$email || !$password) {
                $respuesta = ['success' => false, 'mensaje' => 'Faltan datos requeridos'];
            } else {
                $respuesta = $crud->crearUsuario($nombre, $email, $password, $telefono);
            }
        }
        break;

    case 'listar':
        $respuesta = $crud->obtenerTodos();
        break;

    case 'obtener':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $respuesta = $crud->obtenerPorId($id);
        } else {
            $respuesta = ['error' => 'ID requerido'];
        }
        break;

    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefono = $_POST['telefono'] ?? null;

            if (!$id || !$nombre || !$email) {
                $respuesta = ['success' => false, 'mensaje' => 'Faltan datos requeridos'];
            } else {
                $respuesta = $crud->actualizarUsuario($id, $nombre, $email, $telefono);
            }
        }
        break;

    case 'eliminar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $respuesta = $crud->eliminarUsuario($id);
            } else {
                $respuesta = ['success' => false, 'mensaje' => 'ID requerido'];
            }
        }
        break;

    case 'buscar':
        $termino = $_GET['termino'] ?? null;
        if ($termino) {
            $respuesta = $crud->buscar($termino);
        } else {
            $respuesta = ['error' => 'Término de búsqueda requerido'];
        }
        break;

    default:
        $respuesta = ['error' => 'Acción no reconocida'];
}

echo json_encode($respuesta);
?>

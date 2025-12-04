<?php
require_once 'conexion.php';

class UsuariosCRUD {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // CREATE - Crear nuevo usuario
    public function crearUsuario($nombre, $email, $password, $telefono = null) {
        try {
            // Validar que el email no exista
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'mensaje' => 'El email ya está registrado'];
            }

            // Insertar nuevo usuario
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (nombre, email, password, telefono, fecha_registro)
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$nombre, $email, $password_hash, $telefono]);
            
            return ['success' => true, 'mensaje' => 'Usuario registrado exitosamente', 'id' => $this->pdo->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error al crear usuario: ' . $e->getMessage()];
        }
    }

    // READ - Obtener todos los usuarios
    public function obtenerTodos() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nombre, email, telefono, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // READ - Obtener usuario por ID
    public function obtenerPorId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nombre, email, telefono, fecha_registro FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // UPDATE - Actualizar usuario
    public function actualizarUsuario($id, $nombre, $email, $telefono = null) {
        try {
            // Validar que el email no exista en otro usuario
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'mensaje' => 'El email ya está registrado por otro usuario'];
            }

            $stmt = $this->pdo->prepare("
                UPDATE usuarios 
                SET nombre = ?, email = ?, telefono = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$nombre, $email, $telefono, $id]);
            
            if ($result) {
                return ['success' => true, 'mensaje' => 'Usuario actualizado exitosamente'];
            } else {
                return ['success' => false, 'mensaje' => 'No se pudo actualizar el usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error al actualizar usuario: ' . $e->getMessage()];
        }
    }

    // DELETE - Eliminar usuario
    public function eliminarUsuario($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                return ['success' => true, 'mensaje' => 'Usuario eliminado exitosamente'];
            } else {
                return ['success' => false, 'mensaje' => 'No se pudo eliminar el usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error al eliminar usuario: ' . $e->getMessage()];
        }
    }

    // Buscar usuarios
    public function buscar($termino) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nombre, email, telefono, fecha_registro FROM usuarios 
                WHERE nombre LIKE ? OR email LIKE ? 
                ORDER BY fecha_registro DESC
            ");
            $busqueda = '%' . $termino . '%';
            $stmt->execute([$busqueda, $busqueda]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>

<?php
// Incluir la conexión y creación de tabla
require 'db.php'; 

$message = '';
$edit_user = null; // Variable para almacenar los datos del usuario a editar

// --- 1. MANEJO DE ACCIONES (CREATE, UPDATE, DELETE) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, correo, edad, genero) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['edad'], $_POST['genero']]);
            $message = "Usuario CREADO con éxito.";

        } elseif ($action === 'update') {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, correo=?, edad=?, genero=? WHERE id=?");
            $stmt->execute([$_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['edad'], $_POST['genero'], $_POST['id']]);
            $message = "Usuario ACTUALIZADO con éxito.";

        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = "Usuario ELIMINADO con éxito.";
        }
        
    } catch (PDOException $e) {
        $message = "Error en la operación: " . $e->getMessage();
    }
}

// --- 2. PREPARAR EDICIÓN (READ para un ID específico) ---

if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([$_GET['edit_id']]);
    $edit_user = $stmt->fetch();
}

// --- 3. LECTURA DE TODOS LOS USUARIOS (READ) ---
$stmt = $pdo->query('SELECT * FROM usuarios ORDER BY id DESC');
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuarios con Docker y PHP</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1, h2 { color: #333; }
        form { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        input, select, button { margin-bottom: 10px; padding: 8px; width: 100%; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <h1>Gestión de Usuarios con PHP y Docker</h1>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <hr>
    <h2><?= $edit_user ? '✏️ Editar Usuario (ID: ' . $edit_user['id'] . ')' : '➕ Agregar Nuevo Usuario' ?></h2>
    
    <form method="POST">
        <input type="hidden" name="action" value="<?= $edit_user ? 'update' : 'create' ?>">
        
        <?php if ($edit_user): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_user['id']) ?>">
        <?php endif; ?>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($edit_user['nombre'] ?? '') ?>" required>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($edit_user['apellido'] ?? '') ?>" required>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($edit_user['correo'] ?? '') ?>" required>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad" value="<?= htmlspecialchars($edit_user['edad'] ?? '') ?>" required>

        <label for="genero">Género:</label>
        <select id="genero" name="genero" required>
            <?php 
            $current_genero = $edit_user['genero'] ?? '';
            $options = ['Masculino', 'Femenino', 'Otro'];
            foreach ($options as $opt) {
                $selected = ($opt == $current_genero) ? 'selected' : '';
                echo "<option value=\"$opt\" $selected>$opt</option>";
            }
            ?>
        </select>

        <button type="submit"><?= $edit_user ? 'Guardar Cambios' : 'Registrar Usuario' ?></button>
        
        <?php if ($edit_user): ?>
            <a href="index.php"><button type="button">Cancelar Edición</button></a>
        <?php endif; ?>
    </form>
    <hr>

    <h2>📋 Lista de Usuarios</h2>
    
    <?php if ($usuarios): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Edad</th>
                    <th>Género</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nombre']) ?></td>
                    <td><?= htmlspecialchars($user['apellido']) ?></td>
                    <td><?= htmlspecialchars($user['correo']) ?></td>
                    <td><?= htmlspecialchars($user['edad']) ?></td>
                    <td><?= htmlspecialchars($user['genero']) ?></td>
                    <td>
                        <a href="index.php?edit_id=<?= $user['id'] ?>">Editar</a> 
                        
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que desea eliminar a <?= htmlspecialchars($user['nombre']) ?>?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" style="width: auto; padding: 5px 10px;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>

</body>
</html>
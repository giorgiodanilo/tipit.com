<?php
session_start();

// 1. El Mensaje ahora tiene un ID y un estado (contador)
class MensajeContacto {
    public string $id;
    public int $interacciones = 0;

    public function __construct(
        public string $nombre,
        public string $texto
    ) {
        // Generamos un ID único para poder identificar este mensaje luego
        $this->id = uniqid();
    }

    public function interactuar() {
        $this->interacciones++;
    }
}

// Inicializar el almacén de mensajes en la sesión
if (!isset($_SESSION['lista_mensajes'])) {
    $_SESSION['lista_mensajes'] = [];
}

// 2. El Procesador de lógica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ACCIÓN: Nuevo Mensaje
    if (isset($_POST['nombre'], $_POST['texto'])) {
        $nuevoMsg = new MensajeContacto($_POST['nombre'], $_POST['texto']);
        $_SESSION['lista_mensajes'][$nuevoMsg->id] = $nuevoMsg;
    }
    
    // ACCIÓN: Interactuar (Incrementar contador)
    if (isset($_POST['interactuar_id'])) {
        $id = $_POST['interactuar_id'];
        if (isset($_SESSION['lista_mensajes'][$id])) {
            $_SESSION['lista_mensajes'][$id]->interactuar();
        }
    }

    // Evitar reenvío de formulario al recargar
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tip it</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; width: 100%; max-width: 400px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .mensaje-recibido { background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .btn-like { background: #e7f3fe; border: none; color: #1877f2; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-like:hover { background: #dbeafe; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button[type="submit"].main-btn { width: 100%; background: #1877f2; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; }
    </style>
    <link rel="icon " type="image/png" href="tipitcorbata.png">
</head>
<body>

    <div class="card">
        <h3>Presidential</h3>
        <form method="POST">
            <input type="text" name="nombre" placeholder="Username" required>
            <textarea name="texto" placeholder="Tip it..." required></textarea>
            <button type="submit" class="main-btn">send</button>
        </form>
    </div>

    <div style="width: 100%; max-width: 400px;">
        <h3>messagges received</h3>
        <?php foreach (array_reverse($_SESSION['lista_mensajes']) as $m): ?>
            <div class="mensaje-recibido">
                <div>
                    <strong><?php echo htmlspecialchars($m->nombre); ?>:</strong>
                    <p style="margin: 5px 0;"><?php echo htmlspecialchars($m->texto); ?></p>
                </div>
                
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="interactuar_id" value="<?php echo $m->id; ?>">
                    <button type="submit" class="btn-like">
                        👍 <?php echo $m->interacciones; ?>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
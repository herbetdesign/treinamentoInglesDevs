<?php
session_start();
require '../bd.php'; // Caminho relativo à raiz

// Verifica login
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = trim($_POST['user'] ?? '');
        $pass = trim($_POST['pass'] ?? '');

        // Busca usuário
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: admin.php");
                exit;
            } else {
                $error = "Senha incorreta!";
            }
        } else {
            $error = "Usuário não encontrado!";
        }
    }

    // Exibe formulário de login
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Admin</title>
    </head>
    <body>
        <form method="POST">
            <h2>Área Administrativa</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <input type="text" name="user" placeholder="Usuário" required>
            <input type="password" name="pass" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// Processa inserção de frases e upload de áudio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $frase = $_POST['frase'];
    $categoria = $_POST['categoria'];
    $traducao = $_POST['traducao'];
    $audio_normal = '';

    // Validação de campos obrigatórios
    if (empty($frase) || empty($categoria) || empty($traducao)) {
        die("Preencha frase, categoria e tradução!");
    }

    // Processa upload do áudio
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../audios/'; // Pasta na raiz do site
        $original_name = basename($_FILES['audio_file']['name']);
        $target_path = $upload_dir . $original_name;

        // Verifica se é um arquivo de áudio válido
        $allowed_types = ['audio/mpeg', 'audio/mp3'];
        if (!in_array($_FILES['audio_file']['type'], $allowed_types)) {
            die("Formato de áudio inválido. Use MP3.");
        }

        // Move o arquivo para a pasta
        if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $target_path)) {
            $audio_normal = 'audios/' . $original_name;
        } else {
            die("Erro ao fazer upload do áudio!");
        }
    }

    // Insere no banco
    $stmt = $conn->prepare("INSERT INTO perguntas (pergunta, categoria, traducao, audio_normal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $frase, $categoria, $traducao, $audio_normal);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Frase e áudio inseridos com sucesso!</p>";
    } else {
        echo "<p style='color:red;'>Erro: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inserir Frase</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Inserir Nova Frase</h1>
    <form method="POST" enctype="multipart/form-data"> <!-- Adicionado enctype -->
        <div class="form-group">
            <label>Frase em Inglês:</label>
            <input type="text" name="frase" required>
        </div>

        <div class="form-group">
            <label>Categoria:</label>
            <select name="categoria" required>
                <option value="">Selecute</option>
                <option value="basico">Básico</option>
                <option value="basico-intermediario">Básico-Intermediário</option>
                <option value="intermediario">Intermediário</option>
                <option value="intermediario-avancado">Intermediário-Avançado</option>
                <option value="avancado">Avançado</option>
                <option value="fluente">Fluente</option>
                <option value="entrevista">Entrevista Real</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tradução para Português:</label>
            <input type="text" name="traducao" required>
        </div>

        <div class="form-group">
            <label>Upload do Áudio (MP3):</label>
            <input type="file" name="audio_file" accept="audio/*" required> <!-- Campo de upload -->
        </div>

        <button type="submit">Salvar Frase</button>
    </form>
</body>
</html>

<?php
require 'bd.php'; // Caminho relativo para a raiz

// Processa formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $frase = $_POST['frase'];
    $categoria = $_POST['categoria'];
    $traducao = $_POST['traducao'];
    $audio_normal = $_POST['audio_normal'];

    // Validação
    if (empty($frase) || empty($categoria) || empty($traducao)) {
        die("Preencha frase, categoria e tradução!");
    }

    // Insere no banco (sem opcoes ou audio_lento)
    $stmt = $conn->prepare("INSERT INTO perguntas (pergunta, categoria, traducao, audio_normal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $frase, $categoria, $traducao, $audio_normal);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Frase inserida com sucesso!</p>";
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
    <form method="POST">
        <div class="form-group">
            <label>Frase em Inglês:</label>
            <input type="text" name="frase" required>
        </div>

        <div class="form-group">
            <label>Categoria:</label>
            <select name="categoria" required>
                <option value="">Selecione</option>
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
            <label>Áudio Normal (ex: audios/frase.mp3):</label>
            <input type="text" name="audio_normal">
        </div>

        <button type="submit">Salvar Frase</button>
    </form>
</body>
</html>
<?php
require '../bd.php'; // Caminho relativo para a raiz

// Seleciona TODAS as colunas necessárias
$stmt = $conn->prepare("SELECT pergunta, traducao, audio_normal FROM perguntas");
$stmt->execute();
$result = $stmt->get_result();

$phrases = [];
while ($row = $result->fetch_assoc()) {
    $phrases[] = [
        'frase' => html_entity_decode($row['pergunta']), // Decodifica HTML entities
        'traducao' => $row['traducao'],
        'audio_normal' => $row['audio_normal']
    ];
}

header('Content-Type: application/json'); // Define cabeçalho JSON
echo json_encode($phrases);
$conn->close();
?>

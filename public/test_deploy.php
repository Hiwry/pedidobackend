<?php
// Teste simples para verificar se o arquivo está acessível
echo json_encode([
    'status' => 'success',
    'message' => 'Arquivo deploy.php está acessível!',
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'url' => $_SERVER['REQUEST_URI']
]);
?>

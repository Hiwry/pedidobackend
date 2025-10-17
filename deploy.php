<?php
// ============================
// Deploy automático do GitHub
// ============================

// Caminho do arquivo .env
$dotenvPath = __DIR__ . '/../.env'; // ajuste se o .env estiver em outro lugar

// Carregar variáveis do .env
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Recuperar variáveis
$secret = getenv('DEPLOY_SECRET');
$repoPath = getenv('REPO_PATH');

// Verificar se o request veio do GitHub
$hubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');
if ($hubSignature !== 'sha1=' . hash_hmac('sha1', $payload, $secret)) {
    http_response_code(403);
    exit('Forbidden: chave inválida');
}

// Ir para o repositório
chdir($repoPath);

// Fazer pull do GitHub
exec('git pull origin main 2>&1', $output);

// Registrar logs
$logFile = $repoPath . '/deploy.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . implode("\n", $output) . "\n", FILE_APPEND);

// Resposta simples para o GitHub
echo "Deploy executado com sucesso!\n";
?>
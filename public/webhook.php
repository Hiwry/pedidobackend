<?php
// Webhook simples para deploy
header('Content-Type: application/json');

// Log da requisição
$logFile = __DIR__ . '/../storage/logs/webhook.log';
$timestamp = date('Y-m-d H:i:s');
$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();
$payload = file_get_contents('php://input');

// Log básico
$logEntry = "[$timestamp] $method - Headers: " . json_encode($headers) . "\n";
$logEntry .= "[$timestamp] Payload: " . $payload . "\n\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Verificar se é POST
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Verificar se é do GitHub
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (strpos($userAgent, 'GitHub-Hookshot') === false) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden - Not from GitHub']);
    exit;
}

// Verificar signature (opcional)
$secret = 'sua_chave_secreta_aqui'; // Substitua pela sua chave
$hubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';

if (!empty($secret) && !empty($hubSignature)) {
    $expectedSignature = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    if (!hash_equals($expectedSignature, $hubSignature)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid signature']);
        exit;
    }
}

// Executar deploy
$deployCommands = [
    'cd ' . dirname(__DIR__),
    'git pull origin main',
    'composer install --no-dev --optimize-autoloader',
    'php artisan migrate --force',
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan route:clear',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache'
];

$output = [];
$success = true;

foreach ($deployCommands as $command) {
    $result = shell_exec($command . ' 2>&1');
    $output[] = $command . ': ' . $result;
    
    if (strpos($result, 'error') !== false || strpos($result, 'fatal') !== false) {
        $success = false;
    }
}

// Log do resultado
$logEntry = "[$timestamp] Deploy " . ($success ? 'SUCCESS' : 'FAILED') . "\n";
$logEntry .= implode("\n", $output) . "\n\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Resposta
if ($success) {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Deploy executado com sucesso!',
        'timestamp' => $timestamp,
        'output' => $output
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro durante o deploy',
        'timestamp' => $timestamp,
        'output' => $output
    ]);
}
?>

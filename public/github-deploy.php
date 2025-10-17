<?php
// ========================================
// DEPLOY AUTOMÁTICO GITHUB -> CPANEL
// ========================================

// Configurações
$logFile = __DIR__ . '/../storage/logs/github-deploy.log';
$timestamp = date('Y-m-d H:i:s');
$method = $_SERVER['REQUEST_METHOD'];

// Função para log
function writeLog($message) {
    global $logFile, $timestamp;
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Função para executar comando
function executeCommand($command, $description) {
    writeLog("Executando: $description");
    $output = shell_exec($command . ' 2>&1');
    writeLog("Resultado: $output");
    return $output;
}

// Headers de resposta
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Hub-Signature, X-GitHub-Event');

writeLog("=== INICIANDO DEPLOY ===");
writeLog("Método: $method");
writeLog("User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A'));

// Verificar método
if ($method !== 'POST') {
    writeLog("ERRO: Método não permitido");
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed', 'method' => $method]);
    exit;
}

// Verificar se é do GitHub
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (strpos($userAgent, 'GitHub-Hookshot') === false) {
    writeLog("ERRO: Não é do GitHub");
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden - Not from GitHub']);
    exit;
}

// Obter payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

writeLog("Payload recebido: " . substr($payload, 0, 200) . "...");

// Verificar se é push para main
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/main') {
    writeLog("INFO: Push não é para main, ignorando");
    echo json_encode(['message' => 'Not main branch, ignoring']);
    exit;
}

writeLog("Push detectado para branch main");

// Verificar signature (opcional - configure sua chave)
$secret = 'sua_chave_secreta_github'; // SUBSTITUA pela sua chave do GitHub
$hubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';

if (!empty($secret) && !empty($hubSignature)) {
    $expectedSignature = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    if (!hash_equals($expectedSignature, $hubSignature)) {
        writeLog("ERRO: Signature inválida");
        http_response_code(403);
        echo json_encode(['error' => 'Invalid signature']);
        exit;
    }
    writeLog("Signature válida");
}

// Ir para o diretório do projeto
$projectDir = dirname(__DIR__);
if (!chdir($projectDir)) {
    writeLog("ERRO: Não foi possível acessar diretório $projectDir");
    http_response_code(500);
    echo json_encode(['error' => 'Cannot access project directory']);
    exit;
}

writeLog("Diretório atual: " . getcwd());

// Verificar se é repositório git
if (!is_dir('.git')) {
    writeLog("ERRO: Não é repositório git");
    http_response_code(500);
    echo json_encode(['error' => 'Not a git repository']);
    exit;
}

// 1. Fazer backup antes do deploy
writeLog("Fazendo backup...");
$backupDir = $projectDir . '/backups/' . date('Y-m-d_H-i-s');
if (!is_dir($projectDir . '/backups')) {
    mkdir($projectDir . '/backups', 0755, true);
}
executeCommand("cp -r $projectDir $backupDir", "Backup do projeto");

// 2. Git pull
writeLog("Executando git pull...");
$gitOutput = executeCommand('git pull origin main', 'Git Pull');
if (strpos($gitOutput, 'error') !== false || strpos($gitOutput, 'fatal') !== false) {
    writeLog("ERRO: Falha no git pull");
    http_response_code(500);
    echo json_encode(['error' => 'Git pull failed', 'output' => $gitOutput]);
    exit;
}

// 3. Instalar dependências
writeLog("Instalando dependências...");
$composerOutput = executeCommand('composer install --no-dev --optimize-autoloader', 'Composer Install');

// 4. Executar migrations
writeLog("Executando migrations...");
$migrationOutput = executeCommand('php artisan migrate --force', 'Database Migrations');

// 5. Limpar caches
writeLog("Limpando caches...");
executeCommand('php artisan config:clear', 'Clear Config Cache');
executeCommand('php artisan cache:clear', 'Clear Application Cache');
executeCommand('php artisan view:clear', 'Clear View Cache');
executeCommand('php artisan route:clear', 'Clear Route Cache');

// 6. Otimizar para produção
writeLog("Otimizando para produção...");
executeCommand('php artisan config:cache', 'Cache Configuration');
executeCommand('php artisan route:cache', 'Cache Routes');
executeCommand('php artisan view:cache', 'Cache Views');

// 7. Ajustar permissões
writeLog("Ajustando permissões...");
executeCommand('chmod -R 755 storage/', 'Storage Permissions');
executeCommand('chmod -R 755 bootstrap/cache/', 'Bootstrap Cache Permissions');

// 8. Verificar se aplicação está funcionando
writeLog("Verificando aplicação...");
$healthCheck = file_get_contents('https://vestalize.com/');
if (empty($healthCheck)) {
    writeLog("AVISO: Aplicação pode não estar funcionando");
}

writeLog("=== DEPLOY CONCLUÍDO COM SUCESSO ===");

// Resposta de sucesso
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Deploy executado com sucesso!',
    'timestamp' => $timestamp,
    'repository' => $data['repository']['name'] ?? 'N/A',
    'commit' => $data['head_commit']['id'] ?? 'N/A',
    'author' => $data['head_commit']['author']['name'] ?? 'N/A',
    'backup' => $backupDir,
    'site' => 'https://vestalize.com'
]);

writeLog("Resposta enviada com sucesso");
?>

<?php
// ============================
// Deploy automático do GitHub
// ============================

// Configurações
$dotenvPath = __DIR__ . '/.env'; // Caminho correto do .env
$repoPath = __DIR__; // Diretório atual do projeto
$logFile = $repoPath . '/storage/logs/deploy.log';

// Função para log
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Função para executar comandos
function executeCommand($command, $description) {
    writeLog("Executando: $description");
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        writeLog("ERRO em '$description': " . implode("\n", $output));
        return false;
    }
    
    writeLog("SUCESSO em '$description': " . implode("\n", $output));
    return true;
}

// Carregar variáveis do .env
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remover aspas se existirem
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        
        putenv($name . '=' . $value);
    }
}

// Recuperar variáveis
$secret = getenv('DEPLOY_SECRET');
$repoPath = getenv('REPO_PATH') ?: __DIR__;

// Verificar se as variáveis necessárias existem
if (empty($secret)) {
    writeLog("ERRO: DEPLOY_SECRET não configurado no .env");
    http_response_code(500);
    exit('DEPLOY_SECRET não configurado');
}

// Verificar se o request veio do GitHub (apenas se secret estiver configurado)
if (!empty($secret)) {
    $hubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
    $payload = file_get_contents('php://input');
    
    if (empty($hubSignature) || $hubSignature !== 'sha1=' . hash_hmac('sha1', $payload, $secret)) {
        writeLog("ERRO: Chave de autenticação inválida");
        http_response_code(403);
        exit('Forbidden: chave inválida');
    }
}

writeLog("=== INICIANDO DEPLOY ===");

// Ir para o diretório do repositório
if (!chdir($repoPath)) {
    writeLog("ERRO: Não foi possível acessar o diretório $repoPath");
    http_response_code(500);
    exit('Erro ao acessar diretório');
}

// Verificar se é um repositório git
if (!is_dir('.git')) {
    writeLog("ERRO: Diretório não é um repositório git");
    http_response_code(500);
    exit('Não é um repositório git');
}

// 1. Fazer pull do GitHub
if (!executeCommand('git pull origin main', 'Git Pull')) {
    http_response_code(500);
    exit('Erro no git pull');
}

// 2. Instalar/atualizar dependências
if (!executeCommand('composer install --no-dev --optimize-autoloader', 'Composer Install')) {
    writeLog("AVISO: Erro no composer install, continuando...");
}

// 3. Executar migrations
if (!executeCommand('php artisan migrate --force', 'Database Migrations')) {
    writeLog("AVISO: Erro nas migrations, continuando...");
}

// 4. Limpar caches
executeCommand('php artisan config:clear', 'Clear Config Cache');
executeCommand('php artisan cache:clear', 'Clear Application Cache');
executeCommand('php artisan view:clear', 'Clear View Cache');
executeCommand('php artisan route:clear', 'Clear Route Cache');

// 5. Otimizar para produção
executeCommand('php artisan config:cache', 'Cache Configuration');
executeCommand('php artisan route:cache', 'Cache Routes');
executeCommand('php artisan view:cache', 'Cache Views');

// 6. Verificar permissões
$directories = ['storage', 'bootstrap/cache'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        writeLog("Permissões ajustadas para: $dir");
    }
}

writeLog("=== DEPLOY CONCLUÍDO COM SUCESSO ===");

// Resposta para o GitHub
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Deploy executado com sucesso!',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
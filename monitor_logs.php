<?php
echo "=== MONITOR DE LOGS DE EDIÇÃO ===\n";
echo "Pressione Ctrl+C para parar\n\n";

$logFile = __DIR__ . '/storage/logs/laravel.log';
$lastSize = 0;

if (!file_exists($logFile)) {
    echo "Arquivo de log não encontrado: $logFile\n";
    exit(1);
}

echo "Monitorando: $logFile\n";
echo "Aguardando logs de edição...\n\n";

while (true) {
    clearstatcache();
    $currentSize = filesize($logFile);
    
    if ($currentSize > $lastSize) {
        $handle = fopen($logFile, 'r');
        fseek($handle, $lastSize);
        
        while (($line = fgets($handle)) !== false) {
            // Filtrar apenas logs relacionados à edição
            if (strpos($line, 'SEWING') !== false || 
                strpos($line, 'AJAX') !== false || 
                strpos($line, 'update_items') !== false ||
                strpos($line, 'edit_order_data') !== false) {
                echo $line;
            }
        }
        
        fclose($handle);
        $lastSize = $currentSize;
    }
    
    sleep(1);
}
?>

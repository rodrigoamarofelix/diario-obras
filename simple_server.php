<?php
/**
 * Servidor HTTP simples para acesso móvel
 * Funciona com qualquer versão do PHP
 */

// Configurações
$host = '0.0.0.0';
$port = 8081;
$target_host = 'localhost';
$target_port = 3000;

echo "🚀 Iniciando servidor HTTP simples...\n";
echo "📡 Host: $host\n";
echo "🔌 Porta: $port\n";
echo "🎯 Redirecionando para: $target_host:$target_port\n";
echo "📱 Acesse: http://172.31.163.215:$port\n";
echo "🛑 Pressione Ctrl+C para parar\n";
echo str_repeat("-", 50) . "\n";

// Criar socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (!$socket) {
    die("❌ Erro ao criar socket: " . socket_strerror(socket_last_error()) . "\n");
}

// Configurar socket para reutilizar endereço
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

// Bind do socket
if (!socket_bind($socket, $host, $port)) {
    die("❌ Erro ao fazer bind: " . socket_strerror(socket_last_error()) . "\n");
}

// Escutar conexões
if (!socket_listen($socket, 5)) {
    die("❌ Erro ao escutar: " . socket_strerror(socket_last_error()) . "\n");
}

echo "✅ Servidor iniciado com sucesso!\n";

while (true) {
    // Aceitar conexão
    $client = socket_accept($socket);
    if (!$client) {
        continue;
    }

    // Ler requisição
    $request = '';
    while (($line = socket_read($client, 1024)) !== false) {
        $request .= $line;
        if (strpos($request, "\r\n\r\n") !== false) {
            break;
        }
    }

    if (empty($request)) {
        socket_close($client);
        continue;
    }

    // Parse da requisição
    $lines = explode("\r\n", $request);
    $request_line = $lines[0];
    $parts = explode(' ', $request_line);
    $method = $parts[0];
    $path = $parts[1] ?? '/';

    // Construir URL de destino
    $target_url = "http://$target_host:$target_port$path";

    // Preparar contexto HTTP
    $context_options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", array_slice($lines, 1, -2)),
            'timeout' => 30
        ]
    ];

    // Se for POST/PUT, adicionar dados
    if (in_array($method, ['POST', 'PUT'])) {
        $body_start = strpos($request, "\r\n\r\n");
        if ($body_start !== false) {
            $body = substr($request, $body_start + 4);
            $context_options['http']['content'] = $body;
        }
    }

    $context = stream_context_create($context_options);

    // Fazer requisição para o servidor Laravel
    $response = @file_get_contents($target_url, false, $context);

    if ($response === false) {
        // Erro na requisição
        $http_response = "HTTP/1.1 502 Bad Gateway\r\n";
        $http_response .= "Content-Type: text/html\r\n";
        $http_response .= "Content-Length: " . strlen("Erro ao conectar com o servidor Laravel") . "\r\n";
        $http_response .= "Access-Control-Allow-Origin: *\r\n";
        $http_response .= "\r\n";
        $http_response .= "Erro ao conectar com o servidor Laravel";
    } else {
        // Sucesso
        $http_response = "HTTP/1.1 200 OK\r\n";
        $http_response .= "Content-Type: text/html\r\n";
        $http_response .= "Content-Length: " . strlen($response) . "\r\n";
        $http_response .= "Access-Control-Allow-Origin: *\r\n";
        $http_response .= "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS\r\n";
        $http_response .= "Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN, Cookie\r\n";
        $http_response .= "\r\n";
        $http_response .= $response;
    }

    // Enviar resposta
    socket_write($client, $http_response);
    socket_close($client);
}

socket_close($socket);
?>

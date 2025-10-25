#!/usr/bin/env python3
"""
Servidor de túnel simples e robusto para acesso móvel
"""
import socket
import threading
import http.server
import socketserver
import urllib.request
import urllib.parse
import json
import time

class SimpleTunnelHandler(http.server.BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        """Log personalizado"""
        timestamp = time.strftime("%H:%M:%S")
        print(f"[{timestamp}] {format % args}")

    def do_GET(self):
        self._handle_request('GET')

    def do_POST(self):
        self._handle_request('POST')

    def do_PUT(self):
        self._handle_request('PUT')

    def do_DELETE(self):
        self._handle_request('DELETE')

    def do_OPTIONS(self):
        """Suporte para CORS preflight"""
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Cookie, Authorization')
        self.send_header('Access-Control-Max-Age', '86400')
        self.end_headers()

    def _handle_request(self, method):
        try:
            # Construir URL
            url = f"http://localhost:8000{self.path}"
            if hasattr(self, 'query_string') and self.query_string:
                url += f"?{self.query_string}"

            # Preparar requisição
            if method in ['POST', 'PUT']:
                content_length = int(self.headers.get('Content-Length', 0))
                post_data = self.rfile.read(content_length)
                req = urllib.request.Request(url, data=post_data, method=method)
            else:
                req = urllib.request.Request(url, method=method)

            # Copiar headers importantes
            headers_to_copy = [
                'Content-Type', 'Content-Length', 'X-CSRF-TOKEN', 'Cookie',
                'User-Agent', 'Accept', 'Accept-Language', 'Accept-Encoding',
                'Authorization', 'Referer', 'Origin'
            ]

            for header_name in headers_to_copy:
                if header_name in self.headers:
                    req.add_header(header_name, self.headers[header_name])

            # Fazer requisição
            with urllib.request.urlopen(req, timeout=30) as response:
                data = response.read()

                # Enviar resposta
                self.send_response(response.getcode())

                # Copiar headers de resposta importantes
                response_headers = [
                    'Content-Type', 'Content-Length', 'Set-Cookie',
                    'Location', 'Cache-Control', 'Expires'
                ]

                for header_name in response_headers:
                    if header_name in response.headers:
                        self.send_header(header_name, response.headers[header_name])

                # Headers CORS
                self.send_header('Access-Control-Allow-Origin', '*')
                self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Cookie, Authorization')

                self.end_headers()
                self.wfile.write(data)

        except urllib.error.HTTPError as e:
            self.send_response(e.code)
            self.send_header('Content-Type', 'text/html')
            self.end_headers()
            self.wfile.write(f"<h1>Erro {e.code}</h1><p>{e.reason}</p>".encode())

        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'text/html')
            self.end_headers()
            self.wfile.write(f"<h1>Erro 500</h1><p>{str(e)}</p>".encode())

def get_local_ip():
    """Obtém o IP local da máquina"""
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
        s.close()
        return ip
    except:
        return "127.0.0.1"

def main():
    # Configurações
    PORT = 9001
    LOCAL_IP = get_local_ip()

    print("🚀 Iniciando servidor de túnel simples...")
    print(f"📡 IP local: {LOCAL_IP}")
    print(f"🔌 Porta: {PORT}")
    print(f"🌐 URL para acesso: http://{LOCAL_IP}:{PORT}")
    print("📱 Use esta URL no seu celular!")
    print("🛑 Pressione Ctrl+C para parar")
    print("-" * 50)

    try:
        with socketserver.TCPServer(("", PORT), SimpleTunnelHandler) as httpd:
            httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Servidor parado!")
    except Exception as e:
        print(f"❌ Erro: {e}")

if __name__ == "__main__":
    main()

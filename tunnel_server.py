#!/usr/bin/env python3
"""
Servidor de túnel local para acesso móvel
"""
import socket
import threading
import http.server
import socketserver
import urllib.request
import urllib.parse
import json

class TunnelHandler(http.server.BaseHTTPRequestHandler):
    def do_GET(self):
        try:
            # Redirecionar para o servidor local
            url = f"http://localhost:8000{self.path}"
            req = urllib.request.Request(url)

            # Copiar headers importantes
            for header_name in ['Cookie', 'User-Agent', 'Accept', 'Accept-Language']:
                if header_name in self.headers:
                    req.add_header(header_name, self.headers[header_name])

            req.add_header('User-Agent', 'Tunnel-Server/1.0')

            with urllib.request.urlopen(req) as response:
                data = response.read()

            self.send_response(response.getcode())
            self.send_header('Content-Type', response.headers.get('Content-Type', 'text/html'))
            self.send_header('Access-Control-Allow-Origin', '*')
            self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Cookie')
            self.end_headers()
            self.wfile.write(data)

        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'text/plain')
            self.end_headers()
            self.wfile.write(f"Erro GET: {str(e)}".encode())

    def do_POST(self):
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)

            url = f"http://localhost:8000{self.path}"
            req = urllib.request.Request(url, data=post_data)

            # Copiar todos os headers importantes
            for header_name in ['Content-Type', 'Content-Length', 'X-CSRF-TOKEN', 'Cookie']:
                if header_name in self.headers:
                    req.add_header(header_name, self.headers[header_name])

            req.add_header('User-Agent', 'Tunnel-Server/1.0')

            with urllib.request.urlopen(req) as response:
                data = response.read()

            self.send_response(response.getcode())
            self.send_header('Content-Type', response.headers.get('Content-Type', 'text/html'))
            self.send_header('Access-Control-Allow-Origin', '*')
            self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Cookie')
            self.end_headers()
            self.wfile.write(data)

        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'text/plain')
            self.end_headers()
            self.wfile.write(f"Erro POST: {str(e)}".encode())

    def do_OPTIONS(self):
        """Suporte para CORS preflight"""
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Cookie')
        self.end_headers()

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
    PORT = 9000
    LOCAL_IP = get_local_ip()

    print("🚀 Iniciando servidor de túnel local...")
    print(f"📡 IP local: {LOCAL_IP}")
    print(f"🔌 Porta: {PORT}")
    print(f"🌐 URL para acesso: http://{LOCAL_IP}:{PORT}")
    print("📱 Use esta URL no seu celular!")
    print("🛑 Pressione Ctrl+C para parar")
    print("-" * 50)

    try:
        with socketserver.TCPServer(("", PORT), TunnelHandler) as httpd:
            httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Servidor parado!")
    except Exception as e:
        print(f"❌ Erro: {e}")

if __name__ == "__main__":
    main()


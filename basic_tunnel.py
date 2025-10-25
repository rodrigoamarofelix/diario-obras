#!/usr/bin/env python3
"""
Servidor HTTP simples para túnel móvel
"""
import socket
import http.server
import socketserver
import urllib.request
import urllib.parse

class BasicTunnelHandler(http.server.BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        print(f"[{self.address_string()}] {format % args}")

    def do_GET(self):
        self._proxy_request()

    def do_POST(self):
        self._proxy_request()

    def do_PUT(self):
        self._proxy_request()

    def do_DELETE(self):
        self._proxy_request()

    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Cookie')
        self.end_headers()

    def _proxy_request(self):
        try:
            # URL de destino
            url = f"http://localhost:8000{self.path}"

            # Preparar requisição
            if self.command in ['POST', 'PUT']:
                content_length = int(self.headers.get('Content-Length', 0))
                post_data = self.rfile.read(content_length)
                req = urllib.request.Request(url, data=post_data, method=self.command)
            else:
                req = urllib.request.Request(url, method=self.command)

            # Copiar headers
            for header_name in ['Content-Type', 'Cookie', 'X-CSRF-TOKEN', 'User-Agent']:
                if header_name in self.headers:
                    req.add_header(header_name, self.headers[header_name])

            # Fazer requisição
            with urllib.request.urlopen(req, timeout=30) as response:
                data = response.read()

                # Enviar resposta
                self.send_response(response.getcode())
                self.send_header('Content-Type', response.headers.get('Content-Type', 'text/html'))
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(data)

        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'text/html')
            self.end_headers()
            self.wfile.write(f"<h1>Erro</h1><p>{str(e)}</p>".encode())

def get_local_ip():
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
        s.close()
        return ip
    except:
        return "127.0.0.1"

def main():
    PORT = 9002
    LOCAL_IP = get_local_ip()

    print("🚀 Servidor de túnel básico iniciado!")
    print(f"📡 IP: {LOCAL_IP}")
    print(f"🔌 Porta: {PORT}")
    print(f"🌐 URL: http://{LOCAL_IP}:{PORT}")
    print("📱 Acesse esta URL no seu celular!")
    print("🛑 Ctrl+C para parar")
    print("-" * 50)

    try:
        with socketserver.TCPServer(("", PORT), BasicTunnelHandler) as httpd:
            httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Servidor parado!")
    except Exception as e:
        print(f"❌ Erro: {e}")

if __name__ == "__main__":
    main()



#!/usr/bin/env python3
import http.server
import socketserver
import urllib.request
import urllib.parse
import json
import sys

class TunnelHandler(http.server.BaseHTTPRequestHandler):
    def do_GET(self):
        self.proxy_request()

    def do_POST(self):
        self.proxy_request()

    def do_PUT(self):
        self.proxy_request()

    def do_DELETE(self):
        self.proxy_request()

    def do_OPTIONS(self):
        self.proxy_request()

    def do_HEAD(self):
        self.proxy_request()

    def proxy_request(self):
        try:
            # Target URL
            target_url = f"http://localhost:3000{self.path}"

            # Prepare headers
            headers = {}
            for header, value in self.headers.items():
                if header.lower() not in ['host', 'connection']:
                    headers[header] = value

            # Get request data for POST/PUT
            data = None
            if self.command in ['POST', 'PUT']:
                content_length = int(self.headers.get('Content-Length', 0))
                if content_length > 0:
                    data = self.rfile.read(content_length)

            # Make request
            req = urllib.request.Request(target_url, data=data, headers=headers, method=self.command)

            with urllib.request.urlopen(req, timeout=30) as response:
                # Send response headers
                self.send_response(response.status)

                for header, value in response.headers.items():
                    if header.lower() not in ['connection', 'transfer-encoding']:
                        self.send_header(header, value)

                # Add CORS headers
                self.send_header('Access-Control-Allow-Origin', '*')
                self.send_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                self.send_header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')

                self.end_headers()

                # Send response body
                self.wfile.write(response.read())

        except Exception as e:
            print(f"Error proxying request: {e}")
            self.send_error(500, f"Proxy error: {str(e)}")

    def log_message(self, format, *args):
        print(f"[{self.address_string()}] {format % args}")

if __name__ == "__main__":
    PORT = 8084

    print(f"🚀 Iniciando túnel simples...")
    print(f"📡 IP local: 172.31.163.215")
    print(f"🔌 Porta: {PORT}")
    print(f"🌐 URL para acesso: http://172.31.163.215:{PORT}")
    print(f"📱 Use esta URL no seu celular!")
    print(f"🛑 Pressione Ctrl+C para parar")
    print("-" * 50)

    try:
        with socketserver.TCPServer(("0.0.0.0", PORT), TunnelHandler) as httpd:
            httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n✅ Servidor parado pelo usuário")
    except Exception as e:
        print(f"❌ Erro: {e}")

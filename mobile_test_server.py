#!/usr/bin/env python3
"""
Servidor HTTP simples para servir arquivos estáticos
"""
import http.server
import socketserver
import os
import webbrowser

PORT = 8082

class MyHTTPRequestHandler(http.server.SimpleHTTPRequestHandler):
    def end_headers(self):
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        super().end_headers()

def main():
    # Mudar para o diretório do projeto
    os.chdir('/home/rodrigo/projetos/diario-obras')

    print("🚀 Servidor de teste móvel iniciado!")
    print(f"📡 Porta: {PORT}")
    print(f"🌐 URL: http://localhost:{PORT}/teste_mobile.html")
    print("📱 Abra esta URL no seu celular!")
    print("🛑 Pressione Ctrl+C para parar")
    print("-" * 50)

    try:
        with socketserver.TCPServer(("", PORT), MyHTTPRequestHandler) as httpd:
            httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Servidor parado!")
    except Exception as e:
        print(f"❌ Erro: {e}")

if __name__ == "__main__":
    main()




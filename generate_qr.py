#!/usr/bin/env python3
"""
Gerador de QR Code para acesso móvel
"""
import qrcode
import socket

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

def create_qr_code():
    """Cria QR Code com URL de acesso"""
    ip = get_local_ip()
    port = 3000

    # URLs para diferentes portas
    urls = [
        f"http://{ip}:{port}",
        f"http://{ip}:8081",
        f"http://{ip}:9000",
        f"http://{ip}:9001",
        f"http://{ip}:9002"
    ]

    print("📱 QR Codes para acesso móvel:")
    print("=" * 50)

    for i, url in enumerate(urls, 1):
        print(f"\n{i}. {url}")

        # Criar QR Code
        qr = qrcode.QRCode(
            version=1,
            error_correction=qrcode.constants.ERROR_CORRECT_L,
            box_size=10,
            border=4,
        )
        qr.add_data(url)
        qr.make(fit=True)

        # Gerar imagem
        img = qr.make_image(fill_color="black", back_color="white")
        img.save(f"qr_code_{i}.png")

        print(f"   QR Code salvo como: qr_code_{i}.png")

    print("\n📱 Instruções:")
    print("1. Abra a galeria do celular")
    print("2. Escaneie um dos QR codes")
    print("3. Teste cada URL até encontrar uma que funcione")
    print("4. Para testar fotos: adicione /diario-obras/fotos/create na URL")

if __name__ == "__main__":
    create_qr_code()




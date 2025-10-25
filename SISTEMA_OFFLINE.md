# 📱 Sistema Offline - Diário de Obras

## 🎯 **Funcionalidades**

### ✅ **O que funciona OFFLINE:**
- **Tirar fotos** com a câmera do celular
- **Capturar GPS** automaticamente
- **Salvar fotos** localmente no celular
- **Visualizar mapa** com localização
- **Gerenciar fotos** salvas
- **Funciona sem internet** completamente

### 🔄 **Sincronização:**
- **Quando online:** Botão "Sincronizar" fica ativo
- **Dados salvos:** Ficam armazenados no celular
- **Sincronização:** Envia para o servidor quando possível

## 📱 **Como Usar**

### 1. **Acessar o Sistema**
```
https://mariano-appointed-matteo.ngrok-free.dev/sistema_offline.html
```

### 2. **Tirar Fotos**
1. **Selecione projeto** e categoria
2. **Adicione descrição** (opcional)
3. **Clique em "Tirar Foto com Câmera"**
4. **Permita acesso** à câmera
5. **Capture a foto**
6. **Clique em "Salvar Foto (Offline)"**

### 3. **GPS Automático**
- **Localização** é capturada automaticamente
- **Mapa** mostra sua posição
- **Precisão** é exibida
- **Botão "Atualizar"** para nova localização

### 4. **Gerenciar Fotos**
- **Ver fotos salvas** na seção "Fotos Salvas"
- **Excluir fotos** individualmente
- **Contador** mostra quantas fotos estão salvas

### 5. **Sincronização**
- **Quando online:** Botão "Sincronizar" fica ativo
- **Clique para sincronizar** todas as fotos
- **Aguarde** a sincronização completar

## 🔧 **Tecnologias Usadas**

### **Armazenamento Local:**
- **IndexedDB:** Banco de dados no navegador
- **Persistente:** Dados não são perdidos
- **Capacidade:** Até vários GB de fotos

### **GPS:**
- **Geolocation API:** Captura coordenadas
- **Leaflet:** Mapa interativo
- **OpenStreetMap:** Tiles gratuitos

### **Câmera:**
- **MediaDevices API:** Acesso à câmera
- **Canvas:** Captura de fotos
- **Base64:** Conversão para armazenamento

## 📊 **Vantagens**

### ✅ **Sistema Offline:**
- **Funciona sem internet**
- **Dados seguros** no celular
- **Sincronização automática**
- **Interface moderna**
- **Responsivo** para mobile

### ✅ **Para Obras:**
- **Fotos com GPS** precisas
- **Categorização** por tipo
- **Descrições** detalhadas
- **Histórico** completo
- **Backup** automático

## 🚀 **API Implementada**

### ✅ **Já Implementado:**
- **Rota:** `POST /api/sync-photos`
- **Rota:** `GET /api/offline-data` (NOVA!)
- **Controller:** `FotoObraController@syncOfflinePhotos`
- **Controller:** `FotoObraController@getOfflineData` (NOVO!)
- **Autenticação:** Middleware `auth` obrigatório
- **Validação:** Dados obrigatórios verificados
- **Armazenamento:** Fotos salvas em `storage/app/public/fotos/`
- **Banco de dados:** Registros criados na tabela `fotos_obras`
- **Dados dinâmicos:** Projetos e categorias reais do sistema

### 🔧 **Como Funciona:**

1. **Sistema offline** carrega projetos e categorias reais
2. **Cache local** mantém dados quando offline
3. **Fotos** são salvas localmente no celular
4. **GPS** é capturado automaticamente
5. **Online** botão sincronizar fica ativo
6. **Sincronização** envia todas as fotos para o servidor
7. **Limpeza** dados locais são removidos após sucesso

### 📊 **Dados Sincronizados:**
- **Fotos:** Arquivos de imagem
- **GPS:** Latitude, longitude, altitude, precisão
- **Metadados:** Projeto, categoria, descrição
- **Timestamp:** Data/hora da captura
- **Usuário:** ID do usuário logado
- **Projetos:** Todos os projetos (planejamento, em_andamento, concluido)
- **Categorias:** Categorias padrão do sistema (antes, progresso, problema, solução, final, geral)

## 📱 **URL para Teste**

**`https://mariano-appointed-matteo.ngrok-free.dev/sistema_offline.html`**

## 💡 **Dicas de Uso**

1. **Primeira vez:** Permita acesso à câmera e localização
2. **Offline:** Sistema funciona perfeitamente sem internet
3. **Sincronização:** Faça quando tiver conexão estável
4. **Backup:** Dados ficam salvos no celular
5. **Performance:** Funciona melhor em celulares modernos

## 🔒 **Segurança**

- **Dados locais:** Ficam apenas no seu celular
- **Sincronização:** Criptografada via HTTPS
- **Privacidade:** Nenhum dado é enviado sem permissão
- **Controle:** Você decide quando sincronizar

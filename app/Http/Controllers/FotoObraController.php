<?php

namespace App\Http\Controllers;

use App\Models\FotoObra;
use App\Models\Projeto;
use App\Models\AtividadeObra;
use App\Models\EquipeObra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FotoObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fotos = FotoObra::with(['projeto', 'atividade', 'equipe', 'usuario'])
            ->withTrashed()
            ->latest('data_captura')
            ->paginate(20);

        return view('diario-obras.fotos.index', compact('fotos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projetoId = $request->get('projeto_id');
        $projetos = Projeto::ativos()->get();
        $atividades = AtividadeObra::where('status', '!=', 'concluido')->get();
        $equipes = EquipeObra::with(['pessoa', 'projeto'])->latest()->limit(50)->get();

        return view('diario-obras.fotos.create', compact('projetos', 'atividades', 'equipes', 'projetoId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'atividade_id' => 'nullable|exists:atividade_obras,id',
            'equipe_id' => 'nullable|exists:equipe_obras,id',
            'titulo' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'arquivo' => 'required|file|image|max:20480', // 20MB max
            'categoria' => 'required|in:antes,progresso,problema,solucao,final,geral',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'altitude' => 'nullable|numeric',
            'precisao' => 'nullable|numeric',
            'data_captura' => 'nullable|date',
        ]);

        $arquivo = $request->file('arquivo');

        // Gerar hash único para evitar duplicatas
        $hashArquivo = hash_file('sha256', $arquivo->getPathname());

        // Verificar se já existe uma foto com o mesmo hash
        if (FotoObra::where('hash_arquivo', $hashArquivo)->exists()) {
            return back()->with('error', 'Esta foto já foi enviada anteriormente!');
        }

        // Processar e salvar a imagem
        $resultadoUpload = $this->processarImagem($arquivo);

        // Extrair metadados EXIF
        $exifData = $this->extrairExifData($arquivo->getPathname());

        $foto = FotoObra::create([
            'projeto_id' => $request->projeto_id,
            'atividade_id' => $request->atividade_id,
            'equipe_id' => $request->equipe_id,
            'user_id' => auth()->id(),
            'titulo' => $request->titulo ?: $this->gerarTituloAutomatico($request->categoria),
            'descricao' => $request->descricao,
            'caminho_arquivo' => $resultadoUpload['caminho'],
            'nome_arquivo' => $resultadoUpload['nome'],
            'mime_type' => $arquivo->getMimeType(),
            'tamanho_arquivo' => $arquivo->getSize(),
            'hash_arquivo' => $hashArquivo,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'altitude' => $request->altitude,
            'precisao' => $request->precisao,
            'camera_marca' => $exifData['marca'] ?? null,
            'camera_modelo' => $exifData['modelo'] ?? null,
            'lente' => $exifData['lente'] ?? null,
            'aperture' => $exifData['aperture'] ?? null,
            'shutter_speed' => $exifData['shutter_speed'] ?? null,
            'iso' => $exifData['iso'] ?? null,
            'focal_length' => $exifData['focal_length'] ?? null,
            'tags' => $request->tags ?: $this->gerarTagsAutomaticas($request->categoria),
            'categoria' => $request->categoria,
            'data_captura' => $request->data_captura ?: $exifData['data_captura'] ?? now(),
            'data_upload' => now(),
        ]);

        return redirect()->route('diario-obras.fotos.index')
            ->with('success', 'Foto registrada com sucesso!');
    }

    /**
     * Processar e otimizar imagem
     */
    private function processarImagem($arquivo)
    {
        $nomeOriginal = $arquivo->getClientOriginalName();
        $nomeArquivo = time() . '_' . Str::slug(pathinfo($nomeOriginal, PATHINFO_FILENAME)) . '.jpg';

        // Criar diretório se não existir
        $diretorio = 'fotos-obras/' . date('Y/m');
        Storage::disk('public')->makeDirectory($diretorio);

        $caminhoCompleto = $diretorio . '/' . $nomeArquivo;

        // Salvar arquivo diretamente (sem processamento por enquanto)
        $arquivo->storeAs($diretorio, $nomeArquivo, 'public');

        return [
            'caminho' => $caminhoCompleto,
            'nome' => $nomeArquivo
        ];
    }

    /**
     * Extrair dados EXIF da imagem
     */
    private function extrairExifData($caminhoArquivo)
    {
        $exif = @exif_read_data($caminhoArquivo);

        if (!$exif) {
            return [];
        }

        $dados = [];

        // Dados da câmera
        $dados['marca'] = $exif['Make'] ?? null;
        $dados['modelo'] = $exif['Model'] ?? null;
        $dados['lente'] = $exif['UndefinedTag:0xA434'] ?? null;

        // Configurações da foto
        $dados['aperture'] = isset($exif['COMPUTED']['ApertureFNumber']) ?
            floatval(str_replace('f/', '', $exif['COMPUTED']['ApertureFNumber'])) : null;

        $dados['shutter_speed'] = isset($exif['ExposureTime']) ?
            floatval($exif['ExposureTime']) : null;

        $dados['iso'] = $exif['ISOSpeedRatings'] ?? null;
        $dados['focal_length'] = isset($exif['FocalLength']) ?
            floatval($exif['FocalLength']) : null;

        // Data de captura
        if (isset($exif['DateTimeOriginal'])) {
            $dados['data_captura'] = \DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
        }

        return $dados;
    }

    /**
     * Gerar título automático baseado na categoria
     */
    private function gerarTituloAutomatico($categoria)
    {
        $titulos = [
            'antes' => 'Foto Antes da Obra',
            'progresso' => 'Progresso da Obra',
            'problema' => 'Problema Identificado',
            'solucao' => 'Solução Implementada',
            'final' => 'Resultado Final',
            'geral' => 'Foto da Obra'
        ];

        return $titulos[$categoria] ?? 'Foto da Obra';
    }

    /**
     * Gerar tags automáticas baseadas na categoria
     */
    private function gerarTagsAutomaticas($categoria)
    {
        $tagsPorCategoria = [
            'antes' => ['antes', 'inicio', 'baseline'],
            'progresso' => ['progresso', 'andamento', 'desenvolvimento'],
            'problema' => ['problema', 'issue', 'defeito'],
            'solucao' => ['solucao', 'correcao', 'reparo'],
            'final' => ['final', 'concluido', 'resultado'],
            'geral' => ['obra', 'construcao']
        ];

        return $tagsPorCategoria[$categoria] ?? ['obra'];
    }

    /**
     * Upload múltiplo de fotos
     */
    public function uploadMultiple(Request $request)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'atividade_id' => 'nullable|exists:atividade_obras,id',
            'equipe_id' => 'nullable|exists:equipe_obras,id',
            'fotos' => 'required|array|min:1|max:10',
            'fotos.*' => 'file|image|max:20480',
            'categoria' => 'required|in:antes,progresso,problema,solucao,final,geral',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $fotosSalvas = 0;
        $erros = [];

        foreach ($request->file('fotos') as $index => $arquivo) {
            try {
                // Gerar hash único
                $hashArquivo = hash_file('sha256', $arquivo->getPathname());

                if (FotoObra::where('hash_arquivo', $hashArquivo)->exists()) {
                    $erros[] = "Foto " . ($index + 1) . ": Já foi enviada anteriormente";
                    continue;
                }

                // Processar imagem
                $resultadoUpload = $this->processarImagem($arquivo);
                $exifData = $this->extrairExifData($arquivo->getPathname());

                FotoObra::create([
                    'projeto_id' => $request->projeto_id,
                    'atividade_id' => $request->atividade_id,
                    'equipe_id' => $request->equipe_id,
                    'user_id' => auth()->id(),
                    'titulo' => $this->gerarTituloAutomatico($request->categoria) . ' ' . ($index + 1),
                    'caminho_arquivo' => $resultadoUpload['caminho'],
                    'nome_arquivo' => $resultadoUpload['nome'],
                    'mime_type' => $arquivo->getMimeType(),
                    'tamanho_arquivo' => $arquivo->getSize(),
                    'hash_arquivo' => $hashArquivo,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'camera_marca' => $exifData['marca'] ?? null,
                    'camera_modelo' => $exifData['modelo'] ?? null,
                    'tags' => $this->gerarTagsAutomaticas($request->categoria),
                    'categoria' => $request->categoria,
                    'data_captura' => $exifData['data_captura'] ?? now(),
                    'data_upload' => now(),
                ]);

                $fotosSalvas++;
            } catch (\Exception $e) {
                $erros[] = "Foto " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $mensagem = "{$fotosSalvas} fotos salvas com sucesso!";
        if (!empty($erros)) {
            $mensagem .= " Erros: " . implode(', ', $erros);
        }

        return back()->with('success', $mensagem);
    }

    /**
     * Display the specified resource.
     */
    public function show(FotoObra $foto)
    {
        $foto->load(['projeto', 'atividade', 'equipe', 'usuario']);
        return view('diario-obras.fotos.show', compact('foto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FotoObra $foto)
    {
        $projetos = Projeto::ativos()->get();
        $atividades = AtividadeObra::where('status', '!=', 'concluido')->get();
        $equipes = EquipeObra::with(['pessoa', 'projeto'])->latest()->limit(50)->get();

        return view('diario-obras.fotos.edit', compact('foto', 'projetos', 'atividades', 'equipes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FotoObra $foto)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'atividade_id' => 'nullable|exists:atividade_obras,id',
            'equipe_id' => 'nullable|exists:equipe_obras,id',
            'titulo' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'arquivo' => 'nullable|file|image|max:20480',
            'categoria' => 'required|in:antes,progresso,problema,solucao,final,geral',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'altitude' => 'nullable|numeric',
            'precisao' => 'nullable|numeric',
            'aprovada' => 'boolean',
            'publica' => 'boolean',
        ]);

        $dados = [
            'projeto_id' => $request->projeto_id,
            'atividade_id' => $request->atividade_id,
            'equipe_id' => $request->equipe_id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'tags' => $request->tags,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'altitude' => $request->altitude,
            'precisao' => $request->precisao,
            'aprovada' => $request->aprovada ?? false,
            'publica' => $request->publica ?? true,
        ];

        // Se um novo arquivo foi enviado
        if ($request->hasFile('arquivo')) {
            // Remove o arquivo antigo
            Storage::disk('public')->delete($foto->caminho_arquivo);

            $arquivo = $request->file('arquivo');
            $resultadoUpload = $this->processarImagem($arquivo);
            $exifData = $this->extrairExifData($arquivo->getPathname());

            $dados['caminho_arquivo'] = $resultadoUpload['caminho'];
            $dados['nome_arquivo'] = $resultadoUpload['nome'];
            $dados['mime_type'] = $arquivo->getMimeType();
            $dados['tamanho_arquivo'] = $arquivo->getSize();
            $dados['hash_arquivo'] = hash_file('sha256', $arquivo->getPathname());
            $dados['camera_marca'] = $exifData['marca'] ?? null;
            $dados['camera_modelo'] = $exifData['modelo'] ?? null;
            $dados['lente'] = $exifData['lente'] ?? null;
            $dados['aperture'] = $exifData['aperture'] ?? null;
            $dados['shutter_speed'] = $exifData['shutter_speed'] ?? null;
            $dados['iso'] = $exifData['iso'] ?? null;
            $dados['focal_length'] = $exifData['focal_length'] ?? null;
            $dados['data_captura'] = $exifData['data_captura'] ?? now();
        }

        $foto->update($dados);

        return redirect()->route('diario-obras.fotos.index')
            ->with('success', 'Foto atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(FotoObra $foto)
    {
        $foto->delete();

        return redirect()->route('diario-obras.fotos.index')
            ->with('success', 'Foto excluída com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore($id)
    {
        $foto = FotoObra::withTrashed()->findOrFail($id);
        $foto->restore();

        return redirect()->route('diario-obras.fotos.index')
            ->with('success', 'Foto restaurada com sucesso!');
    }

    /**
     * Permanently delete a resource (only for administrators).
     */
    public function forceDelete($id)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Você não tem permissão para excluir permanentemente.');
        }

        $foto = FotoObra::withTrashed()->findOrFail($id);

        // Remove o arquivo físico
        Storage::disk('public')->delete($foto->caminho_arquivo);

        $foto->forceDelete();

        return redirect()->route('diario-obras.fotos.index')
            ->with('success', 'Foto excluída permanentemente!');
    }

    /**
     * Fotos por projeto
     */
    public function porProjeto(Projeto $projeto)
    {
        $fotos = $projeto->fotos()
            ->with(['atividade', 'equipe', 'usuario'])
            ->withTrashed()
            ->latest('data_captura')
            ->paginate(20);

        return view('diario-obras.fotos.por-projeto', compact('fotos', 'projeto'));
    }

    /**
     * API para obter coordenadas das fotos de um projeto
     */
    public function coordenadasProjeto(Projeto $projeto)
    {
        $fotos = $projeto->fotos()
            ->comGeolocalizacao()
            ->select('id', 'titulo', 'latitude', 'longitude', 'categoria', 'data_captura')
            ->get();

        return response()->json($fotos);
    }

    /**
     * Sincronizar fotos offline
     */
    public function syncOfflinePhotos(Request $request)
    {
        try {
            $photos = $request->input('photos', []);

            if (empty($photos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma foto para sincronizar'
                ], 400);
            }

            $syncedCount = 0;
            $errors = [];

            foreach ($photos as $photoData) {
                try {
                    // Validar dados obrigatórios
                    if (empty($photoData['projeto']) || empty($photoData['categoria'])) {
                        $errors[] = 'Projeto e categoria são obrigatórios';
                        continue;
                    }

                    // Processar cada foto
                    foreach ($photoData['photos'] as $photo) {
                        $fotoObra = new FotoObra();

                        // Dados básicos
                        $fotoObra->projeto_id = $photoData['projeto'];
                        $fotoObra->categoria = $photoData['categoria'];
                        $fotoObra->descricao = $photoData['descricao'] ?? '';
                        $fotoObra->usuario_id = auth()->id();
                        $fotoObra->data_captura = $photoData['timestamp'] ?? now();

                        // Dados de localização
                        if (isset($photoData['location'])) {
                            $fotoObra->latitude = $photoData['location']['latitude'];
                            $fotoObra->longitude = $photoData['location']['longitude'];
                            $fotoObra->altitude = $photoData['location']['altitude'] ?? null;
                            $fotoObra->precisao = $photoData['location']['accuracy'] ?? null;
                        }

                        // Processar arquivo da foto
                        $base64 = $photo['data'];
                        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));

                        // Gerar nome único para o arquivo
                        $filename = 'offline_' . time() . '_' . Str::random(10) . '.jpg';
                        $path = 'fotos/' . $filename;

                        // Salvar arquivo
                        Storage::disk('public')->put($path, $imageData);

                        $fotoObra->caminho_arquivo = $path;
                        $fotoObra->nome_arquivo = $photo['name'];
                        $fotoObra->tamanho_arquivo = $photo['size'];
                        $fotoObra->tipo_arquivo = $photo['type'];

                        // Dados EXIF (se disponíveis)
                        $fotoObra->dados_exif = json_encode([
                            'width' => null,
                            'height' => null,
                            'orientation' => null,
                            'device_make' => 'Mobile Device',
                            'device_model' => 'Offline Camera'
                        ]);

                        $fotoObra->save();
                        $syncedCount++;
                    }

                } catch (\Exception $e) {
                    $errors[] = 'Erro ao processar foto: ' . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'synced' => $syncedCount,
                'errors' => $errors,
                'message' => "Sincronizadas {$syncedCount} fotos com sucesso!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter dados para sistema offline
     */
    public function getOfflineData()
    {
        try {
            // Buscar projetos ativos e concluídos (útil para fotos de obras finalizadas)
            $projetos = Projeto::whereIn('status', ['planejamento', 'em_andamento', 'concluido'])
                ->select('id', 'nome', 'descricao', 'status')
                ->orderBy('status')
                ->orderBy('nome')
                ->get();

            // Categorias do sistema (baseadas na validação do controller)
            $categorias = [
                ['id' => 'antes', 'nome' => 'Antes'],
                ['id' => 'progresso', 'nome' => 'Progresso'],
                ['id' => 'problema', 'nome' => 'Problema'],
                ['id' => 'solucao', 'nome' => 'Solução'],
                ['id' => 'final', 'nome' => 'Final'],
                ['id' => 'geral', 'nome' => 'Geral']
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'projetos' => $projetos,
                    'categorias' => $categorias
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados: ' . $e->getMessage()
            ], 500);
        }
    }
}
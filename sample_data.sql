-- Dados de exemplo para o sistema
-- Inserir dados na ordem correta respeitando as chaves estrangeiras

-- 1. Usuários
INSERT INTO users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) VALUES
(1, 'Administrador', 'admin@diarioobras.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NOW(), NOW());

-- 2. Funções
INSERT INTO funcoes (id, nome, descricao, created_at, updated_at) VALUES
(1, 'Engenheiro', 'Engenheiro responsável pela obra', NOW(), NOW()),
(2, 'Arquiteto', 'Arquiteto do projeto', NOW(), NOW()),
(3, 'Mestre de Obras', 'Mestre responsável pela execução', NOW(), NOW()),
(4, 'Pedreiro', 'Pedreiro da equipe', NOW(), NOW()),
(5, 'Eletricista', 'Eletricista da obra', NOW(), NOW());

-- 3. Lotações
INSERT INTO lotacoes (id, nome, descricao, created_at, updated_at) VALUES
(1, 'Obra Principal', 'Obra principal do projeto', NOW(), NOW()),
(2, 'Obra Secundária', 'Obra secundária', NOW(), NOW()),
(3, 'Escritório', 'Escritório da empresa', NOW(), NOW());

-- 4. Pessoas
INSERT INTO pessoas (id, nome, cpf, telefone, email, funcao_id, lotacao_id, created_at, updated_at) VALUES
(1, 'João Silva', '123.456.789-00', '(11) 99999-9999', 'joao@email.com', 1, 1, NOW(), NOW()),
(2, 'Maria Santos', '987.654.321-00', '(11) 88888-8888', 'maria@email.com', 2, 1, NOW(), NOW()),
(3, 'Pedro Costa', '456.789.123-00', '(11) 77777-7777', 'pedro@email.com', 3, 1, NOW(), NOW()),
(4, 'Ana Oliveira', '789.123.456-00', '(11) 66666-6666', 'ana@email.com', 4, 2, NOW(), NOW()),
(5, 'Carlos Lima', '321.654.987-00', '(11) 55555-5555', 'carlos@email.com', 5, 2, NOW(), NOW());

-- 5. Empresas
INSERT INTO empresas (id, nome, cnpj, endereco, telefone, email, created_at, updated_at) VALUES
(1, 'Construtora ABC Ltda', '12.345.678/0001-90', 'Rua das Obras, 123', '(11) 3333-3333', 'contato@construtoraabc.com', NOW(), NOW()),
(2, 'Engenharia XYZ S/A', '98.765.432/0001-10', 'Av. Construção, 456', '(11) 4444-4444', 'info@engenhariaxyz.com', NOW(), NOW()),
(3, 'Obras & Cia Ltda', '11.222.333/0001-44', 'Rua Projetos, 789', '(11) 5555-5555', 'obras@obrasecia.com', NOW(), NOW());

-- 6. Projetos
INSERT INTO projetos (id, nome, descricao, data_inicio, data_fim, status, valor_total, empresa_id, created_at, updated_at) VALUES
(1, 'Residencial Solar', 'Construção de residencial com sistema solar', '2025-01-01', '2025-12-31', 'em_andamento', 500000.00, 1, NOW(), NOW()),
(2, 'Comercial Plaza', 'Edifício comercial de 10 andares', '2025-02-01', '2025-11-30', 'em_andamento', 2000000.00, 2, NOW(), NOW()),
(3, 'Reforma Escolar', 'Reforma completa da escola municipal', '2025-03-01', '2025-10-31', 'planejamento', 300000.00, 3, NOW(), NOW());

-- 7. Contratos
INSERT INTO contratos (id, numero, descricao, valor, data_inicio, data_fim, status, projeto_id, created_at, updated_at) VALUES
(1, 'CTR-001', 'Contrato de construção do residencial', 500000.00, '2025-01-01', '2025-12-31', 'ativo', 1, NOW(), NOW()),
(2, 'CTR-002', 'Contrato de construção comercial', 2000000.00, '2025-02-01', '2025-11-30', 'ativo', 2, NOW(), NOW()),
(3, 'CTR-003', 'Contrato de reforma escolar', 300000.00, '2025-03-01', '2025-10-31', 'ativo', 3, NOW(), NOW());

-- 8. Equipe de Obras
INSERT INTO equipe_obras (id, projeto_id, pessoa_id, funcao_id, data_inicio, data_fim, status, created_at, updated_at) VALUES
(1, 1, 1, 1, '2025-01-01', '2025-12-31', 'ativo', NOW(), NOW()),
(2, 1, 2, 2, '2025-01-01', '2025-12-31', 'ativo', NOW(), NOW()),
(3, 2, 3, 3, '2025-02-01', '2025-11-30', 'ativo', NOW(), NOW()),
(4, 3, 4, 4, '2025-03-01', '2025-10-31', 'ativo', NOW(), NOW()),
(5, 3, 5, 5, '2025-03-01', '2025-10-31', 'ativo', NOW(), NOW());

-- 9. Relação Projeto-Empresa
INSERT INTO projeto_empresa (id, projeto_id, empresa_id, tipo_relacao, created_at, updated_at) VALUES
(1, 1, 1, 'construtora', NOW(), NOW()),
(2, 2, 2, 'construtora', NOW(), NOW()),
(3, 3, 3, 'construtora', NOW(), NOW());

-- Dados de exemplo para o sistema (estrutura correta)
-- Inserir dados na ordem correta respeitando as chaves estrangeiras

-- 1. Usuários (já existe, vamos usar o existente)
-- INSERT INTO users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) VALUES
-- (1, 'Administrador', 'admin@diarioobras.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NOW(), NOW());

-- 2. Funções (já existem, vamos usar as existentes)
-- INSERT INTO funcoes (id, nome, descricao, created_at, updated_at) VALUES
-- (1, 'Engenheiro', 'Engenheiro responsável pela obra', NOW(), NOW()),
-- (2, 'Arquiteto', 'Arquiteto do projeto', NOW(), NOW()),
-- (3, 'Mestre de Obras', 'Mestre responsável pela execução', NOW(), NOW()),
-- (4, 'Pedreiro', 'Pedreiro da equipe', NOW(), NOW()),
-- (5, 'Eletricista', 'Eletricista da obra', NOW(), NOW());

-- 3. Lotações (já existem, vamos usar as existentes)
-- INSERT INTO lotacoes (id, nome, descricao, created_at, updated_at) VALUES
-- (1, 'Obra Principal', 'Obra principal do projeto', NOW(), NOW()),
-- (2, 'Obra Secundária', 'Obra secundária', NOW(), NOW()),
-- (3, 'Escritório', 'Escritório da empresa', NOW(), NOW());

-- 4. Pessoas (estrutura correta)
INSERT INTO pessoas (id, nome, cpf, lotacao_id, funcao_id, status, status_validacao, tentativas_validacao, created_at, updated_at) VALUES
(1, 'João Silva', '123.456.789-00', 1, 1, 'ativo', 'validado', 0, NOW(), NOW()),
(2, 'Maria Santos', '987.654.321-00', 1, 2, 'ativo', 'validado', 0, NOW(), NOW()),
(3, 'Pedro Costa', '456.789.123-00', 1, 3, 'ativo', 'validado', 0, NOW(), NOW()),
(4, 'Ana Oliveira', '789.123.456-00', 2, 4, 'ativo', 'validado', 0, NOW(), NOW()),
(5, 'Carlos Lima', '321.654.987-00', 2, 5, 'ativo', 'validado', 0, NOW(), NOW());

-- 5. Empresas (estrutura correta)
INSERT INTO empresas (id, nome, razao_social, cnpj, email, telefone, cep, endereco, numero, bairro, cidade, estado, pais, ativo, created_by, created_at, updated_at) VALUES
(1, 'Construtora ABC Ltda', 'Construtora ABC Ltda', '12.345.678/0001-90', 'contato@construtoraabc.com', '(11) 3333-3333', '01234-567', 'Rua das Obras', '123', 'Centro', 'São Paulo', 'SP', 'Brasil', true, 1, NOW(), NOW()),
(2, 'Engenharia XYZ S/A', 'Engenharia XYZ S/A', '98.765.432/0001-10', 'info@engenhariaxyz.com', '(11) 4444-4444', '01234-567', 'Av. Construção', '456', 'Centro', 'São Paulo', 'SP', 'Brasil', true, 1, NOW(), NOW()),
(3, 'Obras & Cia Ltda', 'Obras & Cia Ltda', '11.222.333/0001-44', 'obras@obrasecia.com', '(11) 5555-5555', '01234-567', 'Rua Projetos', '789', 'Centro', 'São Paulo', 'SP', 'Brasil', true, 1, NOW(), NOW());

-- 6. Projetos (estrutura correta)
INSERT INTO projetos (id, nome, descricao, data_inicio, status, valor_total, empresa_id, created_at, updated_at) VALUES
(1, 'Residencial Solar', 'Construção de residencial com sistema solar', '2025-01-01', 'em_andamento', 500000.00, 1, NOW(), NOW()),
(2, 'Comercial Plaza', 'Edifício comercial de 10 andares', '2025-02-01', 'em_andamento', 2000000.00, 2, NOW(), NOW()),
(3, 'Reforma Escolar', 'Reforma completa da escola municipal', '2025-03-01', 'planejamento', 300000.00, 3, NOW(), NOW());

-- 7. Contratos (estrutura correta)
INSERT INTO contratos (id, numero, descricao, data_inicio, data_fim, status, projeto_id, fiscal_id, gestor_id, created_at, updated_at) VALUES
(1, 'CTR-001', 'Contrato de construção do residencial', '2025-01-01', '2025-12-31', 'ativo', 1, 1, 2, NOW(), NOW()),
(2, 'CTR-002', 'Contrato de construção comercial', '2025-02-01', '2025-11-30', 'ativo', 2, 2, 3, NOW(), NOW()),
(3, 'CTR-003', 'Contrato de reforma escolar', '2025-03-01', '2025-10-31', 'ativo', 3, 3, 4, NOW(), NOW());

-- 8. Equipe de Obras (estrutura correta)
INSERT INTO equipe_obras (id, projeto_id, pessoa_id, data_inicio, data_fim, status, created_at, updated_at) VALUES
(1, 1, 1, '2025-01-01', '2025-12-31', 'ativo', NOW(), NOW()),
(2, 1, 2, '2025-01-01', '2025-12-31', 'ativo', NOW(), NOW()),
(3, 2, 3, '2025-02-01', '2025-11-30', 'ativo', NOW(), NOW()),
(4, 3, 4, '2025-03-01', '2025-10-31', 'ativo', NOW(), NOW()),
(5, 3, 5, '2025-03-01', '2025-10-31', 'ativo', NOW(), NOW());

-- 9. Relação Projeto-Empresa (estrutura correta)
INSERT INTO projeto_empresa (id, projeto_id, empresa_id, created_at, updated_at) VALUES
(1, 1, 1, NOW(), NOW()),
(2, 2, 2, NOW(), NOW()),
(3, 3, 3, NOW(), NOW());

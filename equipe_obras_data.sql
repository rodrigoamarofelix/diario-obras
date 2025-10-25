-- Dados de exemplo para equipe_obras (estrutura correta)
INSERT INTO equipe_obras (id, projeto_id, pessoa_id, data_trabalho, hora_entrada, hora_saida, horas_trabalhadas, funcao, atividades_realizadas, presente, created_by, created_at, updated_at) VALUES
(1, 1, 1, '2025-01-01', '08:00:00', '17:00:00', 8, 'engenheiro', 'Supervisão geral da obra', true, 1, NOW(), NOW()),
(2, 1, 2, '2025-01-01', '08:00:00', '17:00:00', 8, 'arquiteto', 'Acompanhamento do projeto', true, 1, NOW(), NOW()),
(3, 2, 3, '2025-02-01', '08:00:00', '17:00:00', 8, 'supervisor', 'Supervisão da construção', true, 1, NOW(), NOW()),
(4, 3, 4, '2025-03-01', '08:00:00', '17:00:00', 8, 'pedreiro', 'Execução da reforma', true, 1, NOW(), NOW()),
(5, 3, 5, '2025-03-01', '08:00:00', '17:00:00', 8, 'eletricista', 'Instalações elétricas', true, 1, NOW(), NOW());

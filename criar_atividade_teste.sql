-- Script para criar uma atividade de teste no banco de dados

-- Primeiro, vamos pegar os IDs dos projetos e usuários disponíveis
-- Vou usar subqueries para pegar o primeiro projeto e o primeiro usuário disponível

INSERT INTO atividade_obras (
    projeto_id,
    data_atividade,
    titulo,
    descricao,
    tipo,
    status,
    hora_inicio,
    hora_fim,
    tempo_gasto_minutos,
    observacoes,
    problemas_encontrados,
    solucoes_aplicadas,
    responsavel_id,
    created_by,
    created_at,
    updated_at
)
SELECT 
    (SELECT id FROM projetos WHERE deleted_at IS NULL LIMIT 1) as projeto_id,
    CURRENT_DATE as data_atividade,
    'Instalação Elétrica - Sala Principal' as titulo,
    'Instalação da fiação elétrica completa para a sala principal, incluindo iluminação, tomadas e aparelhos de ar condicionado. A instalação será executada conforme projeto elétrico aprovado.' as descricao,
    'construcao' as tipo,
    'em_andamento' as status,
    '08:00' as hora_inicio,
    '17:00' as hora_fim,
    540 as tempo_gasto_minutos,
    'Trabalho iniciado pela manhã. Toda a equipe está trabalhando conforme o planejado.' as observacoes,
    'Nenhum problema encontrado até o momento.' as problemas_encontrados,
    '-' as solucoes_aplicadas,
    (SELECT id FROM users WHERE profile != 'pending' LIMIT 1) as responsavel_id,
    (SELECT id FROM users WHERE profile != 'pending' LIMIT 1) as created_by,
    NOW() as created_at,
    NOW() as updated_at
WHERE NOT EXISTS (
    SELECT 1 FROM atividade_obras WHERE titulo = 'Instalação Elétrica - Sala Principal'
);

-- Verificar se foi criado
SELECT * FROM atividade_obras WHERE titulo = 'Instalação Elétrica - Sala Principal';


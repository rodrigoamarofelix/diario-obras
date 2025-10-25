// === CORREÇÃO DE CONFLITOS JAVASCRIPT - SGC ===

// Verificar e corrigir conflitos do Alpine.js
(function () {
    'use strict';

    // Verificar se há múltiplas instâncias do Alpine
    if (window.Alpine && window.Alpine.version) {
        console.log('✅ Alpine.js detectado - Versão:', window.Alpine.version);
    }

    // Verificar se há conflitos com Livewire
    if (window.Livewire) {
        console.log('✅ Livewire detectado');
    }

    // Função para limpar instâncias duplicadas
    function limparInstanciasDuplicadas() {
        // Limpar event listeners duplicados
        const eventos = ['livewire:load', 'livewire:init', 'livewire:update', 'livewire:updated'];
        eventos.forEach(evento => {
            // Remover listeners antigos se existirem
            if (window.livewireListeners && window.livewireListeners[evento]) {
                window.livewireListeners[evento].forEach(listener => {
                    document.removeEventListener(evento, listener);
                });
                delete window.livewireListeners[evento];
            }
        });
    }

    // Executar limpeza quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', limparInstanciasDuplicadas);
    } else {
        limparInstanciasDuplicadas();
    }

    // Função para debounce (evitar execuções múltiplas)
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Função para throttle (limitar execuções)
    function throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Expor funções globalmente
    window.SGCUtils = {
        debounce: debounce,
        throttle: throttle,
        limparInstanciasDuplicadas: limparInstanciasDuplicadas
    };

    console.log('✅ Utilitários JavaScript do SGC carregados');
})();

// === CORREÇÃO ESPECÍFICA PARA GRÁFICOS ===

// Variável global para controlar instâncias de gráficos
window.chartInstances = window.chartInstances || {};

// Função para destruir gráficos existentes de forma mais robusta
function destruirGraficos() {
    // Destruir gráficos conhecidos pelos IDs dos canvas
    const canvasIds = ['contratosStatusChart', 'medicoesMesChart', 'pagamentosMesChart', 'usuariosMesChart'];

    canvasIds.forEach(canvasId => {
        const canvas = document.getElementById(canvasId);
        if (canvas && typeof Chart !== 'undefined') {
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                try {
                    existingChart.destroy();
                    console.log(`🗑️ Gráfico ${canvasId} destruído via Chart.getChart()`);
                } catch (error) {
                    console.warn(`⚠️ Erro ao destruir gráfico ${canvasId}:`, error);
                }
            }
        }
    });

    // Destruir gráficos armazenados no objeto global
    if (window.chartInstances) {
        Object.keys(window.chartInstances).forEach(key => {
            if (window.chartInstances[key] && typeof window.chartInstances[key].destroy === 'function') {
                try {
                    window.chartInstances[key].destroy();
                    console.log(`🗑️ Gráfico ${key} destruído via chartInstances`);
                } catch (error) {
                    console.warn(`⚠️ Erro ao destruir gráfico ${key}:`, error);
                }
            }
        });
        window.chartInstances = {};
    }
}

// Função para verificar se Chart.js está disponível
function verificarChartJS() {
    if (typeof Chart === 'undefined') {
        console.warn('⚠️ Chart.js não está disponível');
        return false;
    }
    console.log('✅ Chart.js disponível - Versão:', Chart.version);
    return true;
}

// Função para renderizar gráficos com proteção contra conflitos
function renderizarGraficosSeguro(dados) {
    if (!verificarChartJS()) {
        console.error('❌ Chart.js não disponível, não é possível renderizar gráficos');
        return;
    }

    // Destruir gráficos existentes primeiro
    destruirGraficos();

    // Aguardar um pouco para garantir que o DOM está pronto
    setTimeout(() => {
        try {
            // Aqui você pode chamar sua função de renderização de gráficos
            console.log('📊 Iniciando renderização segura dos gráficos...');
            console.log('📊 Dados recebidos:', dados);

            // Sua lógica de renderização aqui...

        } catch (error) {
            console.error('❌ Erro ao renderizar gráficos:', error);
        }
    }, 100);
}

// Expor funções globalmente
window.renderizarGraficosSeguro = renderizarGraficosSeguro;
window.destruirGraficos = destruirGraficos;

console.log('✅ Sistema de gráficos seguro carregado');

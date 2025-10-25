// === LAZY LOADING SERVICE - SGC ===

class LazyLoadingService {
    constructor() {
        this.observers = new Map();
        this.loadedElements = new Set();
        this.init();
    }

    init() {
        // Configurar Intersection Observer
        this.setupIntersectionObserver();

        // Configurar lazy loading para imagens
        this.setupImageLazyLoading();

        // Configurar lazy loading para tabelas
        this.setupTableLazyLoading();

        // Configurar lazy loading para componentes Livewire
        this.setupLivewireLazyLoading();

        console.log('✅ Lazy Loading Service inicializado');
    }

    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        };

        this.intersectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.handleIntersection(entry.target);
                }
            });
        }, options);
    }

    setupImageLazyLoading() {
        // Lazy loading para imagens com data-src
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            this.intersectionObserver.observe(img);
        });
    }

    setupTableLazyLoading() {
        // Lazy loading para tabelas com paginação
        const tables = document.querySelectorAll('.lazy-table');
        tables.forEach(table => {
            this.intersectionObserver.observe(table);
        });
    }

    setupLivewireLazyLoading() {
        // Lazy loading para componentes Livewire
        const livewireComponents = document.querySelectorAll('[wire\\:id]');
        livewireComponents.forEach(component => {
            if (component.dataset.lazy === 'true') {
                this.intersectionObserver.observe(component);
            }
        });
    }

    handleIntersection(element) {
        if (this.loadedElements.has(element)) {
            return;
        }

        this.loadedElements.add(element);

        if (element.tagName === 'IMG' && element.dataset.src) {
            this.loadImage(element);
        } else if (element.classList.contains('lazy-table')) {
            this.loadTable(element);
        } else if (element.dataset.lazy === 'true') {
            this.loadLivewireComponent(element);
        }
    }

    loadImage(img) {
        return new Promise((resolve, reject) => {
            const imageLoader = new Image();

            imageLoader.onload = () => {
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                img.classList.add('loaded');
                resolve(img);
            };

            imageLoader.onerror = () => {
                img.classList.add('error');
                reject(new Error('Erro ao carregar imagem'));
            };

            imageLoader.src = img.dataset.src;
        });
    }

    loadTable(table) {
        const url = table.dataset.url;
        const page = table.dataset.page || 1;

        if (!url) return;

        fetch(`${url}?page=${page}&lazy=true`)
            .then(response => response.json())
            .then(data => {
                this.appendTableData(table, data);
            })
            .catch(error => {
                console.error('Erro ao carregar dados da tabela:', error);
            });
    }

    loadLivewireComponent(component) {
        const wireId = component.getAttribute('wire:id');

        if (!wireId) return;

        // Disparar evento para o Livewire carregar o componente
        component.dispatchEvent(new CustomEvent('livewire:load', {
            detail: { wireId }
        }));
    }

    appendTableData(table, data) {
        const tbody = table.querySelector('tbody');
        if (!tbody || !data.rows) return;

        data.rows.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = row;
            tbody.appendChild(tr);
        });

        // Atualizar informações de paginação
        this.updatePaginationInfo(table, data);
    }

    updatePaginationInfo(table, data) {
        const paginationInfo = table.querySelector('.pagination-info');
        if (paginationInfo && data.pagination) {
            paginationInfo.textContent = `Mostrando ${data.pagination.from}-${data.pagination.to} de ${data.pagination.total}`;
        }
    }

    // Métodos públicos para controle manual
    loadMore(tableElement) {
        const currentPage = parseInt(tableElement.dataset.page) || 1;
        tableElement.dataset.page = currentPage + 1;
        this.loadTable(tableElement);
    }

    refreshElement(element) {
        this.loadedElements.delete(element);
        this.handleIntersection(element);
    }

    // Lazy loading para gráficos
    loadChart(canvasElement) {
        if (this.loadedElements.has(canvasElement)) {
            return;
        }

        this.loadedElements.add(canvasElement);

        const chartData = canvasElement.dataset.chartData;
        if (!chartData) return;

        try {
            const data = JSON.parse(chartData);
            this.renderChart(canvasElement, data);
        } catch (error) {
            console.error('Erro ao carregar dados do gráfico:', error);
        }
    }

    renderChart(canvas, data) {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js não está disponível');
            return;
        }

        new Chart(canvas, {
            type: data.type || 'bar',
            data: data.data,
            options: data.options || {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Lazy loading para modais
    loadModal(modalElement) {
        const contentUrl = modalElement.dataset.contentUrl;
        if (!contentUrl || this.loadedElements.has(modalElement)) {
            return;
        }

        this.loadedElements.add(modalElement);

        fetch(contentUrl)
            .then(response => response.text())
            .then(html => {
                const content = modalElement.querySelector('.modal-content');
                if (content) {
                    content.innerHTML = html;
                }
            })
            .catch(error => {
                console.error('Erro ao carregar conteúdo do modal:', error);
            });
    }

    // Método para limpar cache
    clearCache() {
        this.loadedElements.clear();
        console.log('✅ Cache do Lazy Loading limpo');
    }

    // Método para obter estatísticas
    getStats() {
        return {
            totalElements: this.loadedElements.size,
            observers: this.observers.size,
            intersectionObserver: !!this.intersectionObserver
        };
    }
}

// Inicializar o serviço quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function () {
    window.lazyLoadingService = new LazyLoadingService();

    // Expor métodos globalmente
    window.loadMore = function (tableElement) {
        window.lazyLoadingService.loadMore(tableElement);
    };

    window.refreshLazyElement = function (element) {
        window.lazyLoadingService.refreshElement(element);
    };

    window.loadChart = function (canvasElement) {
        window.lazyLoadingService.loadChart(canvasElement);
    };

    window.loadModal = function (modalElement) {
        window.lazyLoadingService.loadModal(modalElement);
    };

    console.log('✅ Lazy Loading Service carregado globalmente');
});

// Lazy loading para componentes específicos do SGC
class SGCLazyLoading {
    constructor() {
        this.init();
    }

    init() {
        // Lazy loading para dashboard cards
        this.setupDashboardCards();

        // Lazy loading para workflow items
        this.setupWorkflowItems();

        // Lazy loading para notificações
        this.setupNotifications();

        // Lazy loading para relatórios
        this.setupReports();
    }

    setupDashboardCards() {
        const cards = document.querySelectorAll('.dashboard-card[data-lazy="true"]');
        cards.forEach(card => {
            if (window.lazyLoadingService) {
                window.lazyLoadingService.intersectionObserver.observe(card);
            }
        });
    }

    setupWorkflowItems() {
        const workflowItems = document.querySelectorAll('.workflow-item[data-lazy="true"]');
        workflowItems.forEach(item => {
            if (window.lazyLoadingService) {
                window.lazyLoadingService.intersectionObserver.observe(item);
            }
        });
    }

    setupNotifications() {
        const notifications = document.querySelectorAll('.notification-item[data-lazy="true"]');
        notifications.forEach(notification => {
            if (window.lazyLoadingService) {
                window.lazyLoadingService.intersectionObserver.observe(notification);
            }
        });
    }

    setupReports() {
        const reports = document.querySelectorAll('.report-item[data-lazy="true"]');
        reports.forEach(report => {
            if (window.lazyLoadingService) {
                window.lazyLoadingService.intersectionObserver.observe(report);
            }
        });
    }
}

// Inicializar lazy loading específico do SGC
document.addEventListener('DOMContentLoaded', function () {
    window.sgcLazyLoading = new SGCLazyLoading();
    console.log('✅ SGC Lazy Loading inicializado');
});




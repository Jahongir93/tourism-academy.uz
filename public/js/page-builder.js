class PageBuilder {
    constructor(config = {}) {
        this.config = {
            canvasId: config.canvasId || 'dropZone',
            stylePanelId: config.stylePanelId || 'stylePanel',
            pageId: config.pageId || null,
            language: config.language || 'uz',
            ...config
        };
        
        this.canvas = document.getElementById(this.config.canvasId);
        this.stylePanel = document.getElementById(this.config.stylePanelId);
        this.selectedElement = null;
        this.history = [];
        this.historyIndex = -1;
        this.device = 'desktop';
        this.isDragging = false;
        
        this.init();
    }
    
    init() {
        this.setupDragAndDrop();
        this.setupBlocksDragAndDrop();
        this.setupEventListeners();
        this.setupKeyboardShortcuts();
        this.setupImageUpload();
        this.loadPageData();
        this.initializeAnimations();
        this.initializeCharts();
    }

    setupBlocksDragAndDrop() {
        document.querySelectorAll('.block-item').forEach(item => {
            item.draggable = true;

            item.addEventListener('dragstart', (e) => {
                this.isDragging = true;
                const blockType = item.dataset.block;
                e.dataTransfer.effectAllowed = 'copy';
                e.dataTransfer.setData('blockType', blockType);
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', (e) => {
                this.isDragging = false;
                item.classList.remove('dragging');
            });

            item.addEventListener('click', () => {
                const blockType = item.dataset.block;
                const block = this.createBlock(blockType);
                this.canvas.appendChild(block);
                this.saveHistory();
            });
        });
    }

    createBlock(type) {
        const blocks = this.getBlockTemplates();
        const blockContent = blocks[type];

        if (!blockContent) {
            console.warn('Block not found:', type);
            return document.createElement('div');
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'builder-element builder-block';
        wrapper.dataset.elementType = 'block';
        wrapper.dataset.blockType = type;
        wrapper.dataset.elementId = this.generateId();

        const toolbar = this.createElementToolbar();
        wrapper.appendChild(toolbar);

        const content = document.createElement('div');
        content.className = 'element-content';
        content.innerHTML = blockContent;
        wrapper.appendChild(content);

        this.attachElementEvents(wrapper);
        this.initColumnDropzones(content);

        return wrapper;
    }

    getBlockTemplates() {
        return {
            'hero-1': `
                <section class="hero-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="container text-center py-5">
                        <h1 class="display-4 fw-bold mb-4" contenteditable="true">Saytimizga xush kelibsiz!</h1>
                        <p class="lead mb-4" contenteditable="true">Professional xizmatlar va sifatli mahsulotlar bilan tanishing</p>
                        <div>
                            <a href="#" class="btn btn-light btn-lg me-2" contenteditable="true">Batafsil</a>
                            <a href="#" class="btn btn-outline-light btn-lg" contenteditable="true">Bog'lanish</a>
                        </div>
                    </div>
                </section>
            `,
            'hero-2': `
                <section class="hero-section py-5" style="background: #f8f9fa;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="display-5 fw-bold mb-3" contenteditable="true">Biznes uchun eng yaxshi yechim</h1>
                                <p class="lead text-muted mb-4" contenteditable="true">Zamonaviy texnologiyalar va professional jamoa bilan muvaffaqiyatga erishing.</p>
                                <a href="#" class="btn btn-primary btn-lg" contenteditable="true">Boshlash</a>
                            </div>
                            <div class="col-md-6">
                                <img src="https://via.placeholder.com/500x400" class="img-fluid rounded editable-image" alt="Hero image">
                            </div>
                        </div>
                    </div>
                </section>
            `,
            'services-1': `
                <section class="services-section py-5">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold" contenteditable="true">Bizning xizmatlar</h2>
                            <p class="text-muted" contenteditable="true">Professional va sifatli xizmatlar</p>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center p-4">
                                    <div class="mb-3"><i class="fas fa-rocket fa-3x text-primary"></i></div>
                                    <h5 contenteditable="true">Tez rivojlanish</h5>
                                    <p class="text-muted" contenteditable="true">Loyihangizni tez va sifatli amalga oshiramiz</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4">
                                    <div class="mb-3"><i class="fas fa-shield-alt fa-3x text-success"></i></div>
                                    <h5 contenteditable="true">Xavfsizlik</h5>
                                    <p class="text-muted" contenteditable="true">Ma'lumotlaringiz xavfsizligini kafolatlaymiz</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4">
                                    <div class="mb-3"><i class="fas fa-headset fa-3x text-info"></i></div>
                                    <h5 contenteditable="true">Qo'llab-quvvatlash</h5>
                                    <p class="text-muted" contenteditable="true">24/7 texnik yordam xizmati</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            `,
            'features-1': `
                <section class="features-section py-5" style="background: #f8f9fa;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <img src="https://via.placeholder.com/500x400" class="img-fluid rounded editable-image" alt="Features">
                            </div>
                            <div class="col-md-6">
                                <h2 class="fw-bold mb-4" contenteditable="true">Nima uchun bizni tanlashingiz kerak?</h2>
                                <div class="d-flex mb-3">
                                    <div class="me-3"><i class="fas fa-check-circle text-success fa-2x"></i></div>
                                    <div>
                                        <h6 contenteditable="true">Sifatli xizmat</h6>
                                        <p class="text-muted mb-0" contenteditable="true">Eng yuqori sifat standartlari</p>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="me-3"><i class="fas fa-check-circle text-success fa-2x"></i></div>
                                    <div>
                                        <h6 contenteditable="true">Tejamkorlik</h6>
                                        <p class="text-muted mb-0" contenteditable="true">Qulay narxlar va maxsus takliflar</p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="me-3"><i class="fas fa-check-circle text-success fa-2x"></i></div>
                                    <div>
                                        <h6 contenteditable="true">Tajriba</h6>
                                        <p class="text-muted mb-0" contenteditable="true">10 yillik tajriba</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            `,
            'contact-1': `
                <section class="contact-section py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h2 class="fw-bold mb-4" contenteditable="true">Biz bilan bog'laning</h2>
                                <p class="text-muted mb-4" contenteditable="true">Savollaringiz bo'lsa, biz bilan bog'laning. Tez orada javob beramiz.</p>
                                <div class="d-flex mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                                    <span contenteditable="true">Toshkent sh., Amir Temur ko'chasi, 1-uy</span>
                                </div>
                                <div class="d-flex mb-3">
                                    <i class="fas fa-phone text-primary me-3 mt-1"></i>
                                    <span contenteditable="true">+998 90 123 45 67</span>
                                </div>
                                <div class="d-flex">
                                    <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                                    <span contenteditable="true">info@example.com</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form class="p-4 rounded" style="background: #f8f9fa;">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" placeholder="Ismingiz">
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" rows="4" placeholder="Xabaringiz"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Yuborish</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            `,
            'cta-1': `
                <section class="cta-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="container text-center">
                        <h2 class="fw-bold mb-3" contenteditable="true">Tayyor boshlashga?</h2>
                        <p class="lead mb-4" contenteditable="true">Biz bilan bog'laning va loyihangizni bugun boshlang!</p>
                        <a href="#" class="btn btn-light btn-lg" contenteditable="true">Bog'lanish</a>
                    </div>
                </section>
            `,
            'team-1': `
                <section class="team-section py-5">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold" contenteditable="true">Bizning jamoa</h2>
                            <p class="text-muted" contenteditable="true">Professional va tajribali mutaxassislar</p>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <img src="https://via.placeholder.com/150" class="rounded-circle mb-3 editable-image" style="width: 120px; height: 120px; object-fit: cover;">
                                    <h6 contenteditable="true">Ism Familiya</h6>
                                    <small class="text-muted" contenteditable="true">Direktor</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <img src="https://via.placeholder.com/150" class="rounded-circle mb-3 editable-image" style="width: 120px; height: 120px; object-fit: cover;">
                                    <h6 contenteditable="true">Ism Familiya</h6>
                                    <small class="text-muted" contenteditable="true">Menejer</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <img src="https://via.placeholder.com/150" class="rounded-circle mb-3 editable-image" style="width: 120px; height: 120px; object-fit: cover;">
                                    <h6 contenteditable="true">Ism Familiya</h6>
                                    <small class="text-muted" contenteditable="true">Dasturchi</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <img src="https://via.placeholder.com/150" class="rounded-circle mb-3 editable-image" style="width: 120px; height: 120px; object-fit: cover;">
                                    <h6 contenteditable="true">Ism Familiya</h6>
                                    <small class="text-muted" contenteditable="true">Dizayner</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            `,
            'testimonials-1': `
                <section class="testimonials-section py-5" style="background: #f8f9fa;">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold" contenteditable="true">Mijozlar fikrlari</h2>
                            <p class="text-muted" contenteditable="true">Bizning xizmatlarimiz haqida</p>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <img src="https://via.placeholder.com/80" class="rounded-circle mb-3 editable-image" style="width: 60px; height: 60px; object-fit: cover;">
                                        <p class="fst-italic mb-3" contenteditable="true">"Juda ajoyib xizmat! Tavsiya qilaman."</p>
                                        <h6 class="mb-0" contenteditable="true">Ism Familiya</h6>
                                        <small class="text-muted" contenteditable="true">Kompaniya</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <img src="https://via.placeholder.com/80" class="rounded-circle mb-3 editable-image" style="width: 60px; height: 60px; object-fit: cover;">
                                        <p class="fst-italic mb-3" contenteditable="true">"Professional jamoa, sifatli ish!"</p>
                                        <h6 class="mb-0" contenteditable="true">Ism Familiya</h6>
                                        <small class="text-muted" contenteditable="true">Kompaniya</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <img src="https://via.placeholder.com/80" class="rounded-circle mb-3 editable-image" style="width: 60px; height: 60px; object-fit: cover;">
                                        <p class="fst-italic mb-3" contenteditable="true">"Eng yaxshi tanlov! Rahmat!"</p>
                                        <h6 class="mb-0" contenteditable="true">Ism Familiya</h6>
                                        <small class="text-muted" contenteditable="true">Kompaniya</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            `
        };
    }

    setupImageUpload() {
        // Add click handler for editable images
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('editable-image')) {
                this.openImageUploader(e.target);
            }
        });
    }

    openImageUploader(imgElement) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';

        input.onchange = async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('/cms/pages/builder/upload-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    imgElement.src = data.url;
                    this.saveHistory();
                    this.showNotification('Rasm yuklandi!', 'success');
                } else {
                    this.showNotification('Rasmni yuklashda xatolik', 'danger');
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.showNotification('Rasmni yuklashda xatolik', 'danger');
            }
        };

        input.click();
    }
    
    setupDragAndDrop() {
        // Canvas sortable
        this.sortable = new Sortable(this.canvas, {
            group: 'shared',
            animation: 150,
            ghostClass: 'dragging',
            handle: '.element-handle',
            onEnd: (evt) => {
                this.saveHistory();
                this.updateElementPositions();
            }
        });
        
        // Make element buttons draggable
        document.querySelectorAll('.element-item').forEach(item => {
            item.draggable = true;
            
            item.addEventListener('dragstart', (e) => {
                this.isDragging = true;
                const elementType = item.dataset.element;
                e.dataTransfer.effectAllowed = 'copy';
                e.dataTransfer.setData('elementType', elementType);
                item.classList.add('dragging');
            });
            
            item.addEventListener('dragend', (e) => {
                this.isDragging = false;
                item.classList.remove('dragging');
            });
        });
        
        // Canvas drop events
        this.canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            
            if (this.isDragging) {
                const afterElement = this.getDragAfterElement(this.canvas, e.clientY);
                const draggingElement = document.querySelector('.dragging-placeholder');
                
                if (!draggingElement) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'dragging-placeholder';
                    placeholder.innerHTML = '<div class="placeholder-content">Drop element here</div>';
                    
                    if (afterElement == null) {
                        this.canvas.appendChild(placeholder);
                    } else {
                        this.canvas.insertBefore(placeholder, afterElement);
                    }
                }
            }
        });
        
        this.canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            const elementType = e.dataTransfer.getData('elementType');
            
            if (elementType) {
                const placeholder = document.querySelector('.dragging-placeholder');
                if (placeholder) {
                    const newElement = this.createElement(elementType);
                    placeholder.replaceWith(newElement);
                } else {
                    const afterElement = this.getDragAfterElement(this.canvas, e.clientY);
                    const newElement = this.createElement(elementType);
                    
                    if (afterElement == null) {
                        this.canvas.appendChild(newElement);
                    } else {
                        this.canvas.insertBefore(newElement, afterElement);
                    }
                }
                
                this.saveHistory();
            }
        });
        
        this.canvas.addEventListener('dragleave', (e) => {
            if (e.target === this.canvas) {
                const placeholder = document.querySelector('.dragging-placeholder');
                if (placeholder) {
                    placeholder.remove();
                }
            }
        });
    }
    
    getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.builder-element:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
    
    createElement(type) {
        const element = document.createElement('div');
        element.className = 'builder-element';
        element.dataset.elementType = type;
        element.dataset.elementId = this.generateId();
        
        const toolbar = this.createElementToolbar();
        const content = this.createElementContent(type);
        
        element.appendChild(toolbar);
        element.appendChild(content);
        
        this.attachElementEvents(element);
        
        return element;
    }
    
    createElementToolbar() {
        const toolbar = document.createElement('div');
        toolbar.className = 'element-toolbar';
        toolbar.innerHTML = `
            <div class="element-handle">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <div class="element-actions">
                <button class="btn-element-action" data-action="edit" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-element-action" data-action="duplicate" title="Duplicate">
                    <i class="fas fa-copy"></i>
                </button>
                <button class="btn-element-action" data-action="moveUp" title="Move Up">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button class="btn-element-action" data-action="moveDown" title="Move Down">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button class="btn-element-action text-danger" data-action="delete" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        toolbar.querySelectorAll('.btn-element-action').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const action = btn.dataset.action;
                const element = btn.closest('.builder-element');
                this.handleElementAction(action, element);
            });
        });
        
        return toolbar;
    }
    
    createElementContent(type) {
        const content = document.createElement('div');
        content.className = 'element-content';
        
        const templates = {
            text: '<p contenteditable="true" class="editable-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
            heading: '<h2 contenteditable="true" class="editable-heading">Heading Text</h2>',
            image: '<img src="https://via.placeholder.com/600x400" alt="Placeholder" class="img-fluid editable-image">',
            video: '<div class="video-wrapper"><iframe width="100%" height="315" src="" frameborder="0" allowfullscreen></iframe></div>',
            button: '<button class="btn btn-primary editable-button" contenteditable="true">Click Me</button>',
            divider: '<hr class="element-divider">',
            spacer: '<div class="element-spacer" style="height: 50px;"></div>',
            icon: '<i class="fas fa-star fa-3x element-icon"></i>',
            social: `
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                </div>
            `,
            quote: `
                <blockquote class="blockquote">
                    <p contenteditable="true">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <footer class="blockquote-footer" contenteditable="true">Someone famous</footer>
                </blockquote>
            `,
            accordion: `
                <div class="accordion" id="accordion-${this.generateId()}">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                Accordion Item #1
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show">
                            <div class="accordion-body" contenteditable="true">
                                Content for accordion item 1
                            </div>
                        </div>
                    </div>
                </div>
            `,
            tabs: `
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Tab 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab2">Tab 2</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane fade show active">
                        <p contenteditable="true">Content for tab 1</p>
                    </div>
                    <div id="tab2" class="tab-pane fade">
                        <p contenteditable="true">Content for tab 2</p>
                    </div>
                </div>
            `,
            progressbar: `
                <div class="progress-wrapper">
                    <label class="progress-label" contenteditable="true">Progress: 75%</label>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            `,
            countdown: `
                <div class="countdown-timer" data-target="${this.getFutureDate()}">
                    <div class="countdown-item">
                        <span class="countdown-value" data-days>00</span>
                        <span class="countdown-label">Days</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-value" data-hours>00</span>
                        <span class="countdown-label">Hours</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-value" data-minutes>00</span>
                        <span class="countdown-label">Minutes</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-value" data-seconds>00</span>
                        <span class="countdown-label">Seconds</span>
                    </div>
                </div>
            `,
            chart: `
                <div class="chart-container">
                    <canvas class="element-chart" width="400" height="200"></canvas>
                </div>
            `,
            row: `
                <div class="row">
                    <div class="col-md-6 column-dropzone" data-column="1">
                        <div class="column-placeholder">Column 1</div>
                    </div>
                    <div class="col-md-6 column-dropzone" data-column="2">
                        <div class="column-placeholder">Column 2</div>
                    </div>
                </div>
            `,
            row1: `
                <div class="row">
                    <div class="col-12 column-dropzone" data-column="1">
                        <div class="column-placeholder">1 ustun (100%)</div>
                    </div>
                </div>
            `,
            row2: `
                <div class="row">
                    <div class="col-md-6 column-dropzone" data-column="1">
                        <div class="column-placeholder">Ustun 1 (50%)</div>
                    </div>
                    <div class="col-md-6 column-dropzone" data-column="2">
                        <div class="column-placeholder">Ustun 2 (50%)</div>
                    </div>
                </div>
            `,
            row3: `
                <div class="row">
                    <div class="col-md-4 column-dropzone" data-column="1">
                        <div class="column-placeholder">Ustun 1</div>
                    </div>
                    <div class="col-md-4 column-dropzone" data-column="2">
                        <div class="column-placeholder">Ustun 2</div>
                    </div>
                    <div class="col-md-4 column-dropzone" data-column="3">
                        <div class="column-placeholder">Ustun 3</div>
                    </div>
                </div>
            `,
            row4: `
                <div class="row">
                    <div class="col-md-3 column-dropzone" data-column="1">
                        <div class="column-placeholder">1</div>
                    </div>
                    <div class="col-md-3 column-dropzone" data-column="2">
                        <div class="column-placeholder">2</div>
                    </div>
                    <div class="col-md-3 column-dropzone" data-column="3">
                        <div class="column-placeholder">3</div>
                    </div>
                    <div class="col-md-3 column-dropzone" data-column="4">
                        <div class="column-placeholder">4</div>
                    </div>
                </div>
            `,
            'row-sidebar-left': `
                <div class="row">
                    <div class="col-md-3 column-dropzone" data-column="sidebar">
                        <div class="column-placeholder">Sidebar</div>
                    </div>
                    <div class="col-md-9 column-dropzone" data-column="main">
                        <div class="column-placeholder">Asosiy kontent</div>
                    </div>
                </div>
            `,
            'row-sidebar-right': `
                <div class="row">
                    <div class="col-md-9 column-dropzone" data-column="main">
                        <div class="column-placeholder">Asosiy kontent</div>
                    </div>
                    <div class="col-md-3 column-dropzone" data-column="sidebar">
                        <div class="column-placeholder">Sidebar</div>
                    </div>
                </div>
            `,
            section: `
                <section class="py-5 section-wrapper" style="background: #f8f9fa;">
                    <div class="container">
                        <div class="section-dropzone column-dropzone" style="min-height: 200px;">
                            <div class="column-placeholder">Bo'lim ichiga elementlarni tashlang</div>
                        </div>
                    </div>
                </section>
            `,
            container: `
                <div class="container py-4">
                    <div class="container-dropzone column-dropzone" style="min-height: 150px; border: 2px dashed #dee2e6; border-radius: 8px;">
                        <div class="column-placeholder">Konteyner</div>
                    </div>
                </div>
            `,
            column: `
                <div class="col-md-12 column-dropzone">
                    <div class="column-placeholder">Drop elements here</div>
                </div>
            `,
            card: `
                <div class="card shadow-sm">
                    <img src="https://via.placeholder.com/400x200" class="card-img-top editable-image" alt="Card image">
                    <div class="card-body">
                        <h5 class="card-title" contenteditable="true">Kartochka sarlavhasi</h5>
                        <p class="card-text" contenteditable="true">Bu yerga kartochka matnini yozing. Mahsulot yoki xizmat haqida qisqacha ma'lumot.</p>
                        <a href="#" class="btn btn-primary" contenteditable="true">Batafsil</a>
                    </div>
                </div>
            `,
            testimonial: `
                <div class="testimonial-card text-center p-4" style="background: #f8f9fa; border-radius: 15px;">
                    <img src="https://via.placeholder.com/100" class="rounded-circle mb-3 editable-image" alt="Avatar" style="width: 80px; height: 80px; object-fit: cover;">
                    <p class="mb-3 fst-italic" contenteditable="true">"Bu juda ajoyib xizmat! Men juda mamnunman va barcha do'stlarimga tavsiya qilaman."</p>
                    <h5 class="mb-1" contenteditable="true">Ism Familiya</h5>
                    <small class="text-muted" contenteditable="true">Lavozim, Kompaniya</small>
                    <div class="mt-2">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                </div>
            `,
            team: `
                <div class="team-member text-center">
                    <img src="https://via.placeholder.com/200" class="rounded-circle mb-3 editable-image" alt="Team member" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5 contenteditable="true">Ism Familiya</h5>
                    <p class="text-muted" contenteditable="true">Lavozim</p>
                    <div class="social-links">
                        <a href="#" class="text-primary me-2"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-info me-2"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-secondary"><i class="fas fa-envelope fa-lg"></i></a>
                    </div>
                </div>
            `,
            pricing: `
                <div class="pricing-card text-center p-4" style="background: #fff; border: 2px solid #007bff; border-radius: 15px;">
                    <h4 class="mb-3" contenteditable="true">Standart</h4>
                    <div class="price mb-3">
                        <span class="h1 fw-bold" contenteditable="true">$29</span>
                        <span class="text-muted">/oy</span>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2" contenteditable="true"><i class="fas fa-check text-success me-2"></i>10 GB saqlash</li>
                        <li class="mb-2" contenteditable="true"><i class="fas fa-check text-success me-2"></i>Email qo'llab-quvvatlash</li>
                        <li class="mb-2" contenteditable="true"><i class="fas fa-check text-success me-2"></i>API kirish</li>
                        <li class="mb-2 text-muted" contenteditable="true"><i class="fas fa-times me-2"></i>Premium xususiyatlar</li>
                    </ul>
                    <button class="btn btn-primary btn-lg w-100" contenteditable="true">Tanlash</button>
                </div>
            `,
            gallery: `
                <div class="gallery-grid">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/300x200" class="img-fluid rounded editable-image" alt="Gallery 1">
                        </div>
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/300x200" class="img-fluid rounded editable-image" alt="Gallery 2">
                        </div>
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/300x200" class="img-fluid rounded editable-image" alt="Gallery 3">
                        </div>
                    </div>
                </div>
            `,
            faq: `
                <div class="faq-section">
                    <div class="accordion" id="faq-${this.generateId()}">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" contenteditable="false">
                                    <span contenteditable="true">Savol 1: Bu qanday ishlaydi?</span>
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show">
                                <div class="accordion-body" contenteditable="true">
                                    Bu yerda savol javobini yozing. Foydalanuvchilar uchun tushunarli va qisqa javob bering.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <span contenteditable="true">Savol 2: Narxi qancha?</span>
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse">
                                <div class="accordion-body" contenteditable="true">
                                    Bu yerda narx haqida ma'lumot bering.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            contactform: `
                <div class="contact-form p-4" style="background: #f8f9fa; border-radius: 15px;">
                    <h4 class="mb-4" contenteditable="true">Biz bilan bog'laning</h4>
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Ismingiz">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email manzilingiz">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Xabaringiz"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Yuborish</button>
                    </form>
                </div>
            `,
            map: `
                <div class="map-wrapper" style="border-radius: 15px; overflow: hidden;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2996.4!2d66.95!3d39.65!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMznCsDM5JzAwLjAiTiA2NsKwNTcnMDAuMCJF!5e0!3m2!1sen!2s!4v1234567890"
                        width="100%"
                        height="300"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
            `,
            alert: `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <span contenteditable="true">Bu muhim bildirishnoma! O'zingizning xabaringizni kiriting.</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `,
            list: `
                <ul class="list-group">
                    <li class="list-group-item" contenteditable="true"><i class="fas fa-check-circle text-success me-2"></i>Birinchi element</li>
                    <li class="list-group-item" contenteditable="true"><i class="fas fa-check-circle text-success me-2"></i>Ikkinchi element</li>
                    <li class="list-group-item" contenteditable="true"><i class="fas fa-check-circle text-success me-2"></i>Uchinchi element</li>
                </ul>
            `
        };
        
        content.innerHTML = templates[type] || '<div>Unknown element type</div>';
        
        // Initialize special elements
        if (type === 'countdown') {
            this.initCountdown(content.querySelector('.countdown-timer'));
        } else if (type === 'chart') {
            this.initChart(content.querySelector('.element-chart'));
        } else if (type === 'row' || type === 'column') {
            this.initColumnDropzones(content);
        }
        
        return content;
    }
    
    initColumnDropzones(content) {
        content.querySelectorAll('.column-dropzone').forEach(column => {
            new Sortable(column, {
                group: 'shared',
                animation: 150,
                ghostClass: 'dragging',
                handle: '.element-handle',
                onEnd: () => {
                    this.saveHistory();
                }
            });
        });
    }
    
    initCountdown(element) {
        if (!element) return;
        
        const targetDate = new Date(element.dataset.target);
        
        const updateCountdown = () => {
            const now = new Date();
            const diff = targetDate - now;
            
            if (diff > 0) {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                element.querySelector('[data-days]').textContent = String(days).padStart(2, '0');
                element.querySelector('[data-hours]').textContent = String(hours).padStart(2, '0');
                element.querySelector('[data-minutes]').textContent = String(minutes).padStart(2, '0');
                element.querySelector('[data-seconds]').textContent = String(seconds).padStart(2, '0');
            }
        };
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
    
    initChart(canvas) {
        if (!canvas || typeof Chart === 'undefined') return;
        
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    attachElementEvents(element) {
        element.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectElement(element);
        });
        
        // Make content editable
        element.querySelectorAll('[contenteditable="true"]').forEach(editable => {
            editable.addEventListener('input', () => {
                this.saveHistory();
            });
            
            editable.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                document.execCommand('insertText', false, text);
            });
        });
    }
    
    selectElement(element) {
        // Remove previous selection
        document.querySelectorAll('.builder-element.selected').forEach(el => {
            el.classList.remove('selected');
        });
        
        // Add selection to new element
        element.classList.add('selected');
        this.selectedElement = element;
        
        // Show style panel
        this.showStylePanel(element);
    }
    
    showStylePanel(element) {
        const type = element.dataset.elementType;
        const styles = this.getElementStyles(element);
        
        // Update style panel content
        document.getElementById('selectedElementType').textContent = type.charAt(0).toUpperCase() + type.slice(1);
        
        // Populate style controls
        this.populateStyleControls(element, styles);
        
        // Show panel
        this.stylePanel.classList.add('active');
    }
    
    populateStyleControls(element, styles) {
        const content = element.querySelector('.element-content').firstElementChild;
        
        // Background color
        const bgColorInput = document.getElementById('bgColor');
        if (bgColorInput) {
            bgColorInput.value = styles.backgroundColor || '#ffffff';
            bgColorInput.addEventListener('change', (e) => {
                content.style.backgroundColor = e.target.value;
                this.saveHistory();
            });
        }
        
        // Text color
        const textColorInput = document.getElementById('textColor');
        if (textColorInput) {
            textColorInput.value = styles.color || '#000000';
            textColorInput.addEventListener('change', (e) => {
                content.style.color = e.target.value;
                this.saveHistory();
            });
        }
        
        // Font size
        const fontSizeInput = document.getElementById('fontSize');
        if (fontSizeInput) {
            fontSizeInput.value = parseInt(styles.fontSize) || 16;
            fontSizeInput.addEventListener('input', (e) => {
                content.style.fontSize = e.target.value + 'px';
                this.saveHistory();
            });
        }
        
        // Padding
        const paddingInput = document.getElementById('padding');
        if (paddingInput) {
            paddingInput.value = parseInt(styles.padding) || 0;
            paddingInput.addEventListener('input', (e) => {
                content.style.padding = e.target.value + 'px';
                this.saveHistory();
            });
        }
        
        // Margin
        const marginInput = document.getElementById('margin');
        if (marginInput) {
            marginInput.value = parseInt(styles.margin) || 0;
            marginInput.addEventListener('input', (e) => {
                content.style.margin = e.target.value + 'px';
                this.saveHistory();
            });
        }
        
        // Border radius
        const borderRadiusInput = document.getElementById('borderRadius');
        if (borderRadiusInput) {
            borderRadiusInput.value = parseInt(styles.borderRadius) || 0;
            borderRadiusInput.addEventListener('input', (e) => {
                content.style.borderRadius = e.target.value + 'px';
                this.saveHistory();
            });
        }
        
        // Animation
        const animationSelect = document.getElementById('animation');
        if (animationSelect) {
            animationSelect.addEventListener('change', (e) => {
                content.className = content.className.replace(/animate__\w+/g, '');
                if (e.target.value) {
                    content.classList.add('animate__animated', `animate__${e.target.value}`);
                }
                this.saveHistory();
            });
        }
        
        // Custom CSS
        const customCssTextarea = document.getElementById('customCss');
        if (customCssTextarea) {
            customCssTextarea.value = element.dataset.customCss || '';
            customCssTextarea.addEventListener('input', (e) => {
                element.dataset.customCss = e.target.value;
                this.applyCustomCss(element, e.target.value);
                this.saveHistory();
            });
        }
    }
    
    applyCustomCss(element, css) {
        const styleId = `custom-style-${element.dataset.elementId}`;
        let styleTag = document.getElementById(styleId);
        
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = styleId;
            document.head.appendChild(styleTag);
        }
        
        styleTag.textContent = `[data-element-id="${element.dataset.elementId}"] { ${css} }`;
    }
    
    getElementStyles(element) {
        const content = element.querySelector('.element-content').firstElementChild;
        return window.getComputedStyle(content);
    }
    
    handleElementAction(action, element) {
        switch (action) {
            case 'edit':
                this.selectElement(element);
                break;
            case 'duplicate':
                this.duplicateElement(element);
                break;
            case 'moveUp':
                this.moveElement(element, 'up');
                break;
            case 'moveDown':
                this.moveElement(element, 'down');
                break;
            case 'delete':
                this.deleteElement(element);
                break;
        }
    }
    
    duplicateElement(element) {
        const clone = element.cloneNode(true);
        clone.dataset.elementId = this.generateId();
        element.parentNode.insertBefore(clone, element.nextSibling);
        this.attachElementEvents(clone);
        this.saveHistory();
    }
    
    moveElement(element, direction) {
        if (direction === 'up' && element.previousElementSibling) {
            element.parentNode.insertBefore(element, element.previousElementSibling);
        } else if (direction === 'down' && element.nextElementSibling) {
            element.parentNode.insertBefore(element.nextElementSibling, element);
        }
        this.saveHistory();
    }
    
    deleteElement(element) {
        if (confirm('Are you sure you want to delete this element?')) {
            element.remove();
            this.stylePanel.classList.remove('active');
            this.selectedElement = null;
            this.saveHistory();
        }
    }
    
    setupEventListeners() {
        // Device preview buttons
        document.querySelectorAll('[data-device]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.setDevice(btn.dataset.device);
            });
        });
        
        // Undo/Redo buttons
        document.getElementById('undoBtn')?.addEventListener('click', () => this.undo());
        document.getElementById('redoBtn')?.addEventListener('click', () => this.redo());
        
        // Save button
        document.getElementById('saveBtn')?.addEventListener('click', () => this.save());
        
        // Preview button
        document.getElementById('previewBtn')?.addEventListener('click', () => this.preview());
        
        // Clear canvas
        document.getElementById('clearBtn')?.addEventListener('click', () => this.clearCanvas());
        
        // Close style panel
        document.querySelector('.close-panel')?.addEventListener('click', () => {
            this.stylePanel.classList.remove('active');
            this.selectedElement?.classList.remove('selected');
            this.selectedElement = null;
        });
        
        // Click outside to deselect
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.builder-element') && !e.target.closest('#stylePanel')) {
                this.stylePanel.classList.remove('active');
                this.selectedElement?.classList.remove('selected');
                this.selectedElement = null;
            }
        });
    }
    
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'z':
                        e.preventDefault();
                        this.undo();
                        break;
                    case 'y':
                        e.preventDefault();
                        this.redo();
                        break;
                    case 's':
                        e.preventDefault();
                        this.save();
                        break;
                    case 'd':
                        if (this.selectedElement) {
                            e.preventDefault();
                            this.duplicateElement(this.selectedElement);
                        }
                        break;
                }
            } else if (e.key === 'Delete' && this.selectedElement) {
                e.preventDefault();
                this.deleteElement(this.selectedElement);
            }
        });
    }
    
    setDevice(device) {
        this.device = device;
        const canvas = document.getElementById('canvas');
        
        // Update button states
        document.querySelectorAll('[data-device]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.device === device);
        });
        
        // Update canvas class
        canvas.className = `device-${device}`;
        
        // Update canvas dimensions
        const dimensions = {
            'desktop': { width: '100%', maxWidth: '100%' },
            'tablet': { width: '768px', maxWidth: '768px' },
            'mobile': { width: '375px', maxWidth: '375px' }
        };
        
        Object.assign(canvas.style, dimensions[device]);
    }
    
    saveHistory() {
        const state = this.canvas.innerHTML;
        
        // Remove any states after current index
        this.history = this.history.slice(0, this.historyIndex + 1);
        
        // Add new state
        this.history.push(state);
        this.historyIndex++;
        
        // Limit history size
        if (this.history.length > 50) {
            this.history.shift();
            this.historyIndex--;
        }
        
        this.updateHistoryButtons();
    }
    
    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.canvas.innerHTML = this.history[this.historyIndex];
            this.reattachEvents();
            this.updateHistoryButtons();
        }
    }
    
    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.canvas.innerHTML = this.history[this.historyIndex];
            this.reattachEvents();
            this.updateHistoryButtons();
        }
    }
    
    updateHistoryButtons() {
        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');
        
        if (undoBtn) undoBtn.disabled = this.historyIndex <= 0;
        if (redoBtn) redoBtn.disabled = this.historyIndex >= this.history.length - 1;
    }
    
    reattachEvents() {
        this.canvas.querySelectorAll('.builder-element').forEach(element => {
            this.attachElementEvents(element);
        });
        
        this.canvas.querySelectorAll('.column-dropzone').forEach(column => {
            new Sortable(column, {
                group: 'shared',
                animation: 150,
                ghostClass: 'dragging',
                handle: '.element-handle',
                onEnd: () => {
                    this.saveHistory();
                }
            });
        });
        
        this.canvas.querySelectorAll('.countdown-timer').forEach(timer => {
            this.initCountdown(timer);
        });
        
        this.canvas.querySelectorAll('.element-chart').forEach(chart => {
            this.initChart(chart);
        });
    }
    
    clearCanvas() {
        if (confirm('Are you sure you want to clear the canvas? This action cannot be undone.')) {
            this.canvas.innerHTML = '';
            this.saveHistory();
        }
    }
    
    save() {
        const pageData = this.getPageData();
        
        fetch(`/cms/pages/${this.config.pageId}/builder/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: pageData.elements,
                styles: pageData.styles,
                settings: pageData.settings,
                lang: this.config.language
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Page saved successfully!', 'success');
            } else {
                this.showNotification('Error saving page', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Error saving page', 'error');
        });
    }
    
    getPageData() {
        const elements = [];
        const styles = {};
        
        this.canvas.querySelectorAll('.builder-element').forEach(element => {
            const elementData = {
                id: element.dataset.elementId,
                type: element.dataset.elementType,
                content: this.getElementContent(element),
                styles: this.getElementStyleData(element),
                customCss: element.dataset.customCss || ''
            };
            
            elements.push(elementData);
            
            if (element.dataset.customCss) {
                styles[element.dataset.elementId] = element.dataset.customCss;
            }
        });
        
        return {
            elements,
            styles,
            settings: {
                device: this.device,
                timestamp: new Date().toISOString()
            }
        };
    }
    
    getElementContent(element) {
        const content = element.querySelector('.element-content');
        const type = element.dataset.elementType;
        
        if (type === 'row' || type === 'column') {
            const columns = [];
            content.querySelectorAll('.column-dropzone').forEach(column => {
                const columnElements = [];
                column.querySelectorAll('.builder-element').forEach(el => {
                    columnElements.push(this.getElementContent(el));
                });
                columns.push({
                    width: column.className.match(/col-\w+-\d+/)?.[0] || 'col-md-12',
                    elements: columnElements
                });
            });
            return { type: 'columns', data: columns };
        }
        
        return content.innerHTML;
    }
    
    getElementStyleData(element) {
        const content = element.querySelector('.element-content').firstElementChild;
        if (!content) return {};
        
        const styles = {};
        const computedStyles = window.getComputedStyle(content);
        
        ['backgroundColor', 'color', 'fontSize', 'padding', 'margin', 'borderRadius'].forEach(prop => {
            const value = computedStyles[prop];
            if (value && value !== 'auto' && value !== '0px') {
                styles[prop] = value;
            }
        });
        
        return styles;
    }
    
    preview() {
        const pageData = this.getPageData();
        const previewWindow = window.open('', '_blank');
        
        const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Page Preview</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                <style>
                    ${Object.entries(pageData.styles).map(([id, css]) => 
                        `[data-element-id="${id}"] { ${css} }`
                    ).join('\n')}
                </style>
            </head>
            <body>
                <div class="container py-5">
                    ${this.canvas.innerHTML}
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            </body>
            </html>
        `;
        
        previewWindow.document.write(html);
        previewWindow.document.close();
    }
    
    loadPageData() {
        if (this.config.pageData) {
            // Load existing page data
            this.renderElements(this.config.pageData.elements);
            this.applyStyles(this.config.pageData.styles);
            this.saveHistory();
        }
    }
    
    renderElements(elements) {
        elements.forEach(elementData => {
            const element = this.createElement(elementData.type);
            element.dataset.elementId = elementData.id;
            
            if (elementData.content) {
                element.querySelector('.element-content').innerHTML = elementData.content;
            }
            
            if (elementData.customCss) {
                element.dataset.customCss = elementData.customCss;
                this.applyCustomCss(element, elementData.customCss);
            }
            
            this.canvas.appendChild(element);
        });
    }
    
    applyStyles(styles) {
        Object.entries(styles).forEach(([id, css]) => {
            const element = this.canvas.querySelector(`[data-element-id="${id}"]`);
            if (element) {
                this.applyCustomCss(element, css);
            }
        });
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    generateId() {
        return 'element-' + Math.random().toString(36).substr(2, 9);
    }
    
    getFutureDate() {
        const future = new Date();
        future.setDate(future.getDate() + 30);
        return future.toISOString();
    }
    
    initializeAnimations() {
        // Add animate.css if not already loaded
        if (!document.querySelector('link[href*="animate.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
            document.head.appendChild(link);
        }
    }
    
    initializeCharts() {
        // Add Chart.js if not already loaded
        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            document.head.appendChild(script);
        }
    }
    
    updateElementPositions() {
        // Update element positions after drag and drop
        this.canvas.querySelectorAll('.builder-element').forEach((element, index) => {
            element.dataset.position = index;
        });
    }
}

// Initialize page builder when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('dropZone')) {
        window.pageBuilder = new PageBuilder({
            pageId: document.querySelector('meta[name="page-id"]')?.content
        });
    }
});
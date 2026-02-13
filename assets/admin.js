/**
 * WPWay Admin Dashboard JavaScript
 * Handles GUI interactions for component and page management
 */

(function() {
    'use strict';
    
    const wpwayAdmin = {
        /**
         * Initialize admin dashboard
         */
        init: function() {
            this.setupEventListeners();
            this.loadComponents();
            this.loadPages();
            this.initCodeEditor();
            this.initTabs();
        },
        
        /**
         * Setup event listeners
         */
        setupEventListeners: function() {
            // Component form
            const componentForm = document.getElementById('wpway-component-form');
            if (componentForm) {
                componentForm.addEventListener('submit', (e) => this.handleCreateComponent(e));
            }
            
            // Tab buttons
            document.querySelectorAll('.wpway-tab-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleTabClick(e));
            });
            
            // Create component button
            const createBtn = document.getElementById('wpway-create-component');
            if (createBtn) {
                createBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.switchTab('create');
                });
            }
            
            // Create page button
            const createPageBtn = document.getElementById('wpway-create-page');
            if (createPageBtn) {
                createPageBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.showPageModal();
                });
            }
            
            // Component type change
            const componentType = document.getElementById('component-type');
            if (componentType) {
                componentType.addEventListener('change', (e) => this.handleTypeChange(e));
            }
            
            // Refresh stats button
            const refreshStats = document.getElementById('wpway-refresh-stats');
            if (refreshStats) {
                refreshStats.addEventListener('click', () => this.loadStats());
            }
            
            // Modal close
            document.querySelectorAll('.wpway-modal-close').forEach(closeBtn => {
                closeBtn.addEventListener('click', (e) => {
                    e.target.closest('.wpway-modal').style.display = 'none';
                });
            });
        },
        
        /**
         * Initialize tabs
         */
        initTabs: function() {
            document.querySelectorAll('.wpway-tab-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const tabName = btn.dataset.tab;
                    this.switchTab(tabName);
                });
            });
        },
        
        /**
         * Switch tabs
         */
        switchTab: function(tabName) {
            // Hide all tabs
            document.querySelectorAll('.wpway-tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
            
            // Update button states
            document.querySelectorAll('.wpway-tab-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.tab === tabName) {
                    btn.classList.add('active');
                }
            });
        },
        
        /**
         * Handle tab click
         */
        handleTabClick: function(e) {
            e.preventDefault();
            const tabName = e.target.dataset.tab;
            this.switchTab(tabName);
        },
        
        /**
         * Load components
         */
        loadComponents: function() {
            const componentsList = document.getElementById('wpway-components-list');
            if (!componentsList) return;
            
            fetch(wpwayAdmin.restUrl + '/components', {
                headers: {
                    'X-WP-Nonce': wpwayAdmin.nonce
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.components) {
                    this.renderComponentsList(data.components);
                }
            })
            .catch(err => console.error('Error loading components:', err));
        },
        
        /**
         * Render components list
         */
        renderComponentsList: function(components) {
            const componentsList = document.getElementById('wpway-components-list');
            if (!componentsList) return;
            
            if (Object.keys(components).length === 0) {
                componentsList.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px;">No components created yet.</td></tr>';
                return;
            }
            
            let html = '';
            for (const [name, className] of Object.entries(components)) {
                html += `
                    <tr>
                        <td><strong>${name}</strong></td>
                        <td>PHP</td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            <button class="button" onclick="wpwayAdmin.editComponent('${name}')">Edit</button>
                            <button class="button" onclick="wpwayAdmin.deleteComponent('${name}')">Delete</button>
                        </td>
                    </tr>
                `;
            }
            
            componentsList.innerHTML = html;
        },
        
        /**
         * Handle create component form submission
         */
        handleCreateComponent: function(e) {
            e.preventDefault();
            
            const name = document.getElementById('component-name').value;
            const type = document.getElementById('component-type').value;
            const description = document.getElementById('component-description').value;
            const code = document.getElementById('component-code').value;
            
            if (!name || !type) {
                alert('Please fill in all required fields');
                return;
            }
            
            this.createComponent(name, type, code);
        },
        
        /**
         * Create component via API
         */
        createComponent: function(name, type, code) {
            fetch(wpwayAdmin.restUrl + '/components', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': wpwayAdmin.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    type: type,
                    code: code
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.showMessage('Component created successfully!', 'success');
                    document.getElementById('wpway-component-form').reset();
                    this.loadComponents();
                    this.switchTab('list');
                } else {
                    this.showMessage(data.message || 'Error creating component', 'error');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                this.showMessage('Error creating component', 'error');
            });
        },
        
        /**
         * Edit component
         */
        editComponent: function(componentName) {
            // Open code editor with component code
            const filePath = componentName;
            this.switchTab('code-editor');
            document.getElementById('editor-filename').textContent = componentName + '.php';
        },
        
        /**
         * Delete component
         */
        deleteComponent: function(componentName) {
            if (!confirm(`Are you sure you want to delete component "${componentName}"?`)) {
                return;
            }
            
            fetch(wpwayAdmin.restUrl + '/components/' + componentName, {
                method: 'DELETE',
                headers: {
                    'X-WP-Nonce': wpwayAdmin.nonce
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.showMessage('Component deleted successfully!', 'success');
                    this.loadComponents();
                } else {
                    this.showMessage(data.message || 'Error deleting component', 'error');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                this.showMessage('Error deleting component', 'error');
            });
        },
        
        /**
         * Load pages
         */
        loadPages: function() {
            const pagesList = document.getElementById('wpway-pages-list');
            if (!pagesList) return;
            
            fetch(wpwayAdmin.restUrl + '/pages', {
                headers: {
                    'X-WP-Nonce': wpwayAdmin.nonce
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.pages) {
                    this.renderPagesList(data.pages);
                }
            })
            .catch(err => console.error('Error loading pages:', err));
        },
        
        /**
         * Render pages list
         */
        renderPagesList: function(pages) {
            const pagesList = document.getElementById('wpway-pages-list');
            if (!pagesList) return;
            
            if (pages.length === 0) {
                pagesList.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px;">No pages found.</td></tr>';
                return;
            }
            
            let html = '';
            pages.forEach(page => {
                html += `
                    <tr>
                        <td><strong>${page.post_title}</strong></td>
                        <td>-</td>
                        <td><span class="badge">${page.post_status}</span></td>
                        <td>${new Date(page.post_date).toLocaleDateString()}</td>
                        <td>
                            <button class="button" onclick="wp.editor.open(${page.ID})">Edit</button>
                            <button class="button" onclick="window.open('${page.guid}')">View</button>
                        </td>
                    </tr>
                `;
            });
            
            pagesList.innerHTML = html;
        },
        
        /**
         * Show page modal
         */
        showPageModal: function() {
            const modal = document.getElementById('wpway-page-modal');
            if (modal) {
                modal.style.display = 'flex';
            }
        },
        
        /**
         * Handle component type change
         */
        handleTypeChange: function(e) {
            const type = e.target.value;
            const phpGroup = document.getElementById('php-template-group');
            
            if (type === 'php') {
                phpGroup.style.display = 'block';
            } else {
                phpGroup.style.display = 'none';
            }
        },
        
        /**
         * Initialize code editor
         */
        initCodeEditor: function() {
            const editorContainer = document.getElementById('wpway-code-editor-container');
            const codeTextarea = document.getElementById('wpway-code-textarea');
            
            if (editorContainer && typeof ace !== 'undefined') {
                const editor = ace.edit('wpway-code-editor-container');
                editor.setTheme('ace/theme/chrome');
                editor.session.setMode('ace/mode/php');
                editor.setFontSize(14);
                
                // Sync textarea with editor
                editor.session.on('change', function() {
                    if (codeTextarea) {
                        codeTextarea.value = editor.getValue();
                    }
                });
            }
        },
        
        /**
         * Load statistics
         */
        loadStats: function() {
            fetch(wpwayAdmin.restUrl + '/components', {
                headers: {
                    'X-WP-Nonce': wpwayAdmin.nonce
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('stat-components').textContent = Object.keys(data.components).length;
                }
            });
            
            fetch(wpwayAdmin.restUrl + '/pages', {
                headers: {
                    'X-WP-Nonce': wpwayAdmin.nonce
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('stat-pages').textContent = data.pages.length;
                }
            });
        },
        
        /**
         * Show message notification
         */
        showMessage: function(message, type = 'info') {
            const messageDiv = document.createElement('div');
            messageDiv.className = `wpway-message ${type}`;
            messageDiv.textContent = message;
            
            // Insert at top of page
            const wrap = document.querySelector('.wrap');
            if (wrap) {
                wrap.insertBefore(messageDiv, wrap.firstChild);
                
                // Auto-remove after 5 seconds
                setTimeout(() => messageDiv.remove(), 5000);
            }
        }
    };
    
    // Expose to global scope
    window.wpwayAdmin = wpwayAdmin;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => wpwayAdmin.init());
    } else {
        wpwayAdmin.init();
    }
})();

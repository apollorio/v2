/**
 * Apollo Bulk Editor — Handsontable-based spreadsheet for bulk editing
 *
 * Handles: data loading, cell editing, batch saving, pagination, search,
 * row insertion, row deletion, CSV export.
 *
 * Requires: Handsontable, jQuery, wp-util
 * Localized via ApolloBulk global object.
 *
 * @package Apollo\Sheets
 */

(function ($) {
    'use strict';

    if (typeof Handsontable === 'undefined') {
        console.error('Apollo Bulk Editor: Handsontable not loaded.');
        return;
    }

    // ═══════════════════════════════════════════════════════════════════
    // STATE
    // ═══════════════════════════════════════════════════════════════════

    const config = window.ApolloBulk || {};
    const state = {
        hot: null,                    // Handsontable instance
        data: [],                     // Current rows
        originalData: [],             // Snapshot for change detection
        changedRows: new Set(),       // Set of row indices with changes
        currentPage: 1,
        totalPages: 1,
        totalRows: 0,
        perPage: config.perPage || 50,
        loading: false,
        saving: false,
        colHeaders: [],
        columns: [],
        colWidths: [],
        contentType: config.contentType || '',
        entityType: config.entityType || 'post_type',
        searchTerm: '',
        statusFilter: 'any',
    };

    // ═══════════════════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════════════════

    $(document).ready(function () {
        initEventListeners();
        loadData();
    });

    function initEventListeners() {
        // Save button
        $('#bulk-btn-save').on('click', saveChanges);

        // Add rows
        $('#bulk-btn-add').on('click', function () {
            $('#bulk-add-modal').fadeIn(200);
        });
        $('#bulk-add-cancel').on('click', function () {
            $('#bulk-add-modal').fadeOut(200);
        });
        $('#bulk-add-confirm').on('click', insertRows);

        // Delete
        $('#bulk-btn-delete').on('click', deleteSelected);

        // Export CSV
        $('#bulk-btn-export').on('click', exportCSV);

        // Pagination
        $('#bulk-page-prev').on('click', function () {
            if (state.currentPage > 1) {
                state.currentPage--;
                loadData();
            }
        });
        $('#bulk-page-next').on('click', function () {
            if (state.currentPage < state.totalPages) {
                state.currentPage++;
                loadData();
            }
        });
        $('#bulk-page-current').on('change', function () {
            const page = parseInt($(this).val(), 10);
            if (page >= 1 && page <= state.totalPages) {
                state.currentPage = page;
                loadData();
            } else {
                $(this).val(state.currentPage);
            }
        });

        // Search — debounced
        let searchTimer = null;
        $('#bulk-search').on('input', function () {
            clearTimeout(searchTimer);
            const val = $(this).val();
            searchTimer = setTimeout(function () {
                state.searchTerm = val;
                state.currentPage = 1;
                loadData();
            }, 400);
        });

        // Rows per page selector
        $('#bulk-per-page').on('change', function () {
            const val = $(this).val();
            if (val === 'custom') {
                const custom = prompt('Quantas linhas por página?', state.perPage);
                if (custom && !isNaN(custom) && parseInt(custom, 10) > 0) {
                    state.perPage = parseInt(custom, 10);
                    state.currentPage = 1;
                    loadData();
                } else {
                    $(this).val(state.perPage); // Reset to current
                }
            } else {
                state.perPage = parseInt(val, 10) || 100;
                state.currentPage = 1;
                loadData();
            }
        });

        // Status/role filter
        $('#bulk-status-filter, #bulk-role-filter, #bulk-comment-status-filter').on('change', function () {
            state.statusFilter = $(this).val();
            state.currentPage = 1;
            loadData();
        });

        // Warn before leaving with unsaved changes
        $(window).on('beforeunload', function () {
            if (state.changedRows.size > 0) {
                return 'Existem alterações não salvas. Deseja sair?';
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    // DATA LOADING
    // ═══════════════════════════════════════════════════════════════════

    function loadData() {
        if (state.loading) return;
        state.loading = true;

        setConsole(config.i18n.loading, 'info');

        const postData = {
            action: 'apollo_bulk_load',
            nonce: config.nonce,
            content_type: state.contentType,
            entity_type: state.entityType,
            per_page: state.perPage,
            page: state.currentPage,
            search: state.searchTerm,
            orderby: 'ID',
            order: 'DESC',
        };

        // Entity-specific filters
        if (state.entityType === 'post_type') {
            postData.status = state.statusFilter;
        } else if (state.entityType === 'users') {
            postData.role = state.statusFilter !== 'any' ? state.statusFilter : '';
        } else if (state.entityType === 'comments') {
            postData.status = state.statusFilter !== 'any' ? state.statusFilter : 'all';
        }

        $.post(config.ajaxUrl, postData, function (response) {
            state.loading = false;

            if (!response.success) {
                const errorMsg = response.data?.message || config.i18n.error;
                const errorDetails = response.data?.details || '';
                const fullError = errorDetails ? errorMsg + ' — ' + errorDetails : errorMsg;
                setConsole(fullError, 'error');
                console.error('Apollo Bulk Load Error:', response);
                return;
            }

            const d = response.data;

            state.data = d.rows || [];
            state.originalData = JSON.parse(JSON.stringify(state.data));
            state.totalRows = d.total || 0;
            state.totalPages = d.pages || 1;
            state.colHeaders = d.colHeaders || [];
            state.columns = d.columns || [];
            state.colWidths = d.colWidths || [];
            state.changedRows.clear();

            updatePagination();
            renderSpreadsheet();

            if (state.data.length === 0) {
                setConsole(config.i18n.noData, 'warn');
            } else {
                setConsole(state.totalRows + ' ' + config.i18n.total.toLowerCase(), 'success');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            state.loading = false;
            const errorMsg = config.i18n.error + ' (AJAX ' + textStatus + ')';
            setConsole(errorMsg, 'error');
            console.error('Apollo Bulk AJAX Fail:', { jqXHR, textStatus, errorThrown });
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    // HANDSONTABLE RENDER
    // ═══════════════════════════════════════════════════════════════════

    function renderSpreadsheet() {
        const container = document.getElementById('apollo-bulk-spreadsheet');
        if (!container) return;

        // Destroy previous instance
        if (state.hot) {
            state.hot.destroy();
            state.hot = null;
        }

        if (state.data.length === 0) {
            container.innerHTML = '<div style="padding:40px;text-align:center;color:#8c8f94;">' + config.i18n.noData + '</div>';
            return;
        }

        // Convert object rows to arrays for Handsontable
        const columnKeys = state.columns.map(function (c) { return c.data; });

        state.hot = new Handsontable(container, {
            data: state.data,
            colHeaders: state.colHeaders,
            columns: state.columns,
            colWidths: state.colWidths,
            rowHeaders: true,
            fixedColumnsStart: 2,    // Fix ID + Title columns
            manualColumnResize: true,
            manualRowResize: false,
            contextMenu: ['row_above', 'row_below', '---------', 'undo', 'redo', '---------', 'copy', 'cut'],
            outsideClickDeselects: false,
            autoWrapRow: true,
            autoWrapCol: true,
            stretchH: 'last',
            height: calcHeight(),
            licenseKey: 'non-commercial-and-evaluation',

            // Cell change callback
            afterChange: function (changes, source) {
                if (source === 'loadData' || !changes) return;

                changes.forEach(function (change) {
                    const [row, prop, oldVal, newVal] = change;
                    if (oldVal !== newVal) {
                        state.changedRows.add(row);
                    }
                });

                updateSaveButton();
            },

            // Selection callback — enable delete button
            afterSelection: function () {
                if (state.entityType === 'post_type') {
                    $('#bulk-btn-delete').prop('disabled', false);
                }
            },

            afterDeselect: function () {
                $('#bulk-btn-delete').prop('disabled', true);
            },

            // Row styling for changed rows
            cells: function (row, col) {
                const cellProperties = {};
                if (state.changedRows.has(row)) {
                    cellProperties.className = 'apollo-bulk-cell-changed';
                }
                return cellProperties;
            },

            // ReadOnly cell styling
            afterRenderer: function (td, row, col, prop, value, cellProperties) {
                if (cellProperties.readOnly) {
                    td.classList.add('apollo-bulk-cell-readonly');
                }
            },
        });

        // Resize on window resize
        $(window).off('resize.apollobulk').on('resize.apollobulk', function () {
            if (state.hot) {
                state.hot.updateSettings({ height: calcHeight() });
            }
        });
    }

    function calcHeight() {
        const top = document.getElementById('apollo-bulk-spreadsheet');
        if (!top) return 500;
        const rect = top.getBoundingClientRect();
        return Math.max(400, window.innerHeight - rect.top - 40);
    }

    // ═══════════════════════════════════════════════════════════════════
    // SAVE
    // ═══════════════════════════════════════════════════════════════════

    function saveChanges() {
        if (state.saving || state.changedRows.size === 0) return;
        state.saving = true;

        setConsole(config.i18n.saving, 'info');
        $('#bulk-btn-save').prop('disabled', true);
        $('#bulk-save-modal').fadeIn(200);
        $('#bulk-progress-bar').css('width', '10%');

        // Collect only changed rows
        const changedData = [];
        state.changedRows.forEach(function (rowIndex) {
            if (state.data[rowIndex]) {
                changedData.push(state.data[rowIndex]);
            }
        });

        if (changedData.length === 0) {
            state.saving = false;
            $('#bulk-save-modal').fadeOut(200);
            return;
        }

        // Batch save — send in chunks of 20
        const batchSize = 20;
        const batches = [];
        for (let i = 0; i < changedData.length; i += batchSize) {
            batches.push(changedData.slice(i, i + batchSize));
        }

        let completed = 0;
        let totalSaved = 0;
        let allErrors = [];

        function processBatch(index) {
            if (index >= batches.length) {
                // All done
                state.saving = false;
                state.changedRows.clear();
                state.originalData = JSON.parse(JSON.stringify(state.data));
                updateSaveButton();

                $('#bulk-progress-bar').css('width', '100%');

                const msg = totalSaved + ' ' + config.i18n.saved.toLowerCase();
                if (allErrors.length > 0) {
                    $('#bulk-save-response').html(
                        '<div class="notice notice-warning"><p>' + msg + '</p>' +
                        '<ul>' + allErrors.map(function (e) { return '<li>' + e + '</li>'; }).join('') + '</ul></div>'
                    );
                } else {
                    $('#bulk-save-response').html('<div class="notice notice-success"><p>' + msg + '</p></div>');
                }

                setConsole(msg, 'success');
                if (state.hot) {
                    state.hot.render();
                }

                setTimeout(function () {
                    $('#bulk-save-modal').fadeOut(300);
                    $('#bulk-save-response').empty();
                }, 2000);

                return;
            }

            $.post(config.ajaxUrl, {
                action: 'apollo_bulk_save',
                nonce: config.nonce,
                content_type: state.contentType,
                entity_type: state.entityType,
                rows: JSON.stringify(batches[index]),
            }, function (response) {
                completed++;
                const pct = Math.round((completed / batches.length) * 100);
                $('#bulk-progress-bar').css('width', pct + '%');

                if (response.success) {
                    totalSaved += response.data.saved || 0;
                    if (response.data.errors && response.data.errors.length) {
                        allErrors = allErrors.concat(response.data.errors);
                    }
                } else {
                    allErrors.push(response.data?.message || config.i18n.error);
                }

                // Small delay between batches
                setTimeout(function () {
                    processBatch(index + 1);
                }, 200);
            }).fail(function () {
                completed++;
                allErrors.push(config.i18n.error + ' (batch ' + (index + 1) + ')');
                setTimeout(function () {
                    processBatch(index + 1);
                }, 200);
            });
        }

        processBatch(0);
    }

    // ═══════════════════════════════════════════════════════════════════
    // INSERT ROWS
    // ═══════════════════════════════════════════════════════════════════

    function insertRows() {
        const count = parseInt($('#bulk-add-count').val(), 10) || 5;
        $('#bulk-add-modal').fadeOut(200);

        setConsole(config.i18n.loading, 'info');

        $.post(config.ajaxUrl, {
            action: 'apollo_bulk_insert',
            nonce: config.nonce,
            content_type: state.contentType,
            entity_type: state.entityType,
            count: count,
        }, function (response) {
            if (response.success) {
                setConsole(response.data.message, 'success');
                // Reload to show new rows
                state.currentPage = 1;
                loadData();
            } else {
                setConsole(response.data?.message || config.i18n.error, 'error');
            }
        }).fail(function () {
            setConsole(config.i18n.error, 'error');
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    // DELETE ROWS
    // ═══════════════════════════════════════════════════════════════════

    function deleteSelected() {
        if (!state.hot) return;

        const selected = state.hot.getSelected();
        if (!selected || selected.length === 0) return;

        if (!confirm(config.i18n.confirmDelete)) return;

        // Collect IDs from selected rows
        const ids = [];
        const idKey = state.entityType === 'comments' ? 'comment_ID' : 'ID';

        selected.forEach(function (sel) {
            const startRow = Math.min(sel[0], sel[2]);
            const endRow = Math.max(sel[0], sel[2]);
            for (let r = startRow; r <= endRow; r++) {
                const rowData = state.data[r];
                if (rowData && rowData[idKey]) {
                    ids.push(rowData[idKey]);
                }
            }
        });

        if (ids.length === 0) return;

        setConsole(config.i18n.loading, 'info');

        $.post(config.ajaxUrl, {
            action: 'apollo_bulk_delete',
            nonce: config.nonce,
            content_type: state.contentType,
            entity_type: state.entityType,
            ids: JSON.stringify(ids),
        }, function (response) {
            if (response.success) {
                setConsole(response.data.message, 'success');
                loadData();
            } else {
                setConsole(response.data?.message || config.i18n.error, 'error');
            }
        }).fail(function () {
            setConsole(config.i18n.error, 'error');
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    // EXPORT CSV
    // ═══════════════════════════════════════════════════════════════════

    function exportCSV() {
        if (!state.hot || state.data.length === 0) return;

        const sep = ',';
        const lines = [];

        // Headers
        lines.push(state.colHeaders.map(escapeCSV).join(sep));

        // Rows
        const columnKeys = state.columns.map(function (c) { return c.data; });
        state.data.forEach(function (row) {
            const cells = columnKeys.map(function (key) {
                return escapeCSV(row[key] !== undefined && row[key] !== null ? String(row[key]) : '');
            });
            lines.push(cells.join(sep));
        });

        const csv = '\uFEFF' + lines.join('\r\n'); // BOM for UTF-8
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'apollo-bulk-' + state.contentType + '-' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function escapeCSV(value) {
        if (typeof value !== 'string') value = String(value);
        if (value.indexOf(',') !== -1 || value.indexOf('"') !== -1 || value.indexOf('\n') !== -1) {
            return '"' + value.replace(/"/g, '""') + '"';
        }
        return value;
    }

    // ═══════════════════════════════════════════════════════════════════
    // UI HELPERS
    // ═══════════════════════════════════════════════════════════════════

    function updatePagination() {
        $('#bulk-page-current').val(state.currentPage);
        $('#bulk-page-total').text('/ ' + state.totalPages);
        $('#bulk-total-badge').text(state.totalRows);
        $('#bulk-total-info').text(state.totalRows + ' ' + config.i18n.total.toLowerCase());

        $('#bulk-page-prev').prop('disabled', state.currentPage <= 1);
        $('#bulk-page-next').prop('disabled', state.currentPage >= state.totalPages);
    }

    function updateSaveButton() {
        const hasChanges = state.changedRows.size > 0;
        $('#bulk-btn-save').prop('disabled', !hasChanges || state.saving);

        if (hasChanges) {
            $('#bulk-btn-save').addClass('apollo-bulk-btn-pulse');
        } else {
            $('#bulk-btn-save').removeClass('apollo-bulk-btn-pulse');
        }
    }

    function setConsole(message, type) {
        const $console = $('#bulk-console');
        type = type || 'info';
        
        const typeLabels = {
            'info': 'Info',
            'warn': 'Aviso',
            'error': 'Erro',
            'success': 'Sucesso'
        };
        
        const typeLabel = typeLabels[type] || 'Info';
        
        $console
            .removeClass('info warn error success')
            .addClass(type)
            .html('<div><span class="msg-type">' + typeLabel + '</span> ' + message + '</div><button class="console-close" onclick="document.getElementById(\'bulk-console\').style.display=\'none\'"><i class="ri-close-line"></i></button>');
        
        $console.show();
    }

})(jQuery);

    @extends('layouts.app')
    @section('title', 'Deduction Management')
    @section('page-title', 'Payroll')

    @section('content')
    <style>
    /* ═══════════════════════════════════════════════════════
    DEDUCTION MANAGEMENT — matches screenshot UI
    ═══════════════════════════════════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Page layout ── */
    .ded-page { display: flex; flex-direction: column; height: calc(100vh - 120px); overflow: hidden; }

    /* ── Breadcrumb ── */
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6b7280; margin-bottom: 14px; flex-shrink: 0; flex-wrap: wrap; }
    .breadcrumb a { color: #6b7280; text-decoration: none; }
    .breadcrumb a:hover { color: #1a3a1a; }
    .breadcrumb .sep { color: #d1d5db; }
    .breadcrumb .current { color: #1a3a1a; font-weight: 600; }

    /* ── Tabs (top of card — matches screenshot) ── */
    .tab-row { display: flex; align-items: center; gap: 0; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; padding: 0 20px; overflow-x: auto; }
    .tab-btn { padding: 12px 4px; font-size: 13px; font-weight: 500; color: #6b7280; border: none; background: none; border-bottom: 2px solid transparent; cursor: pointer; white-space: nowrap; margin-right: 20px; transition: all .15s; }
    .tab-btn.active { color: #1a3a1a; border-bottom-color: #2d5a1b; font-weight: 700; }
    .tab-btn:last-child { margin-right: 0; }

    /* ── Main card ── */
    .app-card { flex: 1; min-height: 0; display: flex; flex-direction: column; background: #fff; border-radius: 16px; border: 1px solid #f3f4f6; box-shadow: 0 1px 3px rgba(0,0,0,.05); overflow: hidden; }

    /* ── Toolbar ── */
    .toolbar { flex-shrink: 0; padding: 16px 20px; border-bottom: 1px solid #f9fafb; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .toolbar-left h2 { font-size: 14px; font-weight: 700; color: #1f2937; margin: 0 0 2px; }
    .toolbar-left p  { font-size: 11px; color: #9ca3af; margin: 0; }
    .toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* Search */
    .search-wrap { position: relative; }
    .search-wrap input {
        padding: 8px 12px 8px 34px;
        font-size: 12px; border: 1.5px solid #e5e7eb; border-radius: 8px;
        background: #fff; color: #374151; outline: none; width: 220px; transition: border-color .15s;
    }
    .search-wrap input:focus { border-color: #2d5a1b; }
    .search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }

    /* Filter btn */
    .btn-filter {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; font-size: 12px; font-weight: 600;
        color: #374151; background: #fff; border: 1.5px solid #e5e7eb;
        border-radius: 8px; cursor: pointer; transition: all .15s;
    }
    .btn-filter:hover { border-color: #9ca3af; }

    /* Add Position btn */
    .btn-add {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; font-size: 12px; font-weight: 700;
        color: #fff; background: #1a3a1a; border: none;
        border-radius: 8px; cursor: pointer; transition: background .15s;
    }
    .btn-add:hover { background: #2d5a1b; }

    /* ── Scrollable table area ── */
    .tsa { flex: 1; min-height: 0; overflow-y: auto; overflow-x: auto; scrollbar-width: thin; scrollbar-color: #e5e7eb transparent; }
    .tsa::-webkit-scrollbar { width: 5px; height: 5px; }
    .tsa::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 99px; }

    /* ── Table ── */
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 680px; }
    .data-table thead { position: sticky; top: 0; z-index: 2; }
    .data-table thead tr { background: #fafafa; border-bottom: 1px solid #f3f4f6; }
    .data-table th {
        padding: 10px 16px; text-align: left;
        font-size: 11px; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
        user-select: none; cursor: pointer;
    }
    .data-table th .sort-arrow { display: inline-block; margin-left: 4px; opacity: .4; font-size: 10px; transition: opacity .15s; }
    .data-table th:hover .sort-arrow { opacity: 1; }
    .data-table th.sorted .sort-arrow { opacity: 1; color: #1a3a1a; }

    .data-table td { padding: 12px 16px; border-bottom: 1px solid #f9fafb; color: #374151; vertical-align: middle; }
    .data-table tbody tr:hover { background: #fafafa; }
    .data-table tbody tr.dragging { opacity: .5; background: #f0fdf4; }

    /* type badge */
    .badge-fixed     { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #dbeafe; color: #1e40af; }
    .badge-notfixed  { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #fef9c3; color: #854d0e; }
    .badge-inactive  { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #f3f4f6; color: #6b7280; }

    /* Actions cell */
    .action-cell { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
    .icon-btn {
        display: flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid #e5e7eb;
        background: #fff; cursor: pointer; color: #6b7280; transition: all .15s;
    }
    .icon-btn:hover { border-color: #9ca3af; color: #374151; background: #f9fafb; }
    .icon-btn.edit:hover  { border-color: #2d5a1b; color: #1a3a1a; background: #f0fdf4; }
    .icon-btn.del:hover   { border-color: #dc2626; color: #dc2626; background: #fff1f2; }

    /* empty state */
    .empty-state { padding: 64px 24px; text-align: center; color: #9ca3af; }
    .empty-state svg { margin: 0 auto 12px; display: block; }

    /* ═══ MODAL ═══ */
    .modal-bg {
        position: fixed; inset: 0; z-index: 300;
        background: rgba(0,0,0,.4); backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity .2s; padding: 16px;
    }
    .modal-bg.show { opacity: 1; pointer-events: all; }

    .modal-card {
        background: #fff; border-radius: 18px;
        width: min(98vw, 520px); max-height: 90vh;
        overflow: hidden; display: flex; flex-direction: column;
        box-shadow: 0 24px 64px rgba(0,0,0,.22);
        transform: scale(.94) translateY(10px);
        transition: transform .28s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-bg.show .modal-card { transform: scale(1) translateY(0); }

    .modal-head {
        background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
        padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    }
    .modal-head h3 { font-size: 15px; font-weight: 700; color: #fff; margin: 0; }
    .modal-head p  { font-size: 11px; color: rgba(255,255,255,.65); margin: 3px 0 0; }
    .modal-close   { background: rgba(255,255,255,.15); border: none; width: 30px; height: 30px; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; line-height: 1; transition: background .15s; }
    .modal-close:hover { background: rgba(255,255,255,.28); }

    .modal-body { padding: 22px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 14px; }
    .modal-foot { padding: 14px 22px; border-top: 1px solid #f3f4f6; display: flex; gap: 10px; justify-content: flex-end; flex-shrink: 0; }

    /* Form fields */
    .f-label { display: block; font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; }
    .f-input {
        width: 100%; padding: 9px 12px; font-size: 13px;
        border: 1.5px solid #e5e7eb; border-radius: 9px;
        background: #f9fafb; color: #111827; outline: none; transition: border-color .15s, background .15s;
    }
    .f-input:focus { border-color: #2d5a1b; background: #fff; }
    .f-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px; cursor: pointer; }
    .f-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .f-hint { font-size: 10px; color: #9ca3af; margin-top: 3px; }
    .f-err  { font-size: 10px; color: #dc2626; margin-top: 3px; display: none; }

    /* Preview block */
    .preview-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; font-size: 12px; }
    .preview-box .p-row { display: flex; justify-content: space-between; padding: 3px 0; }
    .preview-box .p-lbl { color: #6b7280; }
    .preview-box .p-val { font-weight: 700; color: #111827; }

    /* Buttons */
    .btn-cancel { padding: 9px 18px; font-size: 12px; font-weight: 600; border: 1.5px solid #e5e7eb; border-radius: 8px; color: #6b7280; background: #fff; cursor: pointer; transition: all .15s; }
    .btn-cancel:hover { border-color: #9ca3af; color: #374151; }
    .btn-save   { padding: 9px 22px; font-size: 12px; font-weight: 700; border: none; border-radius: 8px; color: #fff; background: #1a3a1a; cursor: pointer; transition: background .15s; }
    .btn-save:hover { background: #2d5a1b; }
    .btn-save:disabled { background: #9ca3af; cursor: not-allowed; }

    /* Confirm modal */
    .confirm-modal { position: fixed; inset: 0; z-index: 400; background: rgba(0,0,0,.45); backdrop-filter: blur(3px); display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .2s; padding: 16px; }
    .confirm-modal.show { opacity: 1; pointer-events: all; }
    .confirm-card { background: #fff; border-radius: 18px; padding: 26px; width: min(98vw, 420px); box-shadow: 0 24px 64px rgba(0,0,0,.2); transform: scale(.93); transition: transform .25s cubic-bezier(.34,1.56,.64,1); }
    .confirm-modal.show .confirm-card { transform: scale(1); }

    /* Toast */
    #toast { position: fixed; bottom: 20px; right: 20px; z-index: 999; background: #fff; border-radius: 14px; padding: 12px 16px; box-shadow: 0 8px 32px rgba(0,0,0,.15); display: flex; align-items: center; gap: 10px; min-width: 220px; max-width: calc(100vw - 40px); opacity: 0; transform: translateY(12px); transition: all .3s; pointer-events: none; }
    #toast.show { opacity: 1; transform: translateY(0); }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .ded-page { height: auto; overflow: visible; }
        .app-card { overflow: visible; flex: none; height: auto; }
        .tsa { overflow: visible; height: auto; max-height: none; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-right { flex-direction: column; }
        .search-wrap input { width: 100%; }
        .f-row2 { grid-template-columns: 1fr; }
        .tab-row { padding: 0 12px; }
        .data-table { font-size: 12px; }
    }
    </style>

    <div class="ded-page">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Payroll</a>
        <span class="sep">›</span>
        <span class="current">Deduction Management</span>
    </div>

    {{-- Main card --}}
    <div class="app-card">

        {{-- Tab row (matching screenshot) --}}
        <div class="tab-row">
            <a href="{{ route('payroll.index') }}" style="text-decoration:none;">
                <button class="tab-btn">Leave Application List</button>
            </a>
            <a href="{{ route('payroll.index') }}" style="text-decoration:none;">
                <button class="tab-btn">Monetization Request List</button>
            </a>
            <a href="{{ route('payroll.index') }}" style="text-decoration:none;">
                <button class="tab-btn">History</button>
            </a>
            <button class="tab-btn active">Deduction Management</button>
        </div>

        {{-- Toolbar --}}
        <div class="toolbar">
            <div class="toolbar-left">
                <h2>Deduction Management</h2>
                <p>Create and manage payroll deductions</p>
            </div>
            <div class="toolbar-right">
                {{-- Search --}}
                <div class="search-wrap">
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search…"
                        value="{{ $search }}" oninput="filterRows(this.value)">
                </div>

                {{-- Filters btn --}}
                <button class="btn-filter" onclick="toggleFilterPanel()">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filters
                </button>

                {{-- Add --}}
                <button class="btn-add" onclick="openAddModal()">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Deduction
                </button>
            </div>
        </div>

        {{-- Filter panel (hidden by default) --}}
        <div id="filterPanel" style="display:none;padding:10px 20px;border-bottom:1px solid #f3f4f6;background:#fafafa;display:none;align-items:center;gap:12px;flex-wrap:wrap;">
            <div>
                <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px;">Type</label>
                <select id="filterType" class="f-input f-select" style="width:140px;" onchange="applyFilters()">
                    <option value="">All</option>
                    <option value="Fixed">Fixed</option>
                    <option value="Not Fixed">Not Fixed</option>
                </select>
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px;">Status</label>
                <select id="filterActive" class="f-input f-select" style="width:140px;" onchange="applyFilters()">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button onclick="clearFilters()" style="align-self:flex-end;padding:8px 12px;font-size:11px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:7px;background:#fff;color:#6b7280;cursor:pointer;">Clear</button>
        </div>

        {{-- Table --}}
        <div class="tsa">
            <table class="data-table" id="dedTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)" style="width:32px;">#<span class="sort-arrow">↕</span></th>
                        <th onclick="sortTable(1)">Name<span class="sort-arrow">↕</span></th>
                        <th onclick="sortTable(2)">Type<span class="sort-arrow">↕</span></th>
                        <th onclick="sortTable(3)">Rate<span class="sort-arrow">↕</span></th>
                        <th onclick="sortTable(4)">Limit<span class="sort-arrow">↕</span></th>
                        <th onclick="sortTable(5)">Status<span class="sort-arrow">↕</span></th>
                        <th style="text-align:right;cursor:default;">Actions</th>
                    </tr>
                </thead>
                <tbody id="dedTbody">
                    @forelse($deductions as $i => $d)
                    <tr data-id="{{ $d->id }}"
                        data-name="{{ strtolower($d->name) }}"
                        data-type="{{ strtolower($d->type) }}"
                        data-active="{{ $d->is_active ? '1' : '0' }}"
                        class="{{ !$d->is_active ? 'opacity-50' : '' }}"
                        draggable="true"
                        ondragstart="dragStart(event)"
                        ondragover="dragOver(event)"
                        ondrop="dragDrop(event)"
                        ondragend="dragEnd(event)">

                        <td style="color:#9ca3af;font-size:11px;">{{ $i + 1 }}</td>

                        <td>
                            <div style="font-weight:600;color:#111827;">{{ $d->name }}</div>
                        </td>

                        <td>
                            @if($d->type === 'Fixed')
                                <span class="badge-fixed">Fixed</span>
                            @else
                                <span class="badge-notfixed">Not Fixed</span>
                            @endif
                        </td>

                        <td style="font-weight:500;">{{ $d->rate ?? '—' }}</td>

                        <td style="color:#6b7280;">
                            @if($d->limit_amount !== null)
                                ₱{{ number_format($d->limit_amount, 2) }}
                            @else
                                <span style="color:#d1d5db;">N/A</span>
                            @endif
                        </td>

                        <td>
                            @if($d->is_active)
                                <span style="font-size:12px;color:#374151;">{{ $d->status }}</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <div class="action-cell">
                                {{-- Edit --}}
                                <button class="icon-btn edit" title="Edit"
                                    onclick="openEditModal({{ $d->toJson() }})">
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                {{-- Delete --}}
                                <button class="icon-btn del" title="Delete"
                                    onclick="askDelete({{ $d->id }}, '{{ addslashes($d->name) }}')">
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="7">
                            <div class="empty-state">
                                <svg style="width:44px;height:44px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p style="font-size:14px;font-weight:600;margin:0 0 4px;">No deductions found</p>
                                <p style="font-size:12px;margin:0;">Click <strong>Add Position</strong> to create your first deduction.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>{{-- /app-card --}}
    </div>{{-- /ded-page --}}

    {{-- ═══════════════════════════════════════
        ADD / EDIT MODAL
    ═══════════════════════════════════════ --}}
    <div id="formModal" class="modal-bg" onclick="if(event.target===this)closeModal()">
        <div class="modal-card">
            <div class="modal-head">
                <div>
                    <h3 id="modalTitle">Add Deduction</h3>
                    <p id="modalSub">Fill in the details below</p>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>

            <div class="modal-body">
                {{-- Name --}}
                <div>
                    <label class="f-label">Deduction Name <span style="color:#dc2626;">*</span></label>
                    <input id="f_name" class="f-input" type="text" placeholder="e.g. GSIS Employee Share" oninput="updatePreview()">
                    <p class="f-err" id="err_name">Name is required.</p>
                </div>

                {{-- Type & Rate Type --}}
                <div class="f-row2">
                    <div>
                        <label class="f-label">Type <span style="color:#dc2626;">*</span></label>
                        <select id="f_type" class="f-input f-select">
                            <option value="Fixed">Fixed</option>
                            <option value="Not Fixed">Not Fixed</option>
                        </select>
                    </div>
                    <div>
                        <label class="f-label">Rate Type <span style="color:#dc2626;">*</span></label>
                        <select id="f_rate_type" class="f-input f-select" onchange="updateRateLabel(); updatePreview();">
                            <option value="percent">Percentage (%)</option>
                            <option value="flat">Flat Amount (₱)</option>
                        </select>
                    </div>
                </div>

                {{-- Rate Value & Limit --}}
                <div class="f-row2">
                    <div>
                        <label class="f-label" id="rateLabel">Rate Value (%) <span style="color:#dc2626;">*</span></label>
                        <input id="f_rate_value" class="f-input" type="number" step="0.0001" min="0" placeholder="e.g. 9 for 9%" oninput="updatePreview()">
                        <p class="f-hint" id="rateHint">Enter percentage number (e.g. 9 for 9%, 2.5 for 2.5%)</p>
                    </div>
                    <div>
                        <label class="f-label">Limit / Cap (₱) <span style="font-weight:400;color:#9ca3af;">optional</span></label>
                        <input id="f_limit" class="f-input" type="number" step="0.01" min="0" placeholder="e.g. 2500 — leave blank for N/A" oninput="updatePreview()">
                    </div>
                </div>

                {{-- Status label --}}
                <div>
                    <label class="f-label">Status Label <span style="font-weight:400;color:#9ca3af;">optional — defaults to Name</span></label>
                    <input id="f_status" class="f-input" type="text" placeholder="Auto-filled from name">
                </div>

                {{-- Sort order --}}
                <div>
                    <label class="f-label">Sort Order</label>
                    <input id="f_sort" class="f-input" type="number" min="0" placeholder="0">
                </div>

                {{-- Preview --}}
                <div class="preview-box" id="previewBox" style="display:none;">
                    <div style="font-size:10px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Preview</div>
                    <div class="p-row"><span class="p-lbl">Name</span><span class="p-val" id="prev_name">—</span></div>
                    <div class="p-row"><span class="p-lbl">Rate</span><span class="p-val" id="prev_rate">—</span></div>
                    <div class="p-row"><span class="p-lbl">Limit</span><span class="p-val" id="prev_limit">—</span></div>
                </div>
            </div>

            <div class="modal-foot">
                <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="btn-save" id="saveBtn" onclick="saveDeduction()">💾 Save</button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
        DELETE CONFIRM MODAL
    ═══════════════════════════════════════ --}}
    <div id="confirmModal" class="confirm-modal" onclick="if(event.target===this)closeConfirm()">
        <div class="confirm-card">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:#fff1f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:22px;height:22px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#1f2937;margin:0 0 3px;">Delete Deduction?</h3>
                    <p style="font-size:12px;color:#6b7280;margin:0;" id="confirmName">This action cannot be undone.</p>
                </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button onclick="closeConfirm()" class="btn-cancel">Cancel</button>
                <button onclick="executeDelete()"
                    style="padding:9px 20px;font-size:12px;font-weight:700;color:#fff;background:#dc2626;border:none;border-radius:8px;cursor:pointer;transition:background .15s;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                    🗑 Delete
                </button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast">
        <div id="toastIcon" style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
        <div>
            <p id="toastTitle" style="font-size:13px;font-weight:700;color:#1f2937;margin:0;"></p>
            <p id="toastMsg"   style="font-size:11px;color:#6b7280;margin:2px 0 0;"></p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
        JAVASCRIPT
    ═══════════════════════════════════════ --}}
    <script>
    const CSRF = '{{ csrf_token() }}';
    let editingId   = null;
    let deletingId  = null;

    /* ════ SORT ════ */
    let sortCol = -1, sortAsc = true;
    function sortTable(col) {
        const tbody = document.getElementById('dedTbody');
        const rows  = [...tbody.querySelectorAll('tr[data-id]')];
        if (sortCol === col) sortAsc = !sortAsc; else { sortCol = col; sortAsc = true; }
        rows.sort((a, b) => {
            const va = a.cells[col]?.textContent.trim().toLowerCase() || '';
            const vb = b.cells[col]?.textContent.trim().toLowerCase() || '';
            return sortAsc ? va.localeCompare(vb) : vb.localeCompare(va);
        });
        document.querySelectorAll('.data-table th').forEach((th, i) => {
            th.classList.toggle('sorted', i === col);
            const arr = th.querySelector('.sort-arrow');
            if (arr) arr.textContent = i === col ? (sortAsc ? '↑' : '↓') : '↕';
        });
        rows.forEach(r => tbody.appendChild(r));
        updateRowNumbers();
    }

    /* ════ FILTER ════ */
    function filterRows(val) {
        const rows = document.querySelectorAll('#dedTbody tr[data-id]');
        let visible = 0;
        rows.forEach(r => {
            const name   = r.dataset.name || '';
            const type   = r.dataset.type || '';
            const status = (r.querySelector('td:nth-child(6)')?.textContent || '').toLowerCase();
            const match  = !val || name.includes(val.toLowerCase()) || type.includes(val.toLowerCase()) || status.includes(val.toLowerCase());
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        const empty = document.getElementById('emptyRow');
        if (empty) empty.style.display = visible === 0 ? '' : 'none';
    }

    function applyFilters() {
        const type   = document.getElementById('filterType')?.value || '';
        const active = document.getElementById('filterActive')?.value || '';
        const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const rows   = document.querySelectorAll('#dedTbody tr[data-id]');
        let visible  = 0;
        rows.forEach(r => {
            const mType   = !type   || r.dataset.type === type.toLowerCase();
            const mActive = active === '' || r.dataset.active === active;
            const mSearch = !search || (r.dataset.name || '').includes(search);
            r.style.display = (mType && mActive && mSearch) ? '' : 'none';
            if (mType && mActive && mSearch) visible++;
        });
        const empty = document.getElementById('emptyRow');
        if (empty) empty.style.display = visible === 0 ? '' : 'none';
    }

    function clearFilters() {
        document.getElementById('filterType').value   = '';
        document.getElementById('filterActive').value = '';
        document.getElementById('searchInput').value  = '';
        applyFilters();
    }

    let filterOpen = false;
    function toggleFilterPanel() {
        filterOpen = !filterOpen;
        const panel = document.getElementById('filterPanel');
        panel.style.display = filterOpen ? 'flex' : 'none';
    }

    /* ════ DRAG & DROP reorder ════ */
    let dragSrc = null;
    function dragStart(e) { dragSrc = e.currentTarget; e.currentTarget.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; }
    function dragOver(e)  { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; }
    function dragEnd(e)   { document.querySelectorAll('#dedTbody tr').forEach(r => r.classList.remove('dragging')); }
    function dragDrop(e)  {
        e.preventDefault();
        if (!dragSrc || dragSrc === e.currentTarget) return;
        const tbody = document.getElementById('dedTbody');
        const rows  = [...tbody.querySelectorAll('tr[data-id]')];
        const srcIdx = rows.indexOf(dragSrc);
        const tgtIdx = rows.indexOf(e.currentTarget);
        if (srcIdx < tgtIdx) e.currentTarget.after(dragSrc);
        else e.currentTarget.before(dragSrc);
        updateRowNumbers();
        saveOrder();
    }
    function saveOrder() {
        const ids = [...document.querySelectorAll('#dedTbody tr[data-id]')].map(r => r.dataset.id);
        fetch('/payroll/deductions/reorder', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ ids }),
        });
    }
    function updateRowNumbers() {
        document.querySelectorAll('#dedTbody tr[data-id]').forEach((r, i) => {
            const first = r.cells[0];
            if (first) first.textContent = i + 1;
        });
    }

    /* ════ MODAL ════ */
    function openAddModal() {
        editingId = null;
        document.getElementById('modalTitle').textContent = 'Add Deduction';
        document.getElementById('modalSub').textContent   = 'Fill in the details below';
        resetForm();
        document.getElementById('formModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('f_name').focus(), 200);
    }

    function openEditModal(d) {
        editingId = d.id;
        document.getElementById('modalTitle').textContent = 'Edit Deduction';
        document.getElementById('modalSub').textContent   = '#' + d.id + ' · ' + d.name;

        document.getElementById('f_name').value       = d.name || '';
        document.getElementById('f_type').value       = d.type || 'Fixed';
        document.getElementById('f_rate_type').value  = d.rate_type || 'flat';
        document.getElementById('f_status').value     = d.status || '';
        document.getElementById('f_sort').value       = d.sort_order || 0;
        document.getElementById('f_limit').value      = d.limit_amount || '';

        // Convert stored rate_value back to display (percent type stores as decimal e.g. 0.09)
        const rVal = parseFloat(d.rate_value || 0);
        if (d.rate_type === 'percent') {
            document.getElementById('f_rate_value').value = (rVal * 100).toString();
        } else {
            document.getElementById('f_rate_value').value = rVal.toString();
        }

        updateRateLabel();
        updatePreview();
        document.getElementById('formModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('formModal').classList.remove('show');
        document.body.style.overflow = '';
        editingId = null;
    }

    function resetForm() {
        ['f_name','f_rate_value','f_limit','f_status','f_sort'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        document.getElementById('f_type').value      = 'Fixed';
        document.getElementById('f_rate_type').value = 'percent';
        document.getElementById('previewBox').style.display = 'none';
        document.querySelectorAll('.f-err').forEach(e => e.style.display = 'none');
        updateRateLabel();
    }

    function updateRateLabel() {
        const isPercent = document.getElementById('f_rate_type').value === 'percent';
        document.getElementById('rateLabel').innerHTML = `Rate Value (${isPercent ? '%' : '₱'}) <span style="color:#dc2626;">*</span>`;
        document.getElementById('rateHint').textContent = isPercent
            ? 'Enter percentage number (e.g. 9 for 9%, 2.5 for 2.5%)'
            : 'Enter flat amount in pesos (e.g. 200, 2000)';
        document.getElementById('f_rate_value').placeholder = isPercent ? 'e.g. 9 for 9%' : 'e.g. 2000';
    }

    function updatePreview() {
        const name    = document.getElementById('f_name').value.trim();
        const rType   = document.getElementById('f_rate_type').value;
        const rVal    = parseFloat(document.getElementById('f_rate_value').value) || 0;
        const limit   = parseFloat(document.getElementById('f_limit').value);

        if (!name && !rVal) { document.getElementById('previewBox').style.display = 'none'; return; }
        document.getElementById('previewBox').style.display = '';

        let rateDisplay = '—';
        if (rVal > 0) {
            rateDisplay = rType === 'percent'
                ? rVal + '%'
                : '₱' + rVal.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        }

        document.getElementById('prev_name').textContent  = name || '—';
        document.getElementById('prev_rate').textContent  = rateDisplay;
        document.getElementById('prev_limit').textContent = (!isNaN(limit) && limit > 0)
            ? '₱' + limit.toLocaleString('en-PH', { minimumFractionDigits: 2 })
            : 'N/A';
    }

    /* ════ SAVE ════ */
    function saveDeduction() {
        const name     = document.getElementById('f_name').value.trim();
        const rVal     = document.getElementById('f_rate_value').value;
        const rType    = document.getElementById('f_rate_type').value;
        let   hasError = false;

        // Validate name
        const errName = document.getElementById('err_name');
        if (!name) { errName.style.display = 'block'; hasError = true; }
        else        { errName.style.display = 'none'; }

        if (hasError) return;

        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled    = true;
        saveBtn.textContent = 'Saving…';

        // Store percent as decimal fraction (9% → 0.09)
        const storedRateValue = rType === 'percent'
            ? (parseFloat(rVal) / 100)
            : parseFloat(rVal);

        const limit = document.getElementById('f_limit').value;
        const payload = {
            name:         name,
            type:         document.getElementById('f_type').value,
            rate_type:    rType,
            rate_value:   isNaN(storedRateValue) ? 0 : storedRateValue,
            limit_amount: limit !== '' ? parseFloat(limit) : null,
            status:       document.getElementById('f_status').value.trim() || name,
            sort_order:   parseInt(document.getElementById('f_sort').value) || 0,
        };

        const url    = editingId ? `/payroll/deductions/${editingId}` : '/payroll/deductions';
        const method = editingId ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(res => {
            saveBtn.disabled    = false;
            saveBtn.textContent = '💾 Save';
            if (res.success) {
                closeModal();
                showToast('Saved!', (editingId ? 'Updated' : 'Created') + ': ' + name, 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Error', 'Could not save deduction.', 'error');
            }
        })
        .catch(() => {
            saveBtn.disabled    = false;
            saveBtn.textContent = '💾 Save';
            showToast('Network Error', 'Please try again.', 'error');
        });
    }

    /* ════ DELETE ════ */
    function askDelete(id, name) {
        deletingId = id;
        document.getElementById('confirmName').textContent = `"${name}" will be permanently deleted.`;
        document.getElementById('confirmModal').classList.add('show');
    }
    function closeConfirm() {
        document.getElementById('confirmModal').classList.remove('show');
        deletingId = null;
    }
    function executeDelete() {
        if (!deletingId) return;
        fetch(`/payroll/deductions/${deletingId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(res => {
            closeConfirm();
            if (res.success) {
                const row = document.querySelector(`tr[data-id="${deletingId}"]`);
                if (row) {
                    row.style.transition = 'opacity .3s, transform .3s';
                    row.style.opacity    = '0';
                    row.style.transform  = 'translateX(20px)';
                    setTimeout(() => { row.remove(); updateRowNumbers(); }, 320);
                }
                showToast('Deleted', 'Deduction removed.', 'success');
            } else {
                showToast('Error', 'Could not delete.', 'error');
            }
        })
        .catch(() => showToast('Network Error', 'Please try again.', 'error'));
    }

    /* ════ TOAST ════ */
    function showToast(title, msg, type) {
        const map = {
            success: { bg:'#dcfce7', c:'#16a34a', p:'M5 13l4 4L19 7' },
            error:   { bg:'#fee2e2', c:'#dc2626', p:'M6 18L18 6M6 6l12 12' },
            info:    { bg:'#dbeafe', c:'#2563eb', p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        };
        const s = map[type] || map.info;
        document.getElementById('toastTitle').textContent  = title;
        document.getElementById('toastMsg').textContent    = msg;
        document.getElementById('toastIcon').innerHTML     = `<svg style="width:16px;height:16px;" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
        document.getElementById('toastIcon').style.background = s.bg;
        const t = document.getElementById('toast');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3200);
    }

    /* ════ KEYBOARD ════ */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(); closeConfirm(); }
        if (e.key === 'Enter' && document.getElementById('formModal').classList.contains('show')) {
            e.preventDefault();
            saveDeduction();
        }
    });
    </script>
    @endsection
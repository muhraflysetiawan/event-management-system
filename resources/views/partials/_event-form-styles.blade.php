{{-- Shared styles for survey & requirements sections in event create/edit --}}
<style>
    /* Section headers */
    .event-section { margin-top: 2.5rem; margin-bottom: 2rem; }
    .event-section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    .event-section-header i { color: #980517; }
    .event-section-subtitle {
        color: #980517;
        font-size: 0.85rem;
        font-style: italic;
        margin-bottom: 1.5rem;
    }
    /* Survey question cards */
    .sq-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        margin-bottom: 0.75rem;
    }
    .sq-card:hover { border-color: var(--border-color-strong); }
    .sq-layout {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }
    .sq-field-text { flex: 1; min-width: 0; }
    .sq-field-type { width: 140px; flex-shrink: 0; }
    .sq-field-mandatory {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        padding-bottom: 0.15rem;
    }
    .sq-field-delete {
        flex-shrink: 0;
        padding-bottom: 0.15rem;
    }
    .sq-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.35rem;
    }
    .sq-input {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.875rem;
        color: var(--text-primary);
        background: #fff;
        outline: none;
    }
    .sq-input:focus {
        border-color: #980517;
        box-shadow: 0 0 0 3px rgba(152, 5, 23, 0.08);
    }
    .sq-select {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.85rem;
        color: var(--text-primary);
        background: #fff;
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1rem;
        padding-right: 2rem;
    }
    .sq-select:focus {
        border-color: #980517;
        box-shadow: 0 0 0 3px rgba(152, 5, 23, 0.08);
    }
    .sq-mandatory {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }
    .sq-mandatory input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #980517;
    }
    .sq-btn-delete {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border: none;
        border-radius: var(--radius-sm);
        color: #fff;
        cursor: pointer;
        font-size: 0.85rem;
    }
    .sq-btn-delete:hover { background: linear-gradient(135deg, #b91c1c, #dc2626); }
    .sq-btn-add {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, #980517, #c0392b);
        border: none;
        border-radius: var(--radius-sm);
        color: #fff;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        font-family: inherit;
        margin-top: 0.25rem;
    }
    .sq-btn-add:hover { background: linear-gradient(135deg, #7f0413, #980517); }
    /* Requirements row */
    .req-row {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        align-items: center;
    }
    .req-row .sq-input { flex: 1; }
    /* Checkbox group */
    .checkbox-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .checkbox-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #980517;
    }
    .approval-box {
        background: #fdf2f2;
        padding: 1rem 1.25rem;
        border-radius: var(--radius-sm);
        border: 1px solid #fecaca;
    }
    /* Responsive */
    @media (max-width: 768px) {
        .sq-layout {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        .sq-field-type { width: 100%; }
    }
</style>

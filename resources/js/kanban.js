import Sortable from 'sortablejs';
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

function bootColumn(el) {
    return new Sortable(el, {
        group: 'tickets',
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: function (evt) {
            const item = evt.item;
            const ticketId = parseInt(item.getAttribute('data-ticket-id'), 10);
            const newStatus = evt.to.getAttribute('data-status');
            if (ticketId && newStatus) {
                Livewire.dispatch('kanban:moved', { ticketId, newStatus });
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-kanban]');
    if (!root) return;
    const ids = [
        root.getAttribute('data-col-open'),
        root.getAttribute('data-col-in_progress'),
        root.getAttribute('data-col-closed'),
    ].filter(Boolean);

    ids.forEach((id) => {
        const col = document.getElementById(id);
        if (col) bootColumn(col);
    });
});

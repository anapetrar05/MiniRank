document.addEventListener('DOMContentLoaded', () => {
    const refreshBtn = document.getElementById('refresh-btn');
    if (!refreshBtn) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    refreshBtn.addEventListener('click', async () => {
        refreshBtn.disabled = true;
        refreshBtn.textContent = 'Refreshing…';

        try {
            const res = await fetch('/api/refresh.php', {
                method: 'POST',
                headers: { 'X-CSRF-Token': csrfToken },
            });
            if (!res.ok) throw new Error('Server responded with ' + res.status);

            const data = await res.json();
            updateRows(data.keywords || []);
        } catch (err) {
            alert('Refresh failed: ' + err.message);
        } finally {
            refreshBtn.disabled = false;
            refreshBtn.textContent = 'Refresh positions';
        }
    });
});

function updateRows(keywords) {
    for (const kw of keywords) {
        const row = document.querySelector('tr[data-id="' + kw.id + '"]');
        if (!row) continue;

        row.querySelector('.td-position').textContent = kw.position;
        row.querySelector('.td-trend').innerHTML = trendBadge(kw.trend);
    }
}

function trendBadge(trend) {
    const labels = { improved: 'Improved ↑', declined: 'Declined ↓', stable: 'Stable' };
    const classes = {
        improved: 'badge badge-good',
        declined: 'badge badge-bad',
        stable: 'badge badge-neutral',
    };

    const label = labels[trend] || 'Stable';
    const cls = classes[trend] || 'badge badge-neutral';

    return '<span class="' + cls + '">' + label + '</span>';
}
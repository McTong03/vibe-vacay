<section class="search-section">
    <div style="position:relative; width:600px;">
        <form method="GET" action="filter-search.php" class="search-bar" onsubmit="saveAndSearch(event)">
            <input type="text" name="q" id="search-input"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                placeholder="Find places and things to do"
                autocomplete="off">
            <button type="submit">Search</button>
        </form>

        <div id="search-dropdown" style="
            display:none;
            position:absolute;
            top:calc(100% + 8px);
            left:0; right:0;
            background:white;
            border:1px solid #ddd;
            border-radius:16px;
            box-shadow:0 4px 12px rgba(0,0,0,0.15);
            z-index:9999;
            overflow:hidden;
        "></div>
    </div>
</section>

<script>
    const BASE_PATH = window.location.origin +
        window.location.pathname.replace(/\/[^/]*$/, '');
    const searchInput = document.getElementById('search-input');
    const dropdown = document.getElementById('search-dropdown');
    let debounceTimer = null;

    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim() === '') {
            showHistory();
        }
    });

    searchInput.addEventListener('input', () => {
        const q = searchInput.value.trim();
        clearTimeout(debounceTimer);

        if (q === '') {
            showHistory();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${BASE_PATH}/get-search-suggestions.php?type=suggest&q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.suggestions || data.suggestions.length === 0) {
                        hideDropdown();
                        return;
                    }
                    renderDropdown(data.suggestions.map(s => ({
                        label: s.destination_name,
                        sublabel: s.state_name,
                        icon: '📍',
                        id: s.destination_id
                    })));
                })
                .catch(console.error);
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    function showHistory() {
        fetch(`${BASE_PATH}/get-search-suggestions.php?type=history`)
            .then(r => r.json())
            .then(data => {
                if (!data.history || data.history.length === 0) {
                    hideDropdown();
                    return;
                }
                renderDropdown(data.history.map(h => ({
                    label: h,
                    sublabel: null,
                    icon: '🕐',
                    id: null
                })));
            })
            .catch(console.error);
    }

    function renderDropdown(items) {
        dropdown.innerHTML = items.map((item, i) => `
        <div onclick="selectItem(${JSON.stringify(item.label)}, ${item.id ?? 'null'})"
             style="padding:12px 16px; cursor:pointer; display:flex; align-items:center; gap:10px;
                    border-bottom:${i < items.length-1 ? '1px solid #f0f0f0' : 'none'};
                    background:white;"
             onmouseover="this.style.background='#f8f8f8'"
             onmouseout="this.style.background='white'">
            <span style="font-size:18px;">${item.icon}</span>
            <div>
                <div style="font-size:14px; color:#1e293b; font-weight:500;">${escHtmlStr(item.label)}</div>
                ${item.sublabel ? `<div style="font-size:12px; color:#9ca3af;">${escHtmlStr(item.sublabel)}</div>` : ''}
            </div>
        </div>
    `).join('');
        dropdown.style.display = 'block';
    }

    function selectItem(label, destinationId) {
        hideDropdown();
        saveHistory(label);
        if (destinationId) {
            window.location.href = `destination-description.php?id=${destinationId}`;
        } else {
            window.location.href = `filter-search.php?q=${encodeURIComponent(label)}`;
        }
    }

    function saveAndSearch(e) {
        e.preventDefault();
        const q = searchInput.value.trim();
        if (!q) return;
        saveHistory(q);
        window.location.href = `filter-search.php?q=${encodeURIComponent(q)}`;
    }

    function saveHistory(keyword) {
        fetch(`${BASE_PATH}/save-search-history.php`, {
            method: 'POST',
            body: new URLSearchParams({
                keyword
            })
        });
    }

    function hideDropdown() {
        dropdown.style.display = 'none';
    }

    function escHtmlStr(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
</script>
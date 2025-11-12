<header class="topbar">
    <div class="search">
        <input type="search" placeholder="Search orders, products, customers..." id="globalSearch">
    </div>
    <div class="top-actions">
        <button id="openAddStaff" class="btn small">Add Delivery Staff</button>
        <button id="darkModeToggle" class="btn small" aria-pressed="false">Dark Mode</button>
        <div class="user-welcome">Hi, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> (Admin)</div>
        <a href="../logout.php" class="btn small" style="color: #ffcc00; border-color: rgba(255, 204, 0, 0.15);">Logout</a>
    </div>
</header>
<script>
// Admin global search: filters visible admin tables (orders, products, customers, delivery staff)
(function(){
    const input = document.getElementById('globalSearch');
    if (!input) return;

    // debounce helper
    function debounce(fn, wait){ let t; return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }; }

    const tableSelectors = ['#ordersTable', '#productsTable', '.data-table', '.orders-table'];

    function getTables(){
        const tables = [];
        tableSelectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(t => { if (t && t.tagName === 'TABLE') tables.push(t); });
        });
        return tables;
    }

    function applyFilter(q){
        const query = (q||'').toLowerCase().trim();
        const tables = getTables();
        if (tables.length === 0) return;
        tables.forEach(table => {
            const tbody = table.tBodies[0];
            if (!tbody) return;
            Array.from(tbody.rows).forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = query === '' || text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    }

    const handler = debounce(function(e){ applyFilter(e.target.value); }, 150);
    input.addEventListener('input', handler);

    // allow pressing ESC to clear
    input.addEventListener('keydown', function(e){ if (e.key === 'Escape') { input.value=''; applyFilter(''); } });
})();
</script>
<script src="../scripts/dark-mode.js"></script>

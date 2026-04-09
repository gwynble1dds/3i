</div>
</main>


<div id="toast" class="fixed bottom-5 right-5 transform translate-y-20 opacity-0 transition-all duration-300 z-50">
    <div class="bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <i id="toast-icon" class="fas fa-check-circle text-emerald-400"></i>
        <span id="toast-message">Operation successful</span>
    </div>
</div>

<script>

    function initClock() {
        function update() {
            const now = new Date();
            document.getElementById('live-clock').textContent = now.toLocaleTimeString('en-US', { hour12: false });
            document.getElementById('live-date').textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        update();
        setInterval(update, 1000);
    }
    initClock();


    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('mobile-overlay').classList.toggle('hidden');
    }


    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').textContent = message;
        document.getElementById('toast-icon').className = type === 'success' ? 'fas fa-check-circle text-emerald-400' : 'fas fa-exclamation-circle text-red-400';
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
    }
</script>
</body>

</html>
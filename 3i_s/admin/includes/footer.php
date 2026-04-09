</div>
</main>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-5 right-5 transform translate-y-20 opacity-0 transition-all duration-300 z-50">
    <div class="bg-gray-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <i id="toast-icon" class="fas fa-check-circle text-indigo-400"></i>
        <span id="toast-message">Success</span>
    </div>
</div>

<!-- New User Pop-up Notification -->
<div id="notif-popup" class="fixed top-6 right-6 z-[100] hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 max-w-sm border border-indigo-400/30" style="animation: notifSlideIn 0.4s ease-out;">
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-plus text-xl"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-sm">New User Registered!</p>
            <p id="notif-popup-detail" class="text-xs text-indigo-200 truncate mt-0.5"></p>
        </div>
        <button onclick="closeNotifPopup()" class="text-white/60 hover:text-white transition-colors flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>

    // ========== Clock ==========
    (function () {
        function update() {
            const el = document.getElementById('admin-clock');
            if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
        }
        update(); setInterval(update, 1000);
    })();

    // ========== Sidebar Toggle ==========
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('mobile-overlay').classList.toggle('hidden');
    }

    // ========== Toast ==========
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').textContent = message;
        document.getElementById('toast-icon').className = type === 'success' ? 'fas fa-check-circle text-indigo-400' : 'fas fa-exclamation-circle text-red-400';
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
    }

    // ========== Notification Sound (Web Audio API) ==========
    function playNotificationSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            
            // First tone - pleasant chime
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(830, ctx.currentTime);
            osc1.frequency.setValueAtTime(1050, ctx.currentTime + 0.1);
            gain1.gain.setValueAtTime(0.3, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(ctx.currentTime);
            osc1.stop(ctx.currentTime + 0.5);

            // Second tone - higher chime for a pleasant ding-dong
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1200, ctx.currentTime + 0.15);
            osc2.frequency.setValueAtTime(1400, ctx.currentTime + 0.25);
            gain2.gain.setValueAtTime(0, ctx.currentTime);
            gain2.gain.setValueAtTime(0.25, ctx.currentTime + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.7);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime + 0.15);
            osc2.stop(ctx.currentTime + 0.7);

            // Third tone - resolution
            const osc3 = ctx.createOscillator();
            const gain3 = ctx.createGain();
            osc3.type = 'sine';
            osc3.frequency.setValueAtTime(1600, ctx.currentTime + 0.35);
            gain3.gain.setValueAtTime(0, ctx.currentTime);
            gain3.gain.setValueAtTime(0.2, ctx.currentTime + 0.35);
            gain3.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.9);
            osc3.connect(gain3);
            gain3.connect(ctx.destination);
            osc3.start(ctx.currentTime + 0.35);
            osc3.stop(ctx.currentTime + 0.9);

        } catch (e) {
            console.log('Audio not available:', e);
        }
    }

    // ========== Notification System ==========
    let lastCheckTime = null;
    let notifications = [];
    let notifDropdownOpen = false;

    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        notifDropdownOpen = !notifDropdownOpen;
        dropdown.classList.toggle('hidden', !notifDropdownOpen);
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notif-wrapper');
        if (wrapper && !wrapper.contains(e.target) && notifDropdownOpen) {
            notifDropdownOpen = false;
            document.getElementById('notif-dropdown').classList.add('hidden');
        }
    });

    function clearNotifications() {
        notifications = [];
        renderNotifications();
        updateBadge(0);
    }

    function closeNotifPopup() {
        document.getElementById('notif-popup').classList.add('hidden');
    }

    function showNotifPopup(username) {
        const popup = document.getElementById('notif-popup');
        document.getElementById('notif-popup-detail').textContent = username + ' just signed up';
        popup.classList.remove('hidden');
        // Auto hide after 5 seconds
        setTimeout(() => popup.classList.add('hidden'), 5000);
    }

    function updateBadge(count) {
        const badge = document.getElementById('notif-badge');
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }

    function renderNotifications() {
        const list = document.getElementById('notif-list');
        if (notifications.length === 0) {
            list.innerHTML = '<div class="p-6 text-center text-gray-500 text-sm"><i class="fas fa-check-circle text-2xl mb-2 block text-gray-600"></i>No new notifications</div>';
            return;
        }
        list.innerHTML = notifications.map((n, i) => `
            <div class="px-4 py-3 border-b border-gray-700 hover:bg-gray-700/50 transition-colors notif-item-anim" style="animation-delay: ${i * 0.05}s">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-user-plus text-indigo-400 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white font-medium">New User Registered</p>
                        <p class="text-xs text-gray-400 mt-0.5"><span class="text-indigo-300 font-semibold">${n.username}</span> · ${n.email}</p>
                        <p class="text-[10px] text-gray-500 mt-1"><i class="fas fa-clock mr-1"></i>${n.time_ago}</p>
                    </div>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full flex-shrink-0 mt-2"></span>
                </div>
            </div>
        `).join('');
        updateBadge(notifications.length);
    }

    function timeAgo(dateStr) {
        const now = new Date();
        const date = new Date(dateStr);
        const diff = Math.floor((now - date) / 1000);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return Math.floor(diff / 86400) + 'd ago';
    }

    function checkNewUsers() {
        let url = 'api/admin_api.php?action=check_new_users';
        if (lastCheckTime) url += '&since=' + encodeURIComponent(lastCheckTime);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    lastCheckTime = data.server_time;

                    if (data.new_users.length > 0) {
                        // Play sound and show popup for each new user
                        playNotificationSound();
                        
                        data.new_users.forEach(u => {
                            notifications.unshift({
                                id: u.id,
                                username: u.username,
                                email: u.email,
                                time_ago: timeAgo(u.created_at)
                            });
                        });

                        // Show popup for the latest user
                        showNotifPopup(data.new_users[0].username);
                        
                        // Animate bell
                        const bell = document.getElementById('bell-icon');
                        bell.classList.remove('bell-ring');
                        void bell.offsetWidth; // force reflow
                        bell.classList.add('bell-ring');

                        renderNotifications();
                    }

                    // Update sidebar badge
                    updateBadge(notifications.length > 0 ? notifications.length : 0);
                }
            })
            .catch(err => console.log('Notification check failed:', err));
    }

    // ========== Duty Officer ==========
    function loadDutyOfficer() {
        fetch('api/admin_api.php?action=get_duty_officer')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('duty-officer-badge');
                const nameEl = document.getElementById('duty-officer-name');
                if (data.success && data.on_duty.length > 0) {
                    const officer = data.on_duty[0];
                    nameEl.textContent = officer.name;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            })
            .catch(err => console.log('Duty officer check failed:', err));
    }

    // ========== Initialize ==========
    document.addEventListener('DOMContentLoaded', function() {
        // Initial load
        checkNewUsers();
        loadDutyOfficer();

        // Poll every 15 seconds for new users
        setInterval(checkNewUsers, 15000);

        // Refresh duty officer every 60 seconds
        setInterval(loadDutyOfficer, 60000);
    });

</script>
</body>

</html>
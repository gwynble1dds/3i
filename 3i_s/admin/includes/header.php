<?php
require_once __DIR__ . '/../../db_config.php';
requireAdminLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$page_titles = [
    'dashboard' => 'Admin Dashboard',
    'approve_users' => 'Approve Users',
    'manage_students' => 'Manage Students',
    'manage_medical' => 'Medical Records',
    'settings' => 'Admin Settings'
];
$page_title = $page_titles[$current_page] ?? 'Admin';


$pending_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FEPC Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }


        @keyframes bellRing {
            0% {
                transform: rotate(0);
            }

            10% {
                transform: rotate(14deg);
            }

            20% {
                transform: rotate(-14deg);
            }

            30% {
                transform: rotate(10deg);
            }

            40% {
                transform: rotate(-10deg);
            }

            50% {
                transform: rotate(6deg);
            }

            60% {
                transform: rotate(-6deg);
            }

            70% {
                transform: rotate(2deg);
            }

            80% {
                transform: rotate(-2deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        .bell-ring {
            animation: bellRing 0.8s ease-in-out;
        }

        @keyframes badgePulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            50% {
                transform: scale(1.15);
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }
        }

        .badge-pulse {
            animation: badgePulse 1.5s ease-in-out infinite;
        }

        @keyframes notifSlideIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .notif-dropdown {
            animation: notifSlideIn 0.2s ease-out;
        }

        @keyframes notifItemSlide {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .notif-item-anim {
            animation: notifItemSlide 0.3s ease-out forwards;
        }


        @keyframes dutyGlow {

            0%,
            100% {
                box-shadow: 0 0 4px rgba(16, 185, 129, 0.4);
            }

            50% {
                box-shadow: 0 0 12px rgba(16, 185, 129, 0.7);
            }
        }

        .duty-glow {
            animation: dutyGlow 2.5s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-200 h-screen overflow-hidden flex">

    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"
        onclick="toggleSidebar()"></div>

    <aside id="sidebar"
        class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-gray-800 text-white transform -translate-x-full lg:translate-x-0 sidebar-transition flex flex-col shadow-xl">
        <div class="h-20 flex items-center justify-center border-b border-gray-700 bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center shadow-md">
                    <i class="fas fa-shield-alt text-lg"></i>
                </div>
                <span class="text-xl font-bold tracking-wide">FEPC Admin</span>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-2">
            <a href="dashboard.php"
                class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-700 <?php echo $current_page === 'dashboard' ? 'bg-indigo-600 shadow-md' : 'text-gray-400'; ?>">
                <i class="fas fa-tachometer-alt w-6 text-center"></i><span class="font-medium">Dashboard</span>
            </a>
            <a href="approve_users.php"
                class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-700 <?php echo $current_page === 'approve_users' ? 'bg-indigo-600 shadow-md' : 'text-gray-400'; ?>">
                <i class="fas fa-user-check w-6 text-center"></i><span class="font-medium">Approve Users</span>
                <?php if ($pending_count > 0): ?>
                    <span
                        class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="manage_students.php"
                class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-700 <?php echo $current_page === 'manage_students' ? 'bg-indigo-600 shadow-md' : 'text-gray-400'; ?>">
                <i class="fas fa-users w-6 text-center"></i><span class="font-medium">Students</span>
            </a>
            <a href="manage_medical.php"
                class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-700 <?php echo $current_page === 'manage_medical' ? 'bg-indigo-600 shadow-md' : 'text-gray-400'; ?>">
                <i class="fas fa-heartbeat w-6 text-center"></i><span class="font-medium">Medical Records</span>
            </a>
            <a href="duty_list.php"
                class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-700 <?php echo $current_page === 'duty_list' ? 'bg-indigo-600 shadow-md' : 'text-gray-400'; ?>">
                <i class="fas fa-clipboard-list w-6 text-center"></i><span class="font-medium">Clinic Duty</span>
            </a>
            <a href="settings.php"
                class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-700 <?php echo $current_page === 'settings' ? 'bg-indigo-600 shadow-md' : 'text-gray-400'; ?>">
                <i class="fas fa-cog w-6 text-center"></i><span class="font-medium">Settings</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700 bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold">A</div>
                <div class="flex-1">
                    <p class="text-sm font-semibold"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <a href="logout.php" class="text-gray-500 hover:text-white"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-20 bg-gray-800 shadow-sm flex items-center justify-between px-6 z-10 border-b border-gray-700">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white"><i
                        class="fas fa-bars text-2xl"></i></button>
                <h2 class="text-2xl font-bold text-white"><?php echo $page_title; ?></h2>
            </div>
            <div class="flex items-center gap-4">

                <div id="duty-officer-badge"
                    class="hidden items-center gap-2 bg-emerald-900/50 border border-emerald-600/40 px-4 py-2 rounded-lg duty-glow">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                    <span class="text-xs text-emerald-300 font-medium hidden sm:inline">On Duty:</span>
                    <span id="duty-officer-name" class="text-sm font-bold text-emerald-200"></span>
                </div>


                <div class="relative" id="notif-wrapper">
                    <button id="notif-bell-btn" onclick="toggleNotifDropdown()"
                        class="relative p-2 text-gray-400 hover:text-white transition-colors rounded-lg hover:bg-gray-700">
                        <i class="fas fa-bell text-xl" id="bell-icon"></i>
                        <span id="notif-badge"
                            class="hidden absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center badge-pulse">0</span>
                    </button>

                    <div id="notif-dropdown"
                        class="hidden absolute right-0 top-full mt-2 w-80 bg-gray-800 border border-gray-600 rounded-xl shadow-2xl overflow-hidden notif-dropdown z-50">
                        <div class="px-4 py-3 bg-gray-750 border-b border-gray-600 flex items-center justify-between">
                            <h4 class="text-sm font-bold text-white flex items-center gap-2"><i
                                    class="fas fa-bell text-indigo-400"></i> Notifications</h4>
                            <button onclick="clearNotifications()"
                                class="text-xs text-gray-400 hover:text-red-400 transition-colors"><i
                                    class="fas fa-trash-alt mr-1"></i>Clear</button>
                        </div>
                        <div id="notif-list" class="max-h-72 overflow-y-auto">
                            <div class="p-6 text-center text-gray-500 text-sm"><i
                                    class="fas fa-check-circle text-2xl mb-2 block text-gray-600"></i>No new
                                notifications</div>
                        </div>
                        <div class="px-4 py-2.5 bg-gray-750 border-t border-gray-600 text-center">
                            <a href="approve_users.php"
                                class="text-xs text-indigo-400 hover:text-indigo-300 font-medium transition-colors"><i
                                    class="fas fa-external-link-alt mr-1"></i>View All Pending Users</a>
                        </div>
                    </div>
                </div>


                <div class="flex items-center gap-2 bg-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-clock text-indigo-400"></i>
                    <span id="admin-clock" class="font-mono font-semibold text-gray-300">--:--:--</span>
                </div>
            </div>
        </header>
        <div class="flex-1 overflow-y-auto p-6 bg-gray-900">
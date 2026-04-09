<?php
require_once __DIR__ . '/../db_config.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$page_titles = [
    'dashboard' => 'Dashboard',
    'student_list' => 'Student Registry',
    'medical_records' => 'Medical Records',
    'settings' => 'Settings'
];
$page_title = $page_titles[$current_page] ?? 'FEPC';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FEPC Medical Student Record</title>
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

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

        .medical-icon-btn {
            transition: all 0.2s ease;
        }

        .medical-icon-btn.selected {
            background-color: #ecfdf5;
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .medical-icon-btn .indicator-dot {
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .medical-icon-btn.selected .indicator-dot {
            opacity: 1;
            transform: scale(1);
        }

        .medical-tab {
            position: relative;
            transition: all 0.3s ease;
        }

        .medical-tab.active {
            color: #059669;
            border-bottom: 2px solid #059669;
        }

        .medical-tab:hover:not(.active) {
            color: #374151;
            background-color: #f9fafb;
        }

        .digital-clock {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.05em;
        }

        .officer-card {
            transition: all 0.3s ease;
        }

        .officer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .officer-status {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .searchable-dropdown {
            position: relative;
            width: 100%;
        }

        .searchable-dropdown-input {
            width: 100%;
            padding: 0.5rem 2.5rem 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .searchable-dropdown-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .searchable-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.25rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            max-height: 250px;
            overflow-y: auto;
            z-index: 50;
            display: none;
        }

        .searchable-dropdown-menu.show {
            display: block;
        }

        .searchable-dropdown-search {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            background: white;
        }

        .searchable-dropdown-search input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .searchable-dropdown-search input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .searchable-dropdown-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .searchable-dropdown-item {
            padding: 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.15s;
        }

        .searchable-dropdown-item:hover {
            background-color: #eff6ff;
        }

        .searchable-dropdown-item.selected {
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: 500;
        }

        .searchable-dropdown-item.no-results {
            color: #9ca3af;
            cursor: default;
            text-align: center;
            padding: 1rem;
        }

        .searchable-dropdown-clear {
            position: absolute;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            display: none;
        }

        .searchable-dropdown-clear.show {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 h-screen overflow-hidden flex">


    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"
        onclick="toggleSidebar()"></div>


    <aside id="sidebar"
        class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-emerald-600 text-white transform -translate-x-full lg:translate-x-0 sidebar-transition flex flex-col shadow-xl">
        <div class="h-20 flex items-center justify-center border-b border-emerald-500 bg-emerald-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden shadow-md">
                    <img src="https://i.imgur.com/viB2Fee.jpeg" alt="Logo" class="w-full h-full object-cover">
                </div>
                <span class="text-xl font-bold tracking-wide">FEPC</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-2">
            <a href="dashboard.php"
                class="nav-item w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-emerald-500 <?php echo $current_page === 'dashboard' ? 'bg-emerald-500 shadow-md text-white' : 'text-emerald-100'; ?>">
                <i class="fas fa-home w-6 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="student_list.php"
                class="nav-item w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-emerald-500 <?php echo $current_page === 'student_list' ? 'bg-emerald-500 shadow-md text-white' : 'text-emerald-100'; ?>">
                <i class="fas fa-users w-6 text-center"></i>
                <span class="font-medium">Student List</span>
            </a>
            <a href="medical_records.php"
                class="nav-item w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-emerald-500 <?php echo $current_page === 'medical_records' ? 'bg-emerald-500 shadow-md text-white' : 'text-emerald-100'; ?>">
                <i class="fas fa-heartbeat w-6 text-center"></i>
                <span class="font-medium">Medical Records</span>
            </a>
            <a href="settings.php"
                class="nav-item w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-emerald-500 <?php echo $current_page === 'settings' ? 'bg-emerald-500 shadow-md text-white' : 'text-emerald-100'; ?>">
                <i class="fas fa-cog w-6 text-center"></i>
                <span class="font-medium">Settings</span>
            </a>
        </nav>

        <div class="p-4 border-t border-emerald-500 bg-emerald-700">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=10b981&color=fff"
                    alt="User" class="w-10 h-10 rounded-full border-2 border-emerald-400">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p class="text-xs text-emerald-200 truncate"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                </div>
                <a href="logout.php" class="text-emerald-200 hover:text-white" title="Logout"><i
                        class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </aside>


    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()"
                    class="lg:hidden text-gray-500 hover:text-emerald-600 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo $page_title; ?></h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg">
                    <i class="fas fa-clock text-emerald-600"></i>
                    <span id="live-clock" class="digital-clock font-mono font-semibold text-gray-700">--:--:--</span>
                    <span id="live-date" class="text-sm text-gray-500 ml-2">--/--/----</span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 bg-gray-50 relative">
<?php include 'includes/header.php'; ?>

<div class="fade-in h-full flex flex-col space-y-6">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-2xl shadow-lg">
                    <i class="fas fa-clinic-medical"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">School Clinic</h2>
                    <p class="text-gray-500">Medical Records Management System</p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Medical Officer on Duty</p>
                    <select id="current-officer-select"
                        class="mt-1 block w-48 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm bg-white font-medium"></select>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-user-md text-xl"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden flex-1 flex flex-col">
        <div class="flex border-b border-gray-200">
            <button onclick="switchMedicalTab('patients')" id="tab-patients"
                class="medical-tab active flex-1 py-4 px-6 text-sm font-semibold text-gray-600 flex items-center justify-center gap-2"><i
                    class="fas fa-users-medical"></i> Medical Patients</button>
            <button onclick="switchMedicalTab('visits')" id="tab-visits"
                class="medical-tab flex-1 py-4 px-6 text-sm font-semibold text-gray-600 flex items-center justify-center gap-2"><i
                    class="fas fa-clipboard-list"></i> Visit Log</button>
            <button onclick="switchMedicalTab('officers')" id="tab-officers"
                class="medical-tab flex-1 py-4 px-6 text-sm font-semibold text-gray-600 flex items-center justify-center gap-2"><i
                    class="fas fa-user-md"></i> Officer Log</button>
        </div>


        <div id="medical-tab-patients" class="medical-tab-content p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Medical Patients Registry</h3>
                    <p class="text-sm text-gray-500">Students with registered medical conditions</p>
                </div>
                <button onclick="openMedicalStudentModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-all flex items-center gap-2"><i
                        class="fas fa-user-plus"></i> Register New</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-red-50 border border-red-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-red-600 uppercase">Total Patients</p>
                    <h3 class="text-2xl font-bold text-red-700" id="stat-patients">0</h3>
                </div>
                <div class="bg-orange-50 border border-orange-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-orange-600 uppercase">Critical Cases</p>
                    <h3 class="text-2xl font-bold text-orange-700" id="stat-critical">0</h3>
                </div>
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-blue-600 uppercase">Today's Visits</p>
                    <h3 class="text-2xl font-bold text-blue-700" id="stat-today-v">0</h3>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-emerald-600 uppercase">Active in Clinic</p>
                    <h3 class="text-2xl font-bold text-emerald-700" id="stat-active">0</h3>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 items-center bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
                <span class="text-sm font-semibold text-gray-600">Filter by Condition:</span>
                <button onclick="filterPatients('all')"
                    class="med-filter-btn px-3 py-1 text-sm rounded-full bg-emerald-600 text-white transition-all"
                    data-c="all">All</button>
                <button onclick="filterPatients('Heart')"
                    class="med-filter-btn px-3 py-1 text-sm rounded-full bg-white border border-gray-300 text-gray-600 transition-all"
                    data-c="Heart">❤️ Heart</button>
                <button onclick="filterPatients('Eyesight')"
                    class="med-filter-btn px-3 py-1 text-sm rounded-full bg-white border border-gray-300 text-gray-600 transition-all"
                    data-c="Eyesight">👁️ Eyesight</button>
                <button onclick="filterPatients('Bone/Muscle')"
                    class="med-filter-btn px-3 py-1 text-sm rounded-full bg-white border border-gray-300 text-gray-600 transition-all"
                    data-c="Bone/Muscle">🦴 Bone</button>
                <button onclick="filterPatients('Organ')"
                    class="med-filter-btn px-3 py-1 text-sm rounded-full bg-white border border-gray-300 text-gray-600 transition-all"
                    data-c="Organ">🫁 Organ</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b">Patient ID</th>
                            <th class="p-4 font-semibold border-b">Student Name</th>
                            <th class="p-4 font-semibold border-b">Conditions</th>
                            <th class="p-4 font-semibold border-b">Severity</th>
                            <th class="p-4 font-semibold border-b">Registered</th>
                            <th class="p-4 font-semibold border-b text-center">Status</th>
                            <th class="p-4 font-semibold border-b text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patients-table" class="text-gray-700 text-sm"></tbody>
                </table>
            </div>
        </div>


        <div id="medical-tab-visits" class="medical-tab-content hidden p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Clinic Visit Log</h3>
                    <p class="text-sm text-gray-500">Track student visits</p>
                </div>
                <button onclick="openVisitModal()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md transition-all flex items-center gap-2"><i
                        class="fas fa-plus"></i> New Visit</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Today's Visits</p>
                            <h3 class="text-2xl font-bold text-blue-600" id="v-stat-today">0</h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg text-blue-600"><i class="fas fa-calendar-day text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Currently In</p>
                            <h3 class="text-2xl font-bold text-orange-600" id="v-stat-active">0</h3>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg text-orange-600"><i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">This Week</p>
                            <h3 class="text-2xl font-bold text-emerald-600" id="v-stat-week">0</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600"><i
                                class="fas fa-calendar-week text-xl"></i></div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Avg Duration</p>
                            <h3 class="text-2xl font-bold text-purple-600" id="v-stat-avg">0m</h3>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg text-purple-600"><i
                                class="fas fa-hourglass-half text-xl"></i></div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 items-center bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
                <span class="text-sm font-semibold text-gray-600">Filter:</span>
                <select id="visit-filter-status" onchange="loadVisits()"
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                </select>
                <select id="visit-filter-date" onchange="loadVisits()"
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
                <button onclick="exportVisits()"
                    class="ml-auto bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-md text-sm font-medium shadow-sm flex items-center gap-2 transition-all">
                    <i class="fas fa-file-excel text-green-600"></i> Export
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b">Student</th>
                            <th class="p-4 font-semibold border-b">Date</th>
                            <th class="p-4 font-semibold border-b">Time In</th>
                            <th class="p-4 font-semibold border-b">Time Out</th>
                            <th class="p-4 font-semibold border-b">Duration</th>
                            <th class="p-4 font-semibold border-b">Officer</th>
                            <th class="p-4 font-semibold border-b">Status</th>
                            <th class="p-4 font-semibold border-b text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="visits-table" class="text-gray-700 text-sm"></tbody>
                </table>
            </div>
        </div>


        <div id="medical-tab-officers" class="medical-tab-content hidden p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Medical Officer Duty Log</h3>
                    <p class="text-sm text-gray-500">Track officer schedules</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openOfficerRegModal()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-md transition-all flex items-center gap-2"><i
                            class="fas fa-user-plus"></i> Register Officer</button>
                    <button onclick="openDutyModal()"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-md transition-all flex items-center gap-2"><i
                            class="fas fa-user-clock"></i> Log Duty</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-purple-50 border border-purple-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-purple-600 uppercase">Current Officer</p>
                    <h3 class="text-xl font-bold text-purple-700" id="cur-officer-name">-</h3>
                </div>
                <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-indigo-600 uppercase">Total Hours Today</p>
                    <h3 class="text-2xl font-bold text-indigo-700" id="off-hours-today">0h</h3>
                </div>
                <div class="bg-teal-50 border border-teal-100 p-4 rounded-lg">
                    <p class="text-xs font-medium text-teal-600 uppercase">Officers on Roster</p>
                    <h3 class="text-2xl font-bold text-teal-700" id="off-roster">0</h3>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Registered Medical Officers</h4>
                <div id="officers-list" class="flex flex-wrap gap-2"></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b">Officer Name</th>
                            <th class="p-4 font-semibold border-b">Role</th>
                            <th class="p-4 font-semibold border-b">Date</th>
                            <th class="p-4 font-semibold border-b">Time In</th>
                            <th class="p-4 font-semibold border-b">Time Out</th>
                            <th class="p-4 font-semibold border-b">Duration</th>
                            <th class="p-4 font-semibold border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody id="officer-logs-table" class="text-gray-700 text-sm"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div id="med-patient-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeMedPatientModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 max-h-[80vh] overflow-y-auto">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium text-gray-900">Register Medical Patient</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Registration Type</label>
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <button type="button" onclick="setMedRegType('existing')" id="med-reg-existing-btn"
                                        class="border-2 border-blue-500 bg-blue-50 text-blue-700 p-3 rounded-lg flex flex-col items-center justify-center transition-all">
                                        <div class="flex items-center gap-2 font-semibold text-sm mb-1"><i
                                                class="fas fa-search"></i> Existing Student</div>
                                        <div class="text-xs text-black/50 opacity-70">Select from registry</div>
                                    </button>
                                    <button type="button" onclick="setMedRegType('new')" id="med-reg-new-btn"
                                        class="border border-gray-200 hover:border-blue-300 text-gray-600 p-3 rounded-lg flex flex-col items-center justify-center transition-all">
                                        <div class="flex items-center gap-2 font-semibold text-sm mb-1"><i
                                                class="fas fa-user-plus"></i> New Patient</div>
                                        <div class="text-xs text-black/50 opacity-70">Create new record</div>
                                    </button>
                                </div>
                                <input type="hidden" id="med-reg-type" value="existing">
                            </div>

                            <div id="med-reg-existing-view">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Student <span
                                        class="text-red-500">*</span></label>
                                <select id="med-student-select"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white">
                                    <option value="">Search for a student...</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1 mb-2">Click to search and select from registered
                                    students</p>
                            </div>

                            <div id="med-reg-new-view" class="hidden">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" id="med-new-name"
                                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"
                                            placeholder="">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Student ID <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" id="med-new-id"
                                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"
                                            placeholder="STU-2026-XXX">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                        <select id="med-new-gender"
                                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                        <input type="number" id="med-new-age"
                                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"
                                            min="10" max="100">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <h4 class="text-xs font-bold text-blue-800 uppercase mb-3">Medical Conditions</h4>
                                <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 mb-3" id="condition-icons">
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Eyesight')">
                                        <div class="text-xl mb-1">👁️</div><span
                                            class="text-[10px] text-gray-600">Eye</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Bone/Muscle')">
                                        <div class="text-xl mb-1">🦴</div><span
                                            class="text-[10px] text-gray-600">Bone</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Ear')">
                                        <div class="text-xl mb-1">👂</div><span
                                            class="text-[10px] text-gray-600">Ear</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Speech/Hearing')">
                                        <div class="text-xl mb-1">👄</div><span
                                            class="text-[10px] text-gray-600">Speech</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Heart')">
                                        <div class="text-xl mb-1">❤️</div><span
                                            class="text-[10px] text-gray-600">Heart</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Organ')">
                                        <div class="text-xl mb-1">🫁</div><span
                                            class="text-[10px] text-gray-600">Organ</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                    <div class="medical-icon-btn flex flex-col items-center justify-center p-2 border rounded-lg cursor-pointer hover:bg-white bg-white"
                                        onclick="toggleCondition(this, 'Others')">
                                        <div class="text-xl mb-1">📝</div><span
                                            class="text-[10px] text-gray-600">Other</span>
                                        <div class="indicator-dot w-1.5 h-1.5 bg-blue-500 rounded-full mt-1"></div>
                                    </div>
                                </div>
                                <textarea id="med-notes" rows="3"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white resize-none"
                                    placeholder="Medical notes..."></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration
                                        Date</label>
                                    <input type="date" id="med-reg-date"
                                        class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                    <input type="time" id="med-reg-time"
                                        class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                <button onclick="saveMedPatient()"
                    class="w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Register
                    Patient</button>
                <button onclick="closeMedPatientModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div id="visit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeVisitModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-heartbeat text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium text-gray-900">Log Clinic Visit</h3>
                        <div class="mt-4 space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Patient <span
                                        class="text-red-500">*</span></label><select id="visit-patient"
                                    class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date <span
                                            class="text-red-500">*</span></label><input type="date" id="visit-date"
                                        class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Time In <span
                                            class="text-red-500">*</span></label><input type="time" id="visit-time-in"
                                        class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                </div>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label><input
                                    type="time" id="visit-time-out"
                                    class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                <p class="text-xs text-gray-500 mt-1">Leave blank if still in clinic</p>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Officer <span
                                        class="text-red-500">*</span></label><select id="visit-officer"
                                    class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></select>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Reason</label><textarea
                                    id="visit-notes" rows="2"
                                    class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white resize-none"
                                    placeholder="Reason for visit..."></textarea></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Treatment</label><input
                                    type="text" id="visit-treatment"
                                    class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"
                                    placeholder="Treatment given..."></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                <button onclick="saveVisit()"
                    class="w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 bg-red-500 text-white hover:bg-red-600 sm:ml-3 sm:w-auto sm:text-sm">Log
                    Visit</button>
                <button onclick="closeVisitModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div id="officer-reg-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeOfficerRegModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Register Medical Officer</h3>
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span
                                class="text-red-500">*</span></label><input type="text" id="off-name"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                class="text-red-500">*</span></label><select id="off-role"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                            <option value="School Nurse">School Nurse</option>
                            <option value="School Physician">School Physician</option>
                            <option value="Medical Assistant">Medical Assistant</option>
                            <option value="Dentist">Dentist</option>
                            <option value="ROTC">ROTC</option>
                            <option value="Other">Other</option>
                        </select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">COURSE ( if ROTC)</label><input
                            type="text" id="off-license"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact</label><input type="text"
                            id="off-contact"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                <button onclick="saveOfficerReg()"
                    class="w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-700 sm:ml-3 sm:w-auto sm:text-sm">Register</button>
                <button onclick="closeOfficerRegModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div id="duty-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeDutyModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Log Officer Duty</h3>
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Officer <span
                                class="text-red-500">*</span></label><select id="duty-officer"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date <span
                                    class="text-red-500">*</span></label><input type="date" id="duty-date"
                                class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Time In <span
                                    class="text-red-500">*</span></label><input type="time" id="duty-time-in"
                                class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label><input type="time"
                            id="duty-time-out"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea
                            id="duty-notes" rows="2"
                            class="block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white resize-none"></textarea>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                <button onclick="saveDuty()"
                    class="w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 bg-purple-600 text-white hover:bg-purple-700 sm:ml-3 sm:w-auto sm:text-sm">Log
                    Duty</button>
                <button onclick="closeDutyModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div id="med-details-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeMedDetailsModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 max-h-[80vh] overflow-y-auto">
                <div id="med-details-content"></div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                <button onclick="closeMedDetailsModal()"
                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 sm:w-auto sm:text-sm">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedConditions = [];
    let currentFilter = 'all';
    let allOfficers = [];

    document.addEventListener('DOMContentLoaded', function () {
        loadOfficers();
        loadPatients();
        loadVisits();
        loadOfficerLogs();
    });


    function switchMedicalTab(tab) {
        document.querySelectorAll('.medical-tab').forEach(t => { t.classList.remove('active'); t.classList.add('text-gray-600'); });
        document.querySelectorAll('.medical-tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('medical-tab-' + tab).classList.remove('hidden');
        if (tab === 'patients') loadPatients();
        if (tab === 'visits') loadVisits();
        if (tab === 'officers') loadOfficerLogs();
    }


    function loadOfficers() {
        fetch('api/medical.php?action=list_officers').then(r => r.json()).then(data => {
            allOfficers = data.data;
            const sel = document.getElementById('current-officer-select');
            sel.innerHTML = data.data.map(o => `<option value="${o.id}">${o.name}</option>`).join('');
            if (data.data.length > 0) document.getElementById('cur-officer-name').textContent = data.data[0].name;
        });
    }


    function loadPatients() {
        fetch(`api/medical.php?action=list_patients&filter=${currentFilter}`).then(r => r.json()).then(data => {
            const tbody = document.getElementById('patients-table');
            if (data.data.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-500">No medical patients found</td></tr>'; return; }
            tbody.innerHTML = data.data.map(p => {
                const conditions = (Array.isArray(p.conditions) ? p.conditions : []).map(c => {
                    const colors = { 'Heart': 'bg-red-100 text-red-800', 'Eyesight': 'bg-blue-100 text-blue-800', 'Bone/Muscle': 'bg-yellow-100 text-yellow-800', 'Organ': 'bg-purple-100 text-purple-800', 'Ear': 'bg-orange-100 text-orange-800', 'Speech/Hearing': 'bg-pink-100 text-pink-800' };
                    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${colors[c] || 'bg-gray-100 text-gray-800'} mr-1">${c}</span>`;
                }).join('');
                const sevClass = p.severity === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
                const clinic = p.in_clinic ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>In Clinic</span>' : '<span class="text-gray-400">-</span>';
                return `<tr class="hover:bg-gray-50 border-b border-gray-100">
                <td class="p-4 font-medium">${p.patient_id}</td>
                <td class="p-4"><div class="font-medium">${p.student_name || 'Unknown'}</div><div class="text-xs text-gray-500">${p.stu_id || ''}</div></td>
                <td class="p-4">${conditions}</td>
                <td class="p-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${sevClass}">${p.severity}</span></td>
                <td class="p-4 text-sm text-gray-500">${p.registered_date ? new Date(p.registered_date).toLocaleDateString() : '-'}</td>
                <td class="p-4 text-center">${clinic}</td>
                <td class="p-4 text-right">
                    <button onclick="viewPatientDetails(${p.id})" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></button>
                    <button onclick="deletePatient(${p.id})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td></tr>`;
            }).join('');
        });

        fetch('api/medical.php?action=patient_stats').then(r => r.json()).then(data => {
            document.getElementById('stat-patients').textContent = data.data.total;
            document.getElementById('stat-critical').textContent = data.data.critical;
            document.getElementById('stat-today-v').textContent = data.data.today_visits;
            document.getElementById('stat-active').textContent = data.data.active;
        });
    }

    function filterPatients(condition) {
        currentFilter = condition;
        document.querySelectorAll('.med-filter-btn').forEach(b => {
            if (b.dataset.c === condition) { b.classList.add('bg-emerald-600', 'text-white'); b.classList.remove('bg-white', 'text-gray-600', 'border-gray-300'); }
            else { b.classList.remove('bg-emerald-600', 'text-white'); b.classList.add('bg-white', 'text-gray-600', 'border-gray-300'); }
        });
        loadPatients();
    }

    function openMedicalStudentModal() {
        selectedConditions = [];
        document.querySelectorAll('.medical-icon-btn').forEach(b => b.classList.remove('selected'));
        document.getElementById('med-notes').value = '';
        fetch('api/medical.php?action=available_students').then(r => r.json()).then(data => {
            document.getElementById('med-student-select').innerHTML = '<option value="">Search for a student...</option>' + data.data.map(s => `<option value="${s.id}">${s.name} (${s.student_id} - ${s.strand})</option>`).join('');
        });
        const now = new Date();
        const d = document.getElementById('med-reg-date');
        const t = document.getElementById('med-reg-time');
        if (d) d.value = now.toISOString().split('T')[0];
        if (t) t.value = now.toTimeString().slice(0, 5);

        setMedRegType('existing');
        document.getElementById('med-new-name').value = '';
        document.getElementById('med-new-id').value = '';
        document.getElementById('med-new-age').value = '';
        document.getElementById('med-patient-modal').classList.remove('hidden');
    }
    function closeMedPatientModal() { document.getElementById('med-patient-modal').classList.add('hidden'); }

    function setMedRegType(type) {
        document.getElementById('med-reg-type').value = type;
        if (type === 'existing') {
            document.getElementById('med-reg-existing-btn').classList.add('border-2', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
            document.getElementById('med-reg-existing-btn').classList.remove('border', 'border-gray-200', 'text-gray-600');
            document.getElementById('med-reg-new-btn').classList.remove('border-2', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
            document.getElementById('med-reg-new-btn').classList.add('border', 'border-gray-200', 'text-gray-600');
            document.getElementById('med-reg-existing-view').classList.remove('hidden');
            document.getElementById('med-reg-new-view').classList.add('hidden');
        } else {
            document.getElementById('med-reg-new-btn').classList.add('border-2', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
            document.getElementById('med-reg-new-btn').classList.remove('border', 'border-gray-200', 'text-gray-600');
            document.getElementById('med-reg-existing-btn').classList.remove('border-2', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
            document.getElementById('med-reg-existing-btn').classList.add('border', 'border-gray-200', 'text-gray-600');
            document.getElementById('med-reg-new-view').classList.remove('hidden');
            document.getElementById('med-reg-existing-view').classList.add('hidden');
        }
    }

    function toggleCondition(btn, c) {
        btn.classList.toggle('selected');
        if (selectedConditions.includes(c)) selectedConditions = selectedConditions.filter(x => x !== c);
        else selectedConditions.push(c);
    }

    function saveMedPatient() {
        const regType = document.getElementById('med-reg-type') ? document.getElementById('med-reg-type').value : 'existing';
        const fd = new FormData();
        fd.append('action', 'register_patient');

        if (regType === 'existing') {
            const studentId = document.getElementById('med-student-select').value;
            if (!studentId) { showToast('Select a student', 'error'); return; }
            fd.append('student_id', studentId);
        } else {
            const name = document.getElementById('med-new-name').value.trim();
            const stdId = document.getElementById('med-new-id').value.trim();
            if (!name || !stdId) { showToast('Complete required fields for new patient', 'error'); return; }
            fd.append('new_name', name);
            fd.append('new_id', stdId);
            fd.append('new_gender', document.getElementById('med-new-gender').value);
            fd.append('new_age', document.getElementById('med-new-age').value);
        }

        if (selectedConditions.length === 0) { showToast('Select at least one condition', 'error'); return; }

        fd.append('reg_type', regType);
        fd.append('conditions', JSON.stringify(selectedConditions));
        fd.append('severity', (selectedConditions.includes('Heart') || selectedConditions.includes('Organ')) ? 'Critical' : 'Standard');
        fd.append('notes', document.getElementById('med-notes').value);

        const d = document.getElementById('med-reg-date') ? document.getElementById('med-reg-date').value : '';
        const t = document.getElementById('med-reg-time') ? document.getElementById('med-reg-time').value : '';
        const regDateTime = (d && t) ? (d + ' ' + t + ':00') : new Date().toISOString().slice(0, 19).replace('T', ' ');
        fd.append('registered_date', regDateTime);
        fd.append('registered_by', document.getElementById('current-officer-select').value);
        fetch('api/medical.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) { closeMedPatientModal(); loadPatients(); }
        });
    }

    function deletePatient(id) {
        if (!confirm('Remove this medical record?')) return;
        const fd = new FormData(); fd.append('action', 'delete_patient'); fd.append('id', id);
        fetch('api/medical.php', { method: 'POST', body: fd }).then(r => r.json()).then(() => { showToast('Removed'); loadPatients(); });
    }

    function viewPatientDetails(id) {
        fetch(`api/medical.php?action=get_patient&id=${id}`).then(r => r.json()).then(data => {
            const p = data.data;
            if (!p) return;
            const conditions = (Array.isArray(p.conditions) ? p.conditions : []).map(c => `<span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-sm">${c}</span>`).join(' ');
            const visits = (p.visits || []).map(v => `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border"><div><p class="text-sm font-medium">${new Date(v.visit_date).toLocaleDateString()} at ${v.time_in}</p><p class="text-xs text-gray-500">${v.notes || 'No notes'}</p></div>${v.time_out ? `<span class="text-xs text-gray-500">${v.duration}m</span>` : '<span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs">Active</span>'}</div>`).join('');
            document.getElementById('med-details-content').innerHTML = `
            <div class="bg-gray-50 p-4 rounded-lg border">
                <div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl font-bold">${(p.student_name || 'U').split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()}</div><div><h4 class="text-lg font-bold">${p.student_name || 'Unknown'}</h4><p class="text-sm text-gray-500">${p.stu_id || ''}</p></div></div>
                <div class="grid grid-cols-2 gap-4 mb-4"><div><p class="text-xs text-gray-500 uppercase">Gender</p><p class="font-medium">${p.gender || 'N/A'}</p></div><div><p class="text-xs text-gray-500 uppercase">Age</p><p class="font-medium">${p.age || 'N/A'}</p></div></div>
                <div class="mb-4"><p class="text-xs text-gray-500 uppercase mb-2">Conditions</p><div class="flex flex-wrap gap-2">${conditions}</div></div>
                <div class="mb-4"><p class="text-xs text-gray-500 uppercase mb-1">Severity</p><span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${p.severity === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'}">${p.severity}</span></div>
                <div class="mb-4"><p class="text-xs text-gray-500 uppercase mb-1">Notes</p><p class="text-sm bg-white p-3 rounded border">${p.notes || 'None'}</p></div>
            </div>
            <div class="mt-4"><h4 class="text-sm font-bold mb-3">Recent Visits</h4>${visits || '<p class="text-sm text-gray-500 italic">No visits</p>'}</div>`;
            document.getElementById('med-details-modal').classList.remove('hidden');
        });
    }
    function closeMedDetailsModal() { document.getElementById('med-details-modal').classList.add('hidden'); }


    function loadVisits() {
        const status = document.getElementById('visit-filter-status').value;
        const date = document.getElementById('visit-filter-date').value;
        fetch(`api/medical.php?action=list_visits&status=${status}&date_filter=${date}`).then(r => r.json()).then(data => {
            document.getElementById('v-stat-today').textContent = data.stats.today;
            document.getElementById('v-stat-active').textContent = data.stats.active;
            document.getElementById('v-stat-week').textContent = data.stats.week;
            document.getElementById('v-stat-avg').textContent = data.stats.avg_duration + 'm';
            const tbody = document.getElementById('visits-table');
            if (data.data.length === 0) { tbody.innerHTML = '<tr><td colspan="8" class="p-8 text-center text-gray-500">No visits found</td></tr>'; return; }
            tbody.innerHTML = data.data.map(v => {
                const duration = v.duration ? v.duration + 'm' : (v.time_out ? 'N/A' : '-');
                const sc = !v.time_out ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                const st = !v.time_out ? 'Active' : 'Completed';
                return `<tr class="hover:bg-gray-50 border-b border-gray-100">
                <td class="p-4"><div class="font-medium">${v.student_name || 'Unknown'}</div><div class="text-xs text-gray-500">${v.stu_id || ''}</div></td>
                <td class="p-4 text-sm">${new Date(v.visit_date).toLocaleDateString()}</td>
                <td class="p-4 text-sm font-mono">${v.time_in}</td>
                <td class="p-4 text-sm font-mono">${v.time_out || '-'}</td>
                <td class="p-4 text-sm">${duration}</td>
                <td class="p-4 text-sm">${v.officer_name || '-'}</td>
                <td class="p-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${sc}">${st}</span></td>
                <td class="p-4 text-right">
                    ${!v.time_out ? `<button onclick="checkoutVisit(${v.id})" class="text-emerald-600 hover:text-emerald-900 mr-3" title="Check Out"><i class="fas fa-sign-out-alt"></i></button>` : ''}
                    <button onclick="deleteVisit(${v.id})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td></tr>`;
            }).join('');
        });
    }

    function exportVisits() {
        const status = document.getElementById('visit-filter-status').value;
        const date = document.getElementById('visit-filter-date').value;
        window.location.href = `api/medical.php?action=export_visits&status=${status}&date_filter=${date}`;
    }

    function openVisitModal() {
        const now = new Date();
        document.getElementById('visit-date').value = now.toISOString().split('T')[0];
        document.getElementById('visit-time-in').value = now.toTimeString().slice(0, 5);
        document.getElementById('visit-time-out').value = '';
        document.getElementById('visit-notes').value = '';
        document.getElementById('visit-treatment').value = '';
        fetch('api/medical.php?action=list_patients&filter=all').then(r => r.json()).then(data => {
            document.getElementById('visit-patient').innerHTML = '<option value="">Select Patient</option>' + data.data.map(p => `<option value="${p.id}">${p.student_name} (${(Array.isArray(p.conditions) ? p.conditions : []).join(', ')})</option>`).join('');
        });
        document.getElementById('visit-officer').innerHTML = '<option value="">Select Officer</option>' + allOfficers.map(o => `<option value="${o.id}">${o.name} (${o.role})</option>`).join('');
        document.getElementById('visit-modal').classList.remove('hidden');
    }
    function closeVisitModal() { document.getElementById('visit-modal').classList.add('hidden'); }

    function saveVisit() {
        const fd = new FormData();
        fd.append('action', 'add_visit');
        fd.append('patient_id', document.getElementById('visit-patient').value);
        fd.append('visit_date', document.getElementById('visit-date').value);
        fd.append('time_in', document.getElementById('visit-time-in').value);
        fd.append('time_out', document.getElementById('visit-time-out').value);
        fd.append('officer_id', document.getElementById('visit-officer').value);
        fd.append('notes', document.getElementById('visit-notes').value);
        fd.append('treatment', document.getElementById('visit-treatment').value);
        if (!document.getElementById('visit-patient').value || !document.getElementById('visit-date').value || !document.getElementById('visit-officer').value) { showToast('Fill required fields', 'error'); return; }
        fetch('api/medical.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => { showToast(data.message, data.success ? 'success' : 'error'); if (data.success) { closeVisitModal(); loadVisits(); } });
    }

    function checkoutVisit(id) {
        const fd = new FormData(); fd.append('action', 'checkout_visit'); fd.append('id', id);
        fetch('api/medical.php', { method: 'POST', body: fd }).then(r => r.json()).then(() => { showToast('Checked out'); loadVisits(); });
    }

    function deleteVisit(id) {
        if (!confirm('Delete this visit?')) return;
        const fd = new FormData(); fd.append('action', 'delete_visit'); fd.append('id', id);
        fetch('api/medical.php', { method: 'POST', body: fd }).then(r => r.json()).then(() => { showToast('Deleted'); loadVisits(); });
    }


    function loadOfficerLogs() {
        fetch('api/medical.php?action=list_officer_logs').then(r => r.json()).then(data => {
            document.getElementById('off-roster').textContent = data.stats.roster;
            document.getElementById('off-hours-today').textContent = data.stats.hours_today + 'h';
            const tbody = document.getElementById('officer-logs-table');
            if (data.data.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-500">No duty logs</td></tr>'; return; }
            tbody.innerHTML = data.data.map(l => {
                const sc = !l.time_out ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                return `<tr class="hover:bg-gray-50 border-b border-gray-100">
                <td class="p-4 font-medium">${l.officer_name || 'Unknown'}</td>
                <td class="p-4 text-sm">${l.officer_role || '-'}</td>
                <td class="p-4 text-sm">${new Date(l.log_date).toLocaleDateString()}</td>
                <td class="p-4 text-sm font-mono">${l.time_in}</td>
                <td class="p-4 text-sm font-mono">${l.time_out || '-'}</td>
                <td class="p-4 text-sm">${l.duration ? l.duration + 'm' : '-'}</td>
                <td class="p-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${sc}">${l.time_out ? 'Completed' : 'On Duty'}</span></td>
            </tr>`;
            }).join('');
        });

        fetch('api/medical.php?action=list_officers').then(r => r.json()).then(data => {
            document.getElementById('officers-list').innerHTML = data.data.map(o => `
            <div class="officer-card bg-white border border-gray-200 rounded-lg p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600"><i class="fas fa-user-md"></i></div>
                <div><p class="font-medium text-sm">${o.name}</p><p class="text-xs text-gray-500">${o.role}</p></div>
                <button onclick="deleteOfficer(${o.id})" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
            </div>`).join('');
        });
    }

    function openOfficerRegModal() { document.getElementById('off-name').value = ''; document.getElementById('off-license').value = ''; document.getElementById('off-contact').value = ''; document.getElementById('officer-reg-modal').classList.remove('hidden'); }
    function closeOfficerRegModal() { document.getElementById('officer-reg-modal').classList.add('hidden'); }

    function saveOfficerReg() {
        const name = document.getElementById('off-name').value.trim();
        if (!name) { showToast('Enter name', 'error'); return; }
        const fd = new FormData();
        fd.append('action', 'add_officer'); fd.append('name', name); fd.append('role', document.getElementById('off-role').value);
        fd.append('license', document.getElementById('off-license').value); fd.append('contact', document.getElementById('off-contact').value);
        fetch('api/medical.php', { method: 'POST', body: fd })
            .then(r => r.json().catch(err => { console.error('JSON Parse Error', err); return null; }))
            .then(data => { 
                if (!data) { showToast('Server error', 'error'); return; }
                if (data.success) {
                    showToast('Officer registered'); 
                    closeOfficerRegModal(); 
                    loadOfficers(); 
                    loadOfficerLogs(); 
                } else {
                    showToast(data.message || 'Failed to register', 'error');
                }
            });
    }

    function deleteOfficer(id) {
        if (!confirm('Remove this officer?')) return;
        const fd = new FormData(); fd.append('action', 'delete_officer'); fd.append('id', id);
        fetch('api/medical.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => { showToast(data.message || 'Done', data.success ? 'success' : 'error'); loadOfficers(); loadOfficerLogs(); });
    }

    function openDutyModal() {
        const now = new Date();
        document.getElementById('duty-date').value = now.toISOString().split('T')[0];
        document.getElementById('duty-time-in').value = now.toTimeString().slice(0, 5);
        document.getElementById('duty-time-out').value = '';
        document.getElementById('duty-notes').value = '';
        document.getElementById('duty-officer').innerHTML = '<option value="">Select Officer</option>' + allOfficers.map(o => `<option value="${o.id}">${o.name}</option>`).join('');
        document.getElementById('duty-modal').classList.remove('hidden');
    }
    function closeDutyModal() { document.getElementById('duty-modal').classList.add('hidden'); }

    function saveDuty() {
        const officer = document.getElementById('duty-officer').value;
        const date = document.getElementById('duty-date').value;
        const timeIn = document.getElementById('duty-time-in').value;
        if (!officer) { showToast('Select an officer', 'error'); return; }
        if (!date || !timeIn) { showToast('Date and Time In are required', 'error'); return; }

        const fd = new FormData();
        fd.append('action', 'add_officer_log');
        fd.append('officer_id', officer);
        fd.append('log_date', date);
        fd.append('time_in', timeIn);
        fd.append('time_out', document.getElementById('duty-time-out').value);
        fd.append('notes', document.getElementById('duty-notes').value);

        fetch('api/medical.php', { 
            method: 'POST', 
            body: fd,
            headers: { 'Accept': 'application/json' }
        })
            .then(r => {
                if (!r.ok) throw new Error('Server error: ' + r.status);
                return r.json();
            })
            .then(data => {
                showToast(data.message || 'Done', data.success ? 'success' : 'error');
                if (data.success) {
                    closeDutyModal();
                    loadOfficerLogs();
                }
            })
            .catch(err => {
                console.error('Duty log error:', err);
                showToast('Failed to log duty. Check console for details.', 'error');
            });
    }
</script>

<?php include 'includes/footer.php'; ?>
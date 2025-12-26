<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Group Chat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #e8f4f8;
            height: 100vh;
            overflow: hidden;
        }
        
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: #e8f4f8;
        }
        
        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: white;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .chat-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: #2c3e50;
        }
        
        .btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #2c3e50;
            border: 2px solid #e0e0e0;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #2563eb;
            color: #2563eb;
        }
        
        .chat-header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        #group-members {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        #group-members:before {
            content: "👥";
            font-size: 0.875rem;
        }
        
        .chat-body {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        .chat-sidebar {
            width: 320px;
            background: white;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab-button {
            flex: 1;
            padding: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }
        
        .tab-button.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }
        
        .group-list {
            flex: 1;
            overflow-y: auto;
        }
        
        .group-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .group-item:hover {
            background: #f8f9fa;
        }
        
        .group-item.active {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(30, 64, 175, 0.1) 100%);
            border-left: 3px solid #2563eb;
        }
        
        .group-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .group-info {
            flex: 1;
            min-width: 0;
        }
        
        .group-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .group-desc {
            font-size: 0.875rem;
            color: #95a5a6;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }
        
        .no-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
        }
        
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f8f9fa;
        }
        
        .message {
            display: flex;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message-own {
            flex-direction: row-reverse;
        }
        
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
            font-size: 0.875rem;
        }
        
        .message-content {
            max-width: 60%;
            margin: 0 1rem;
        }
        
        .message-user {
            font-size: 0.75rem;
            color: #95a5a6;
            margin-bottom: 0.25rem;
        }
        
        .message-bubble {
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .message-own .message-bubble {
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            color: white;
        }
        
        .message-time {
            font-size: 0.7rem;
            color: #95a5a6;
            margin-top: 0.25rem;
        }
        
        .message-input-container {
            border-top: 1px solid #e0e0e0;
            background: white;
            padding: 1rem 1.5rem;
        }
        
        .message-input {
            display: flex;
            gap: 0.75rem;
        }
        
        .message-input input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 24px;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }
        
        .message-input input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .message-input button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .message-input button:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .close-modal {
            width: 32px;
            height: 32px;
            border: none;
            background: #f0f0f0;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
        }
        
        .user-list {
            max-height: 300px;
            overflow-y: auto;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem;
        }
        
        .user-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .user-item:hover {
            background: #f8f9fa;
        }
        
        .user-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .btn-join {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #95a5a6;
        }
        
        /* Refresh indicator animation */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
        }
        
        #refresh-dot {
            animation: pulse 2s ease-in-out infinite;
        }
        
        #refresh-dot.paused {
            animation: none;
            background: #ef4444 !important;
        }
        
        /* Skeleton Loading */
        .skeleton-loading {
            padding: 0.5rem;
        }
        
        .skeleton-group-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .skeleton-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
        }
        
        .skeleton-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .skeleton-line {
            height: 12px;
            border-radius: 6px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
        }
        
        .skeleton-title {
            width: 60%;
            height: 16px;
        }
        
        .skeleton-desc {
            width: 80%;
        }
        
        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
        
        /* Typing Indicator */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 12px;
            margin: 0.5rem 0;
            font-size: 0.875rem;
            color: #1976d2;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.15);
            animation: fadeIn 0.3s ease;
        }
        
        .typing-dots {
            display: flex;
            gap: 0.25rem;
            margin-left: 0.25rem;
        }
        
        .typing-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #1976d2;
            animation: typing-bounce 1.4s infinite;
        }
        
        .typing-dot:nth-child(1) {
            animation-delay: 0s;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing-bounce {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.5;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-left">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboardAdmin') : route('dashboard') }}" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Back
                </a>
                <div>
                    <h3 id="current-group-name">Group Chat</h3>
                    <div id="group-members" style="font-size: 0.875rem; color: #95a5a6; margin-top: 0.25rem; display: none;"></div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="showCreateGroupModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/>
                </svg>
                New Group
            </button>
        </div>

        <div class="chat-body">
            <!-- Sidebar -->
            <div class="chat-sidebar">
                <div class="sidebar-tabs">
                    <button class="tab-button active" onclick="switchTab('my-groups')">My Groups</button>
                    <button class="tab-button" onclick="switchTab('available')">Available</button>
                </div>
                
                <div class="group-list" id="my-groups-list">
                    @if(isset($initialGroups) && count($initialGroups) > 0)
                        <!-- Server-side rendered groups -->
                        @foreach($initialGroups as $group)
                        <div class="group-item" onclick="selectGroup({{ $group['id'] }})">
                            <div class="group-avatar">{{ strtoupper(substr($group['name'], 0, 1)) }}</div>
                            <div class="group-info">
                                <div class="group-name">{{ $group['name'] }}</div>
                                <div class="group-desc">{{ $group['member_count'] }} members</div>
                            </div>
                        </div>
                        @endforeach
                    @elseif(isset($initialGroups) && count($initialGroups) === 0)
                        <div class="loading">No groups yet. Create one or join available groups!</div>
                    @else
                        <!-- Skeleton Loading (fallback) -->
                        <div class="skeleton-loading">
                            <div class="skeleton-group-item">
                                <div class="skeleton-avatar"></div>
                                <div class="skeleton-info">
                                    <div class="skeleton-line skeleton-title"></div>
                                    <div class="skeleton-line skeleton-desc"></div>
                                </div>
                            </div>
                            <div class="skeleton-group-item">
                                <div class="skeleton-avatar"></div>
                                <div class="skeleton-info">
                                    <div class="skeleton-line skeleton-title"></div>
                                    <div class="skeleton-line skeleton-desc"></div>
                                </div>
                            </div>
                            <div class="skeleton-group-item">
                                <div class="skeleton-avatar"></div>
                                <div class="skeleton-info">
                                    <div class="skeleton-line skeleton-title"></div>
                                    <div class="skeleton-line skeleton-desc"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="group-list" id="available-groups-list" style="display: none;">
                    <div class="loading">Loading groups...</div>
                </div>
            </div>

            <!-- Main Chat -->
            <div class="chat-main">
                <div class="no-group" id="no-group">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p>Select a group to start chatting</p>
                </div>

                <div id="chat-content" style="display: none; flex-direction: column; flex: 1;">
                    <div class="messages-container" id="messages-container"></div>
                    
                    <!-- Typing Indicator -->
                    <div id="typing-indicator-container" style="padding: 0 1.5rem; display: none;">
                        <div class="typing-indicator">
                            <span id="typing-user-name">Someone</span> is typing
                            <div class="typing-dots">
                                <div class="typing-dot"></div>
                                <div class="typing-dot"></div>
                                <div class="typing-dot"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="message-input-container">
                        <div class="message-input">
                            <input 
                                type="text" 
                                id="message-input"
                                placeholder="Type a message..."
                                onkeypress="if(event.key==='Enter') sendMessage()"
                            />
                            <button onclick="sendMessage()">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div class="modal" id="create-group-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New Group</h2>
                <button class="close-modal" onclick="hideCreateGroupModal()">✕</button>
            </div>
            
            <form onsubmit="createGroup(event)">
                <div class="form-group">
                    <label class="form-label">Group Name *</label>
                    <input type="text" class="form-input" id="group-name" required placeholder="Enter group name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-input" id="group-description" placeholder="Optional description">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Select Members *</label>
                    <div class="user-list" id="users-list">
                        <div class="loading">Loading users...</div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Group</button>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = '/chat';
        const currentUser = {{ auth()->id() }};
        let currentGroupId = null;
        let allUsers = [];
        let messagePollingInterval = null;
        let groupPollingInterval = null;
        let typingPollingInterval = null;
        let typingTimeout = null;
        let lastMessageId = null;

        // Setup CSRF token
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        // Check if server-rendered groups exist
        const hasServerRenderedGroups = @json(isset($initialGroups) && count($initialGroups) > 0);

        // Load initial data
        document.addEventListener('DOMContentLoaded', () => {
            // Only load via AJAX if no server-rendered groups
            if (!hasServerRenderedGroups) {
                loadMyGroups(true);
            }
            
            loadUsers();
            startGroupPolling();
        });

        // Tab switching
        function switchTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('my-groups-list').style.display = tab === 'my-groups' ? 'block' : 'none';
            document.getElementById('available-groups-list').style.display = tab === 'available' ? 'block' : 'none';
            
            if (tab === 'available') {
                loadAvailableGroups();
            }
        }

        // Load my groups
        async function loadMyGroups(showLoading = false) {
            try {
                const container = document.getElementById('my-groups-list');
                
                // Show loading only on first load, not during polling
                if (showLoading || container.children.length === 0) {
                    container.innerHTML = '<div class="loading">Loading groups...</div>';
                }
                
                const response = await axios.get(`${API_BASE}/groups`);
                
                // Check if response has the expected structure
                if (!response.data || !response.data.groups) {
                    throw new Error('Invalid response format from server');
                }
                
                const groups = response.data.groups;
                
                if (groups.length === 0) {
                    container.innerHTML = '<div class="loading">No groups yet. Create one or join available groups!</div>';
                    return;
                }
                
                container.innerHTML = groups.map(group => `
                    <div class="group-item ${currentGroupId === group.id ? 'active' : ''}" onclick="selectGroup(${group.id})">
                        <div class="group-avatar">${group.name.charAt(0).toUpperCase()}</div>
                        <div class="group-info">
                            <div class="group-name">${group.name}</div>
                            <div class="group-desc">${group.member_count} members</div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading groups:', error);
                console.error('Error details:', error.response);
                
                const container = document.getElementById('my-groups-list');
                const errorMsg = error.response?.data?.message || error.message || 'Unknown error';
                container.innerHTML = `
                    <div class="loading" style="color: #ef4444;">
                        <p>Error loading groups</p>
                        <p style="font-size: 0.75rem; margin: 0.5rem 0;">${errorMsg}</p>
                        <button onclick="loadMyGroups(true)" class="btn btn-secondary" style="margin-top: 0.5rem;">Retry</button>
                    </div>
                `;
            }
        }

        // Load available groups
        async function loadAvailableGroups() {
            try {
                const container = document.getElementById('available-groups-list');
                container.innerHTML = '<div class="loading">Loading available groups...</div>';
                
                const response = await axios.get(`${API_BASE}/groups/available`);
                
                if (!response.data || !response.data.groups) {
                    throw new Error('Invalid response format from server');
                }
                
                const groups = response.data.groups;
                
                if (groups.length === 0) {
                    container.innerHTML = '<div class="loading">No available groups to join</div>';
                    return;
                }
                
                container.innerHTML = groups.map(group => `
                    <div class="group-item">
                        <div class="group-avatar">${group.name.charAt(0).toUpperCase()}</div>
                        <div class="group-info">
                            <div class="group-name">${group.name}</div>
                            <div class="group-desc">${group.description || 'No description'} • ${group.member_count} members</div>
                        </div>
                        <button class="btn btn-primary btn-join" onclick="joinGroup(${group.id}, event)">Join</button>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading available groups:', error);
                console.error('Error details:', error.response);
                
                const container = document.getElementById('available-groups-list');
                const errorMsg = error.response?.data?.message || error.message || 'Unknown error';
                container.innerHTML = `
                    <div class="loading" style="color: #ef4444;">
                        <p>Error loading groups</p>
                        <p style="font-size: 0.75rem; margin: 0.5rem 0;">${errorMsg}</p>
                        <button onclick="loadAvailableGroups()" class="btn btn-secondary" style="margin-top: 0.5rem;">Retry</button>
                    </div>
                `;
            }
        }

        // Load all users
        async function loadUsers() {
            try {
                const response = await axios.get(`${API_BASE}/users`);
                allUsers = response.data.users;
                console.log('Users loaded:', allUsers.length);
            } catch (error) {
                console.error('Error loading users:', error);
                alert('Error loading users. Please refresh the page.');
            }
        }

        // Show create group modal
        async function showCreateGroupModal() {
            document.getElementById('create-group-modal').classList.add('active');
            
            const usersList = document.getElementById('users-list');
            
            // If users not loaded yet, load them now
            if (allUsers.length === 0) {
                usersList.innerHTML = '<div class="loading">Loading users...</div>';
                await loadUsers();
            }
            
            if (allUsers.length === 0) {
                usersList.innerHTML = '<div class="loading">No other users available</div>';
                return;
            }
            
            usersList.innerHTML = allUsers.map(user => `
                <label class="user-item">
                    <input type="checkbox" value="${user.id}" name="members">
                    <span>${user.name} (${user.email})</span>
                </label>
            `).join('');
        }

        function hideCreateGroupModal() {
            document.getElementById('create-group-modal').classList.remove('active');
            document.getElementById('group-name').value = '';
            document.getElementById('group-description').value = '';
        }

        // Create group
        async function createGroup(event) {
            event.preventDefault();
            
            const name = document.getElementById('group-name').value;
            const description = document.getElementById('group-description').value;
            const checkboxes = document.querySelectorAll('input[name="members"]:checked');
            const memberIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            if (memberIds.length === 0) {
                alert('Please select at least one member');
                return;
            }
            
            try {
                const response = await axios.post(`${API_BASE}/groups`, {
                    name,
                    description,
                    member_ids: memberIds
                });
                
                hideCreateGroupModal();
                
                // Refresh my groups list
                await loadMyGroups(true);
                
                // Switch to My Groups tab
                document.querySelectorAll('.tab-button')[0].click();
                
                alert('Group created successfully! Members have been notified.');
            } catch (error) {
                console.error('Error creating group:', error);
                alert('Error creating group: ' + (error.response?.data?.message || 'Please try again.'));
            }
        }

        // Join group
        async function joinGroup(groupId, event) {
            event.stopPropagation();
            
            if (!confirm('Are you sure you want to join this group?')) {
                return;
            }
            
            try {
                await axios.post(`${API_BASE}/groups/${groupId}/join`);
                
                // Reload both lists
                await loadMyGroups(true);
                await loadAvailableGroups();
                
                // Switch to My Groups tab
                document.querySelectorAll('.tab-button')[0].click();
                
                alert('Successfully joined the group!');
            } catch (error) {
                console.error('Error joining group:', error);
                alert('Error joining group: ' + (error.response?.data?.message || 'Please try again.'));
            }
        }

        // Select group
        async function selectGroup(groupId) {
            currentGroupId = groupId;
            
            // Update active state
            document.querySelectorAll('.group-item').forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');
            
            // Show chat area
            document.getElementById('no-group').style.display = 'none';
            document.getElementById('chat-content').style.display = 'flex';
            
            // Load group details (including members)
            await loadGroupDetails(groupId);
            
            // Load messages
            await loadMessages(groupId);
            
            // Start polling for new messages and typing status
            startMessagePolling();
            startTypingPolling();
            
            // Setup typing event listeners (after chat area is visible)
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                // Remove old listeners first (if any)
                messageInput.removeEventListener('input', handleTyping);
                messageInput.removeEventListener('blur', stopTyping);
                
                // Add new listeners
                messageInput.addEventListener('input', handleTyping);
                messageInput.addEventListener('blur', stopTyping);
            }
        }
        
        // Load group details
        async function loadGroupDetails(groupId) {
            try {
                const response = await axios.get(`${API_BASE}/groups/${groupId}`);
                const group = response.data.group;
                
                // Update group name in header
                document.getElementById('current-group-name').textContent = group.name;
                
                // Get members except current user
                const otherMembers = group.members.filter(m => m.id !== currentUser);
                const membersDiv = document.getElementById('group-members');
                
                if (otherMembers.length > 0) {
                    const memberNames = otherMembers.map(m => m.name).join(', ');
                    membersDiv.textContent = memberNames;
                    membersDiv.style.display = 'flex'; // Show member list
                } else {
                    membersDiv.textContent = 'Only you in this group';
                    membersDiv.style.display = 'flex'; // Show message even if alone
                }
            } catch (error) {
                console.error('Error loading group details:', error);
                document.getElementById('current-group-name').textContent = 'Group Chat';
                const membersDiv = document.getElementById('group-members');
                membersDiv.textContent = '';
                membersDiv.style.display = 'none'; // Hide on error
            }
        }

        // Start polling for groups
        function startGroupPolling() {
            // Poll every 10 seconds for group updates
            if (groupPollingInterval) {
                clearInterval(groupPollingInterval);
            }
            
            groupPollingInterval = setInterval(() => {
                const activeTab = document.querySelector('.tab-button.active');
                if (activeTab && activeTab.textContent === 'My Groups') {
                    loadMyGroups();
                } else if (activeTab && activeTab.textContent === 'Available') {
                    loadAvailableGroups();
                }
            }, 10000); // 10 seconds (reduced from 5s for performance)
        }

        // Start polling for messages
        function startMessagePolling() {
            // Clear existing interval if any
            if (messagePollingInterval) {
                clearInterval(messagePollingInterval);
            }
            
            // Poll every 3 seconds for new messages
            messagePollingInterval = setInterval(async () => {
                if (currentGroupId) {
                    await loadNewMessages(currentGroupId);
                }
            }, 3000); // 3 seconds (reduced from 2s for performance)
        }

        // Stop message polling
        function stopMessagePolling() {
            if (messagePollingInterval) {
                clearInterval(messagePollingInterval);
                messagePollingInterval = null;
            }
        }

        // Load new messages only (polling)
        async function loadNewMessages(groupId) {
            if (!currentGroupId || currentGroupId !== groupId) return;
            
            try {
                const response = await axios.get(`${API_BASE}/groups/${groupId}/messages`);
                const messages = response.data.messages;
                
                if (messages.length === 0) return;
                
                // Get the ID of the last message currently in the UI
                const container = document.getElementById('messages-container');
                const currentMessages = container.querySelectorAll('.message');
                const currentLastMessageId = currentMessages.length > 0 
                    ? parseInt(currentMessages[currentMessages.length - 1].getAttribute('data-message-id') || '0')
                    : 0;
                
                // Add only new messages
                messages.forEach(msg => {
                    if (msg.id > currentLastMessageId) {
                        addMessageToUI(msg);
                    }
                });
                
                scrollToBottom();
            } catch (error) {
                console.error('Error loading new messages:', error);
            }
        }

        // Load messages (initial load)
        async function loadMessages(groupId) {
            try {
                const response = await axios.get(`${API_BASE}/groups/${groupId}/messages`);
                const messages = response.data.messages;
                
                const container = document.getElementById('messages-container');
                container.innerHTML = messages.map(msg => `
                    <div class="message ${msg.is_own ? 'message-own' : ''}" data-message-id="${msg.id}">
                        <div class="message-avatar">${msg.user_name.charAt(0).toUpperCase()}</div>
                        <div class="message-content">
                            ${!msg.is_own ? `<div class="message-user">${msg.user_name}</div>` : ''}
                            <div class="message-bubble">
                                <p>${msg.message}</p>
                            </div>
                            <div class="message-time">${new Date(msg.created_at).toLocaleTimeString()}</div>
                        </div>
                    </div>
                `).join('');
                
                scrollToBottom();
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        // Send message
        async function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            
            if (!message || !currentGroupId) return;
            
            try {
                const response = await axios.post(`${API_BASE}/groups/${currentGroupId}/messages`, {
                    message
                });
                
                // Clear input
                input.value = '';
                
                // Message will be shown via polling
                // But add immediately for better UX
                const msg = response.data.message;
                if (msg && msg.id) {
                    // Check if message already exists
                    const container = document.getElementById('messages-container');
                    const existingMsg = container.querySelector(`[data-message-id="${msg.id}"]`);
                    if (!existingMsg) {
                        addMessageToUI(msg);
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error sending message. Please try again.');
            }
        }

        // Add message to UI
        function addMessageToUI(msg) {
            const container = document.getElementById('messages-container');
            
            // Check if message already exists
            const existingMsg = container.querySelector(`[data-message-id="${msg.id}"]`);
            if (existingMsg) {
                return; // Don't add duplicate
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${msg.is_own ? 'message-own' : ''}`;
            messageDiv.setAttribute('data-message-id', msg.id);
            messageDiv.innerHTML = `
                <div class="message-avatar">${msg.user_name.charAt(0).toUpperCase()}</div>
                <div class="message-content">
                    ${!msg.is_own ? `<div class="message-user">${msg.user_name}</div>` : ''}
                    <div class="message-bubble">
                        <p>${msg.message}</p>
                    </div>
                    <div class="message-time">${new Date(msg.created_at).toLocaleTimeString()}</div>
                </div>
            `;
            container.appendChild(messageDiv);
        }

        // Scroll to bottom
        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
        
        // Toggle auto-refresh
        let autoRefreshEnabled = true;
        function toggleAutoRefresh() {
            autoRefreshEnabled = !autoRefreshEnabled;
            
            const dot = document.getElementById('refresh-dot');
            const text = document.getElementById('refresh-text');
            const btnText = document.getElementById('refresh-btn-text');
            
            if (autoRefreshEnabled) {
                // Enable polling
                startGroupPolling();
                if (currentGroupId) {
                    startMessagePolling();
                }
                
                // Update UI
                dot.classList.remove('paused');
                text.textContent = 'Auto-refresh ON';
                text.style.color = '#10b981';
                btnText.textContent = 'Pause';
            } else {
                // Stop polling
                if (groupPollingInterval) {
                    clearInterval(groupPollingInterval);
                    groupPollingInterval = null;
                }
                stopMessagePolling();
                
                // Update UI
                dot.classList.add('paused');
                text.textContent = 'Auto-refresh OFF';
                text.style.color = '#ef4444';
                btnText.textContent = 'Resume';
            }
        }
        
        // Typing indicator functions
        function handleTyping() {
            if (!currentGroupId) return;
            
            // Send typing status to server
            axios.post(`${API_BASE}/groups/${currentGroupId}/typing`, {
                is_typing: true
            }).catch(err => console.error('Error sending typing status:', err));
            
            // Clear existing timeout
            if (typingTimeout) {
                clearTimeout(typingTimeout);
            }
            
            // Auto-stop typing after 3 seconds of no input
            typingTimeout = setTimeout(() => {
                stopTyping();
            }, 3000);
        }
        
        function stopTyping() {
            if (!currentGroupId) return;
            
            axios.post(`${API_BASE}/groups/${currentGroupId}/typing`, {
                is_typing: false
            }).catch(err => console.error('Error stopping typing status:', err));
        }
        
        // Poll for typing users
        function startTypingPolling() {
            if (typingPollingInterval) {
                clearInterval(typingPollingInterval);
            }
            
            typingPollingInterval = setInterval(async () => {
                if (!currentGroupId) return;
                
                try {
                    const response = await axios.get(`${API_BASE}/groups/${currentGroupId}/typing`);
                    const typingUsers = response.data.typing_users || [];
                    
                    const container = document.getElementById('typing-indicator-container');
                    const nameSpan = document.getElementById('typing-user-name');
                    
                    if (!container) return;
                    
                    if (typingUsers.length > 0) {
                        // Show typing indicator
                        if (typingUsers.length === 1) {
                            nameSpan.textContent = typingUsers[0].user_name;
                        } else if (typingUsers.length === 2) {
                            nameSpan.textContent = `${typingUsers[0].user_name} and ${typingUsers[1].user_name}`;
                        } else {
                            nameSpan.textContent = `${typingUsers[0].user_name} and ${typingUsers.length - 1} others`;
                        }
                        container.style.display = 'block';
                    } else {
                        // Hide typing indicator
                        container.style.display = 'none';
                    }
                } catch (error) {
                    // Silent fail for typing - not critical
                }
            }, 3000); // Check every 3 seconds (reduced from 1s for performance)
        }
        
        function stopTypingPolling() {
            if (typingPollingInterval) {
                clearInterval(typingPollingInterval);
                typingPollingInterval = null;
            }
            
            // Hide typing indicator
            const container = document.getElementById('typing-indicator-container');
            if (container) {
                container.style.display = 'none';
            }
        }
    </script>
</body>
</html>

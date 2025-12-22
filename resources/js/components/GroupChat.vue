<template>
  <div class="chat-container">
    <!-- Chat Header -->
    <div class="chat-header">
      <div class="chat-header-left">
        <h3>{{ currentGroup ? currentGroup.name : 'Select a Group' }}</h3>
        <span v-if="currentGroup" class="member-count">
          {{ onlineMembers }} online / {{ totalMembers }} members
        </span>
      </div>
      <div class="chat-header-right">
        <button @click="showGroupModal = true" class="btn-new-group">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/>
          </svg>
          New Group
        </button>
      </div>
    </div>

    <div class="chat-body">
      <!-- Sidebar - Group List -->
      <div class="chat-sidebar">
        <div class="sidebar-header">
          <h4>Your Groups</h4>
        </div>
        <div class="group-list">
          <div 
            v-for="group in groups" 
            :key="group.id"
            :class="['group-item', { active: currentGroup?.id === group.id }]"
            @click="selectGroup(group)"
          >
            <div class="group-avatar">
              {{ group.name.charAt(0).toUpperCase() }}
            </div>
            <div class="group-info">
              <div class="group-name">{{ group.name }}</div>
              <div class="group-last-message">Click to open chat...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Chat Area -->
      <div class="chat-main">
        <div v-if="!currentGroup" class="no-group-selected">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ccc">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-width="2"/>
          </svg>
          <p>Select a group to start chatting</p>
        </div>

        <div v-else class="chat-content">
          <!-- Messages Container -->
          <div class="messages-container" ref="messagesContainer">
            <div 
              v-for="message in messages" 
              :key="message.id"
              :class="['message', message.user_id === currentUserId ? 'message-own' : 'message-other']"
            >
              <div class="message-avatar" v-if="message.user_id !== currentUserId">
                {{ message.user_name?.charAt(0) || 'U' }}
              </div>
              <div class="message-content">
                <div class="message-header" v-if="message.user_id !== currentUserId">
                  <span class="message-author">{{ message.user_name }}</span>
                  <span class="message-time">{{ formatTime(message.timestamp) }}</span>
                </div>
                <div class="message-bubble">
                  <p>{{ message.content }}</p>
                </div>
                <div class="message-time-own" v-if="message.user_id === currentUserId">
                  {{ formatTime(message.timestamp) }}
                </div>
              </div>
            </div>

            <!-- Typing Indicator -->
            <div v-if="typingUsers.length > 0" class="typing-indicator">
              <span>{{ typingUsers.join(', ') }} {{ typingUsers.length > 1 ? 'are' : 'is' }} typing...</span>
            </div>
          </div>

          <!-- Message Input -->
          <div class="message-input-container">
            <div class="message-input">
              <input 
                v-model="newMessage" 
                @keyup.enter="sendMessage"
                @input="handleTyping"
                placeholder="Type a message..."
                :disabled="!isConnected"
              />
              <button @click="sendMessage" :disabled="!newMessage.trim() || !isConnected">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
              </button>
            </div>
            <div class="connection-status">
              <span :class="['status-indicator', isConnected ? 'connected' : 'disconnected']"></span>
              {{ isConnected ? 'Connected' : 'Disconnected' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- New Group Modal -->
    <div v-if="showGroupModal" class="modal-overlay" @click.self="showGroupModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Create New Group</h3>
          <button @click="showGroupModal = false" class="btn-close">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Group Name</label>
            <input v-model="newGroup.name" type="text" placeholder="Enter group name" />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="newGroup.description" placeholder="Enter description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Add Members (User IDs)</label>
            <input v-model="newGroup.memberIds" type="text" placeholder="e.g., 1,2,3" />
            <small>Enter user IDs separated by commas</small>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showGroupModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="createGroup" class="btn btn-primary">Create Group</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GroupChat',
  
  data() {
    return {
      // Connection
      isConnected: false,
      connectionInfo: null,
      
      // Groups
      groups: [],
      currentGroup: null,
      
      // Messages
      messages: [],
      newMessage: '',
      
      // Users
      currentUserId: null,
      typingUsers: [],
      onlineMembers: 0,
      totalMembers: 0,
      
      // UI
      showGroupModal: false,
      newGroup: {
        name: '',
        description: '',
        memberIds: ''
      },
      
      // Typing timeout
      typingTimeout: null,
    };
  },
  
  async mounted() {
    await this.initialize();
  },
  
  methods: {
    async initialize() {
      try {
        // Get connection info from Laravel API
        const response = await fetch('/api/chat/connection-info', {
          headers: {
            'Authorization': `Bearer ${this.getAuthToken()}`,
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error('Failed to get connection info');
        }
        
        const data = await response.json();
        this.connectionInfo = data.data;
        this.currentUserId = data.data.user_id;
        
        // Load groups
        await this.loadGroups();
        
        // Note: For real gRPC streaming, you'd need gRPC-Web client library
        // For now, we'll simulate with polling or WebSocket
        this.isConnected = true;
        
      } catch (error) {
        console.error('Initialization error:', error);
        this.showError('Failed to initialize chat');
      }
    },
    
    async loadGroups() {
      try {
        const response = await fetch('/api/chat/groups', {
          headers: {
            'Authorization': `Bearer ${this.getAuthToken()}`,
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) throw new Error('Failed to load groups');
        
        const data = await response.json();
        this.groups = data.data;
        
      } catch (error) {
        console.error('Load groups error:', error);
      }
    },
    
    async selectGroup(group) {
      this.currentGroup = group;
      this.messages = [];
      
      try {
        // Load message history
        const response = await fetch(`/api/chat/groups/${group.id}/messages?limit=50`, {
          headers: {
            'Authorization': `Bearer ${this.getAuthToken()}`,
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) throw new Error('Failed to load messages');
        
        const data = await response.json();
        this.messages = data.data.messages;
        
        // Load members
        const membersResponse = await fetch(`/api/chat/groups/${group.id}`, {
          headers: {
            'Authorization': `Bearer ${this.getAuthToken()}`,
            'Accept': 'application/json'
          }
        });
        
        if (membersResponse.ok) {
          const membersData = await membersResponse.json();
          this.totalMembers = membersData.data.members.length;
          this.onlineMembers = membersData.data.members.filter(m => m.online).length;
        }
        
        this.$nextTick(() => {
          this.scrollToBottom();
        });
        
      } catch (error) {
        console.error('Select group error:', error);
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim() || !this.currentGroup) return;
      
      const message = {
        id: Date.now(),
        group_id: this.currentGroup.id,
        user_id: this.currentUserId,
        user_name: 'You',
        content: this.newMessage,
        timestamp: Math.floor(Date.now() / 1000),
        type: 0
      };
      
      this.messages.push(message);
      this.newMessage = '';
      
      this.$nextTick(() => {
        this.scrollToBottom();
      });
      
      // TODO: Send via gRPC stream
      console.log('Sending message:', message);
    },
    
    handleTyping() {
      // Clear existing timeout
      if (this.typingTimeout) {
        clearTimeout(this.typingTimeout);
      }
      
      // TODO: Send typing indicator via gRPC
      
      // Reset after 2 seconds
      this.typingTimeout = setTimeout(() => {
        // TODO: Send not typing indicator
      }, 2000);
    },
    
    async createGroup() {
      try {
        const memberIds = this.newGroup.memberIds
          .split(',')
          .map(id => parseInt(id.trim()))
          .filter(id => !isNaN(id));
        
        if (!this.newGroup.name || memberIds.length === 0) {
          this.showError('Please fill all required fields');
          return;
        }
        
        const response = await fetch('/api/chat/groups', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${this.getAuthToken()}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            name: this.newGroup.name,
            description: this.newGroup.description,
            member_ids: memberIds
          })
        });
        
        if (!response.ok) throw new Error('Failed to create group');
        
        const data = await response.json();
        this.groups.push(data.data);
        
        // Reset form
        this.newGroup = { name: '', description: '', memberIds: '' };
        this.showGroupModal = false;
        
        this.showSuccess('Group created successfully');
        
      } catch (error) {
        console.error('Create group error:', error);
        this.showError('Failed to create group');
      }
    },
    
    scrollToBottom() {
      const container = this.$refs.messagesContainer;
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    },
    
    formatTime(timestamp) {
      const date = new Date(timestamp * 1000);
      const now = new Date();
      const diff = now - date;
      
      if (diff < 60000) {
        return 'Just now';
      } else if (diff < 3600000) {
        return `${Math.floor(diff / 60000)}m ago`;
      } else if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
      } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
      }
    },
    
    getAuthToken() {
      // Get JWT token from localStorage or wherever you store it
      return localStorage.getItem('auth_token') || '';
    },
    
    showError(message) {
      // TODO: Implement toast notification
      alert(message);
    },
    
    showSuccess(message) {
      // TODO: Implement toast notification
      alert(message);
    }
  }
};
</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #f5f7fa;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1 .5rem;
  background: white;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.chat-header-left h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.member-count {
  font-size: 0.875rem;
  color: #7f8c8d;
  margin-top: 0.25rem;
}

.btn-new-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-new-group:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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

.sidebar-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e0e0e0;
}

.sidebar-header h4 {
  margin: 0;
  font-size: 1rem;
  color: #2c3e50;
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
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-left: 3px solid #667eea;
}

.group-avatar {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: 600;
}

.group-info {
  flex: 1;
  min-width: 0;
}

.group-name {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.group-last-message {
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

.no-group-selected {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #95a5a6;
}

.no-group-selected p {
  margin-top: 1rem;
  font-size: 1.125rem;
}

.chat-content {
  flex: 1;
  display: flex;
  flex-direction: column;
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  flex-shrink: 0;
}

.message-content {
  max-width: 60%;
  margin: 0 1rem;
}

.message-own .message-content {
  align-items: flex-end;
}

.message-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.message-author {
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.875rem;
}

.message-time {
  font-size: 0.75rem;
  color: #95a5a6;
}

.message-bubble {
  background: white;
  padding: 0.75rem 1rem;
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.message-own .message-bubble {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.message-bubble p {
  margin: 0;
  word-wrap: break-word;
}

.message-time-own {
  font-size: 0.75rem;
  color: #95a5a6;
  margin-top: 0.25rem;
  text-align: right;
}

.typing-indicator {
  font-size: 0.875rem;
  color: #7f8c8d;
  font-style: italic;
  padding: 0.5rem 1rem;
}

.message-input-container {
  border-top: 1px solid #e0e0e0;
  background: white;
  padding: 1rem 1.5rem;
}

.message-input {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
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
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.message-input button {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.message-input button:hover:not(:disabled) {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.message-input button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.connection-status {
  font-size: 0.8125rem;
  color: #7f8c8d;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #95a5a6;
}

.status-indicator.connected {
  background: #2ecc71;
  box-shadow: 0 0 8px rgba(46, 204, 113, 0.5);
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.btn-close {
  background: none;
  border: none;
  font-size: 2rem;
  color: #95a5a6;
  cursor: pointer;
  line-height: 1;
  padding: 0;
  width: 32px;
  height: 32px;
}

.modal-body {
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 0.9375rem;
  transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group small {
  display: block;
  margin-top: 0.25rem;
  color: #7f8c8d;
  font-size: 0.8125rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e0e0e0;
}

.btn {
  padding: 0.625rem 1.5rem;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: #ecf0f1;
  color: #2c3e50;
}

.btn-secondary:hover {
  background: #d5dbdb;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
</style>

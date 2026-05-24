/* ===================================
   CHAT POPUP JAVASCRIPT - Modern Implementation
   =================================== */

// Global chat state
const chatState = {
    isOpen: false,
    isMinimized: false,
    currentView: 'conversations', // Default: conversations (not users)
    currentConversationId: null,
    currentUserId: null,
    currentUserName: null,
    currentUserData: null, // Store full user data for profile modal
    lastMessageId: 0,
    pollingInterval: null,
    unreadInterval: null
};

// Initialize chat on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat popup initialized');
    startUnreadCountPolling();
});

// ===== POPUP CONTROLS =====

function toggleChatPopup() {
    const popup = document.getElementById('chatPopup');
    const btn = document.getElementById('chatFloatingBtn');

    if (chatState.isOpen) {
        closeChatPopup();
    } else {
        chatState.isOpen = true;
        chatState.isMinimized = false;
        popup.style.display = 'flex';
        btn.style.display = 'none';

        // Load conversations by default (not all users)
        if (chatState.currentView === 'conversations') {
            loadConversations();
        }
    }
}

function closeChatPopup() {
    const popup = document.getElementById('chatPopup');
    const btn = document.getElementById('chatFloatingBtn');

    chatState.isOpen = false;
    popup.style.display = 'none';
    btn.style.display = 'flex';

    stopChatPolling();
}

function minimizeChatPopup() {
    closeChatPopup();
}

// ===== CONVERSATIONS LOADING =====

async function loadConversations(query = '') {
    const container = document.getElementById('chatConversationsList');

    // Show loading
    container.innerHTML = `
        <div class="chat-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Yuklanmoqda...</p>
        </div>
    `;

    try {
        const url = `${window.CHAT_BASE_URL}/api/chat/conversations${query ? '?q=' + encodeURIComponent(query) : ''}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.data) {
            renderConversations(data.data);
        } else {
            throw new Error(data.message || 'Failed to load conversations');
        }

    } catch (error) {
        console.error('Error loading conversations:', error);
        container.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Suhbatlarni yuklashda xatolik</p>
            </div>
        `;
    }
}

function renderConversations(conversations) {
    const container = document.getElementById('chatConversationsList');

    if (!conversations || conversations.length === 0) {
        container.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-comments"></i>
                <p>Hali suhbatlar yo'q</p>
                <small>Yangi suhbat boshlash uchun "Kontaktlar" tugmasini bosing</small>
            </div>
        `;
        return;
    }

    container.innerHTML = conversations.map(conv => {
        const unreadBadge = conv.unread_count > 0 ? `
            <div class="chat-unread-count">${conv.unread_count > 99 ? '99+' : conv.unread_count}</div>
        ` : '';

        return `
            <div class="chat-user-item" onclick="openExistingConversation(${conv.conversation_id}, ${conv.user_id}, '${escapeHtml(conv.name)}')">
                <div class="chat-avatar">${conv.avatar}</div>
                <div class="chat-user-details">
                    <div class="chat-user-name">${escapeHtml(conv.name)}</div>
                    <div class="chat-user-email">${escapeHtml(conv.last_message).substring(0, 30)}${conv.last_message.length > 30 ? '...' : ''}</div>
                </div>
                ${conv.is_online ? '<div class="chat-online-indicator"></div>' : ''}
                ${unreadBadge}
            </div>
        `;
    }).join('');
}

function searchConversations() {
    const query = document.getElementById('chatConversationSearchInput').value;
    loadConversations(query);
}

// ===== CONTACTS LOADING (ALL USERS) =====

async function loadContacts(query = '') {
    const container = document.getElementById('chatContactsList');

    // Show loading
    container.innerHTML = `
        <div class="chat-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Yuklanmoqda...</p>
        </div>
    `;

    try {
        const url = `${window.CHAT_BASE_URL}/api/chat/users${query ? '?q=' + encodeURIComponent(query) : ''}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.data) {
            renderContacts(data.data);
        } else {
            throw new Error(data.message || 'Failed to load contacts');
        }

    } catch (error) {
        console.error('Error loading contacts:', error);
        container.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Kontaktlarni yuklashda xatolik</p>
            </div>
        `;
    }
}

function renderContacts(users) {
    const container = document.getElementById('chatContactsList');

    if (!users || users.length === 0) {
        container.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-users"></i>
                <p>Kontaktlar topilmadi</p>
            </div>
        `;
        return;
    }

    // Store contacts globally for quick access
    window.chatContacts = users;

    container.innerHTML = users.map(user => `
        <div class="chat-user-item" onclick="openChatWithContactData(${user.id})">
            <div class="chat-avatar">${user.avatar}</div>
            <div class="chat-user-details">
                <div class="chat-user-name">${escapeHtml(user.name)}</div>
                <div class="chat-user-email">${escapeHtml(user.email || user.nickname || '')}</div>
            </div>
            ${user.is_online ? '<div class="chat-online-indicator"></div>' : ''}
        </div>
    `).join('');
}

function openChatWithContactData(userId) {
    const user = window.chatContacts?.find(u => u.id === userId);
    if (user) {
        chatState.currentUserData = user;
        openChatWithUser(userId, user.name);
    } else {
        openChatWithUser(userId, 'User');
    }
}

function searchContacts() {
    const query = document.getElementById('chatContactSearchInput').value;
    loadContacts(query);
}

// ===== LEGACY USER LOADING (KEPT FOR COMPATIBILITY) =====

async function loadChatUsers(query = '') {
    const container = document.getElementById('chatUsersList');

    // Show loading
    container.innerHTML = `
        <div class="chat-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Yuklanmoqda...</p>
        </div>
    `;

    try {
        const url = `${window.CHAT_BASE_URL}/api/chat/users${query ? '?q=' + encodeURIComponent(query) : ''}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.data) {
            renderChatUsers(data.data);
        } else {
            throw new Error(data.message || 'Failed to load users');
        }

    } catch (error) {
        console.error('Error loading users:', error);
        container.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Foydalanuvchilarni yuklashda xatolik</p>
            </div>
        `;
    }
}

function renderChatUsers(users) {
    const container = document.getElementById('chatUsersList');

    if (!users || users.length === 0) {
        container.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-users"></i>
                <p>Foydalanuvchilar topilmadi</p>
            </div>
        `;
        return;
    }

    // Store users globally for quick access
    window.chatUsers = users;

    container.innerHTML = users.map(user => `
        <div class="chat-user-item" onclick="openChatWithUserData(${user.id})">
            <div class="chat-avatar">${user.avatar}</div>
            <div class="chat-user-details">
                <div class="chat-user-name">${escapeHtml(user.name)}</div>
                <div class="chat-user-email">${escapeHtml(user.email || user.nickname || '')}</div>
            </div>
            ${user.is_online ? '<div class="chat-online-indicator"></div>' : ''}
        </div>
    `).join('');
}

function openChatWithUserData(userId) {
    const user = window.chatUsers?.find(u => u.id === userId);
    if (user) {
        chatState.currentUserData = user;
        openChatWithUser(userId, user.name);
    } else {
        openChatWithUser(userId, 'User');
    }
}

function searchChatUsers() {
    const query = document.getElementById('chatSearchInput').value;
    loadChatUsers(query);
}

// ===== CONVERSATION MANAGEMENT =====

async function openChatWithUser(userId, userName) {
    try {
        // Show loading in messages view
        switchToChatView('messages');

        const messagesContainer = document.getElementById('chatMessagesContainer');
        messagesContainer.innerHTML = `
            <div class="chat-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Suhbat ochilmoqda...</p>
            </div>
        `;

        // Get or create conversation
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ user_id: userId })
        });

        const data = await response.json();

        if (data.success && data.data) {
            chatState.currentConversationId = data.data.conversation_id;
            chatState.currentUserId = userId;
            chatState.currentUserName = userName;
            chatState.lastMessageId = 0;

            // Update header
            document.getElementById('chatCurrentUserName').textContent = userName;
            document.getElementById('chatCurrentAvatar').textContent = userName.charAt(0).toUpperCase();

            // Load messages
            await loadChatMessages();

            // Start polling
            startChatPolling();

            // Mark as read
            markConversationAsRead();

        } else {
            throw new Error(data.message || 'Failed to open conversation');
        }

    } catch (error) {
        console.error('Error opening conversation:', error);
        const messagesContainer = document.getElementById('chatMessagesContainer');
        messagesContainer.innerHTML = `
            <div class="chat-empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Suhbatni ochishda xatolik</p>
            </div>
        `;
    }
}

// ===== MESSAGE LOADING =====

async function loadChatMessages(isPolling = false) {
    if (!chatState.currentConversationId) return;

    try {
        const url = `${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}/messages${chatState.lastMessageId > 0 ? '?after=' + chatState.lastMessageId : ''}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.data) {
            if (data.data.length > 0) {
                if (isPolling) {
                    // Append new messages
                    data.data.forEach(msg => appendChatMessage(msg));
                    chatState.lastMessageId = data.data[data.data.length - 1].id;
                } else {
                    // Initial load - render all
                    renderChatMessages(data.data);
                    if (data.data.length > 0) {
                        chatState.lastMessageId = data.data[data.data.length - 1].id;
                    }
                }
            }
        }

    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

function renderChatMessages(messages) {
    const container = document.getElementById('chatMessagesContainer');

    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="chat-empty-state">
                <i class="fas fa-comments"></i>
                <p>Hali xabarlar yo'q</p>
            </div>
        `;
        return;
    }

    container.innerHTML = messages.map(msg => createMessageHTML(msg)).join('');
    scrollToBottom();
}

function appendChatMessage(message) {
    const container = document.getElementById('chatMessagesContainer');

    // Remove empty state if exists
    const emptyState = container.querySelector('.chat-empty-state');
    if (emptyState) {
        emptyState.remove();
    }

    container.insertAdjacentHTML('beforeend', createMessageHTML(message));
    scrollToBottom();
}

function createMessageHTML(msg) {
    const isOwn = msg.is_own ? 'own' : '';

    let content = '';

    // Handle image type
    if (msg.type === 'image' && msg.file_url) {
        const fileName = msg.file_url.split('/').pop();
        content = `
            <div class="chat-message-file chat-image-container">
                <img src="${msg.file_url}" alt="Image" loading="lazy" onclick="window.open('${msg.file_url}', '_blank')">
                <a href="${msg.file_url}" download="${fileName}" class="chat-image-download" title="Yuklab olish">
                    <i class="fas fa-download"></i>
                </a>
                <div class="chat-message-time">${msg.created_at}</div>
            </div>
        `;
    }
    // Handle file type (check if it's a video by extension)
    else if (msg.type === 'file' && msg.file_url) {
        const fileExt = msg.file_url.split('.').pop().toLowerCase();
        const videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov'];

        if (videoExtensions.includes(fileExt)) {
            content = `
                <div class="chat-message-file">
                    <video controls style="max-width: 100%; border-radius: 8px;">
                        <source src="${msg.file_url}" type="video/${fileExt === 'mov' ? 'quicktime' : fileExt}">
                    </video>
                    <div class="chat-message-time">${msg.created_at}</div>
                </div>
            `;
        } else {
            content = `
                <div class="chat-message-file">
                    <a href="${msg.file_url}" target="_blank" download>
                        <i class="fas fa-file"></i> ${escapeHtml(msg.message)}
                    </a>
                    <div class="chat-message-time">${msg.created_at}</div>
                </div>
            `;
        }
    }
    // Handle text messages
    else {
        content = `
            <p class="chat-message-text">${escapeHtml(msg.message)}</p>
            <div class="chat-message-time">${msg.created_at}</div>
        `;
    }

    // Delete button (dropdown menu)
    const deleteMenu = `
        <div class="chat-message-menu">
            <button class="chat-message-menu-btn" onclick="toggleDeleteMenu(event, ${msg.id})">
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="chat-message-delete-dropdown" id="deleteMenu${msg.id}" style="display: none;">
                <button onclick="deleteMessageForMe(${msg.id})">
                    <i class="fas fa-trash"></i> Men uchun o'chirish
                </button>
                ${msg.can_delete_for_everyone ? `
                    <button onclick="deleteMessageForEveryone(${msg.id})" class="delete-everyone">
                        <i class="fas fa-trash-alt"></i> Hamma uchun o'chirish
                    </button>
                ` : ''}
            </div>
        </div>
    `;

    return `
        <div class="chat-message ${isOwn}" data-message-id="${msg.id}">
            <div class="chat-message-bubble">
                ${content}
                ${deleteMenu}
            </div>
        </div>
    `;
}

function scrollToBottom() {
    const container = document.getElementById('chatMessagesContainer');
    container.scrollTop = container.scrollHeight;
}

// ===== MESSAGE SENDING =====

async function sendChatMessage() {
    const input = document.getElementById('chatMessageInput');
    const message = input.value.trim();

    if (!message || !chatState.currentConversationId) return;

    // Disable input
    input.disabled = true;

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}/send`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message: message })
        });

        const data = await response.json();

        if (data.success && data.data) {
            // Clear input
            input.value = '';

            // Append message
            appendChatMessage(data.data);
            chatState.lastMessageId = data.data.id;

        } else {
            throw new Error(data.message || 'Failed to send message');
        }

    } catch (error) {
        console.error('Error sending message:', error);
        alert('Xabar yuborishda xatolik yuz berdi');
    } finally {
        input.disabled = false;
        input.focus();
    }
}

function handleChatKeyPress(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        sendChatMessage();
    }
}

// ===== FILE UPLOAD =====

async function handleChatFileUpload(file) {
    if (!file || !chatState.currentConversationId) return;

    if (file.size > 10 * 1024 * 1024) {
        alert('Fayl hajmi 10MB dan oshmasligi kerak');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('file', file);

        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}/upload`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: formData
        });

        const data = await response.json();

        if (data.success && data.data) {
            appendChatMessage(data.data);
            chatState.lastMessageId = data.data.id;
        } else {
            throw new Error(data.message || 'Failed to upload file');
        }

    } catch (error) {
        console.error('Error uploading file:', error);
        alert('Fayl yuklashda xatolik yuz berdi');
    }

    // Reset file input
    document.getElementById('chatFileInput').value = '';
}

// ===== POLLING =====

function startChatPolling() {
    stopChatPolling();

    chatState.pollingInterval = setInterval(() => {
        if (chatState.currentConversationId && chatState.isOpen && !chatState.isMinimized) {
            loadChatMessages(true);
        }
    }, 3000); // Poll every 3 seconds
}

function stopChatPolling() {
    if (chatState.pollingInterval) {
        clearInterval(chatState.pollingInterval);
        chatState.pollingInterval = null;
    }
}

// ===== UNREAD COUNT =====

function startUnreadCountPolling() {
    updateUnreadCount();

    chatState.unreadInterval = setInterval(() => {
        updateUnreadCount();
    }, 10000); // Check every 10 seconds
}

async function updateUnreadCount() {
    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/unread-count`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.data) {
            const count = data.data.count || 0;
            const badge = document.getElementById('chatUnreadBadge');

            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

    } catch (error) {
        console.error('Error updating unread count:', error);
    }
}

async function markConversationAsRead() {
    if (!chatState.currentConversationId) return;

    try {
        await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        updateUnreadCount();

    } catch (error) {
        console.error('Error marking as read:', error);
    }
}

// ===== VIEW SWITCHING =====

function switchToChatView(viewName) {
    chatState.currentView = viewName;

    const conversationsView = document.getElementById('chatConversationsView');
    const contactsView = document.getElementById('chatContactsView');
    const messagesView = document.getElementById('chatMessagesView');

    conversationsView.classList.remove('active');
    contactsView.classList.remove('active');
    messagesView.classList.remove('active');

    if (viewName === 'conversations') {
        conversationsView.classList.add('active');
        stopChatPolling();
    } else if (viewName === 'contacts') {
        contactsView.classList.add('active');
        stopChatPolling();
    } else if (viewName === 'messages') {
        messagesView.classList.add('active');
    }
}

function backToUsersList() {
    // Go back to conversations view (not contacts)
    switchToChatView('conversations');
    chatState.currentConversationId = null;
    chatState.currentUserId = null;
    chatState.lastMessageId = 0;
    stopChatPolling();

    // Update bottom nav active state
    document.querySelectorAll('.chat-nav-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('[data-view="conversations"]').classList.add('active');

    loadConversations();
}

// Open existing conversation (from conversations list)
async function openExistingConversation(conversationId, userId, userName) {
    chatState.currentConversationId = conversationId;
    chatState.currentUserId = userId;
    chatState.currentUserName = userName;
    chatState.lastMessageId = 0;

    // Update header
    document.getElementById('chatCurrentUserName').textContent = userName;
    document.getElementById('chatCurrentAvatar').textContent = userName.charAt(0).toUpperCase();

    // Switch to messages view
    switchToChatView('messages');

    // Load messages
    await loadChatMessages();

    // Start polling
    startChatPolling();

    // Mark as read
    markConversationAsRead();
}

// Bottom navigation handler
function showChatView(viewName) {
    chatState.currentView = viewName;

    // Update nav buttons
    document.querySelectorAll('.chat-nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-view="${viewName}"]`).classList.add('active');

    // Switch view and load data
    if (viewName === 'conversations') {
        switchToChatView('conversations');
        loadConversations();
    } else if (viewName === 'contacts') {
        switchToChatView('contacts');
        loadContacts();
    }
}

// ===== SETTINGS FUNCTIONS =====

// Show settings modal
function showChatSettings() {
    document.getElementById('chatSettingsModal').style.display = 'flex';
    loadSettings();
}

function closeChatSettings() {
    document.getElementById('chatSettingsModal').style.display = 'none';
}

// Load settings from localStorage
function loadSettings() {
    const notificationSound = localStorage.getItem('chat_notification_sound') !== 'false';
    const desktopNotifications = localStorage.getItem('chat_desktop_notifications') === 'true';
    const showOnlineStatus = localStorage.getItem('chat_show_online') !== 'false';

    document.getElementById('notificationSound').checked = notificationSound;
    document.getElementById('desktopNotifications').checked = desktopNotifications;
    document.getElementById('showOnlineStatus').checked = showOnlineStatus;

    // Load and apply current theme
    const currentTheme = localStorage.getItem('chat_theme') || 'light';
    updateThemeDescription(currentTheme);
}

// Nickname modal
function showNicknameModal() {
    closeChatSettings();
    document.getElementById('nicknameModal').style.display = 'flex';
}

function closeNicknameModal() {
    document.getElementById('nicknameModal').style.display = 'none';
}

async function saveNickname() {
    const nickname = document.getElementById('nicknameInput').value.trim();

    if (!nickname) {
        alert('Nickname kiriting!');
        return;
    }

    // Validate nickname (only letters, numbers, underscore)
    if (!/^[a-zA-Z0-9_]+$/.test(nickname)) {
        alert('Nickname faqat harflar, raqamlar va pastki chiziq (_) dan iborat bo\'lishi kerak!');
        return;
    }

    // Check minimum length
    if (nickname.length < 3) {
        alert('Nickname kamida 3 ta belgidan iborat bo\'lishi kerak!');
        return;
    }

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/chat/update-nickname`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ nickname: nickname })
        });

        const data = await response.json();

        // Check if response is successful
        if (response.ok && data.success) {
            // Update display
            document.getElementById('currentNickname').textContent = '@' + nickname;
            closeNicknameModal();
            alert('✅ Nickname muvaffaqiyatli o\'zgartirildi!');
        } else {
            // Handle different error formats
            let errorMessage = 'Xatolik yuz berdi';

            if (data.message) {
                errorMessage = data.message;
            } else if (data.error) {
                errorMessage = data.error;
            } else if (data.errors) {
                // Laravel validation errors
                const firstError = Object.values(data.errors)[0];
                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            }

            alert('❌ ' + errorMessage);
        }
    } catch (error) {
        console.error('Error saving nickname:', error);
        alert('❌ Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
    }
}

// Notification settings
function toggleNotificationSound(enabled) {
    localStorage.setItem('chat_notification_sound', enabled);
    if (enabled) {
        console.log('Notification sound enabled');
    }
}

function toggleDesktopNotifications(enabled) {
    localStorage.setItem('chat_desktop_notifications', enabled);

    if (enabled) {
        // Request permission
        if ('Notification' in window) {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification('Chat Bildirishnomalar', {
                        body: 'Desktop bildirishnomalar yoqildi!',
                        icon: '/favicon.ico'
                    });
                } else {
                    document.getElementById('desktopNotifications').checked = false;
                    localStorage.setItem('chat_desktop_notifications', false);
                    alert('Brauzer sozlamalaridan bildirishnomaga ruxsat bering!');
                }
            });
        } else {
            alert('Sizning brauzeringiz bildirishnomalarni qo\'llab-quvvatlamaydi!');
            document.getElementById('desktopNotifications').checked = false;
            localStorage.setItem('chat_desktop_notifications', false);
        }
    }
}

function toggleOnlineStatus(enabled) {
    localStorage.setItem('chat_show_online', enabled);
    // This would update the backend in a real implementation
    console.log('Online status visibility:', enabled);
}

// Theme modal functions
function showThemeModal() {
    closeChatSettings();
    document.getElementById('themeModal').style.display = 'flex';

    // Update active theme selection
    const currentTheme = localStorage.getItem('chat_theme') || 'light';
    document.querySelectorAll('.theme-option').forEach(option => {
        option.classList.remove('active');
        if (option.dataset.theme === currentTheme) {
            option.classList.add('active');
        }
    });
}

function closeThemeModal() {
    document.getElementById('themeModal').style.display = 'none';
}

function selectTheme(themeName) {
    // Save theme to localStorage
    localStorage.setItem('chat_theme', themeName);

    // Apply theme
    applyTheme(themeName);

    // Update theme description in settings
    updateThemeDescription(themeName);

    // Close modal and return to settings
    closeThemeModal();
    showChatSettings();
}

function applyTheme(themeName) {
    const body = document.body;

    // Remove all theme classes
    body.classList.remove('chat-light-theme', 'chat-dark-theme');

    if (themeName === 'dark') {
        body.classList.add('chat-dark-theme');
    } else if (themeName === 'auto') {
        // Auto theme: detect system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            body.classList.add('chat-dark-theme');
        } else {
            body.classList.add('chat-light-theme');
        }
    } else {
        // Light theme (default)
        body.classList.add('chat-light-theme');
    }
}

function updateThemeDescription(themeName) {
    const descriptions = {
        'light': 'Yorug\' rejim',
        'dark': 'Qorong\'i rejim',
        'auto': 'Avtomatik'
    };

    const descElement = document.getElementById('currentThemeDesc');
    if (descElement) {
        descElement.textContent = descriptions[themeName] || 'Yorug\' rejim';
    }
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('chat_theme') || 'light';
    applyTheme(savedTheme);

    // Listen for system theme changes when in auto mode
    if (savedTheme === 'auto') {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem('chat_theme') === 'auto') {
                applyTheme('auto');
            }
        });
    }
});

// ===== DELETE FUNCTIONALITY =====

function toggleConversationMenu() {
    const menu = document.getElementById('conversationMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function toggleDeleteMenu(event, messageId) {
    event.stopPropagation();

    // Close all other menus
    document.querySelectorAll('.chat-message-delete-dropdown').forEach(menu => {
        if (menu.id !== `deleteMenu${messageId}`) {
            menu.style.display = 'none';
        }
    });

    // Toggle this menu
    const menu = document.getElementById(`deleteMenu${messageId}`);
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close delete menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.chat-message-menu') && !event.target.closest('.chat-header-actions')) {
        document.querySelectorAll('.chat-message-delete-dropdown').forEach(menu => {
            menu.style.display = 'none';
        });

        // Close conversation menu
        const conversationMenu = document.getElementById('conversationMenu');
        if (conversationMenu) {
            conversationMenu.style.display = 'none';
        }
    }
});

async function deleteMessageForMe(messageId) {
    if (!confirm('Xabarni men uchun o\'chirish?')) {
        return;
    }

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}/message/${messageId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            // Remove message from UI
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                messageElement.remove();
            }

            // Show success message
            showNotification('Xabar o\'chirildi', 'success');
        } else {
            showNotification(data.message || 'Xatolik yuz berdi', 'error');
        }

    } catch (error) {
        console.error('Delete message error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    }
}

async function deleteMessageForEveryone(messageId) {
    if (!confirm('Xabarni hamma uchun o\'chirish? Bu amaldan qaytarib bo\'lmaydi!')) {
        return;
    }

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}/message/${messageId}/everyone`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            // Remove message from UI
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                messageElement.remove();
            }

            // Show success message
            showNotification('Xabar hamma uchun o\'chirildi', 'success');
        } else {
            showNotification(data.message || 'Xatolik yuz berdi', 'error');
        }

    } catch (error) {
        console.error('Delete message for everyone error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    }
}

async function deleteCurrentConversation() {
    if (!chatState.currentConversationId) {
        return;
    }

    if (!confirm('Suhbatni o\'chirish? Barcha xabarlar sizning uchun o\'chiriladi.')) {
        return;
    }

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/conversation/${chatState.currentConversationId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            // Go back to conversations list
            backToUsersList();

            // Reload conversations
            await loadConversations();

            // Show success message
            showNotification('Suhbat o\'chirildi', 'success');
        } else {
            showNotification(data.message || 'Xatolik yuz berdi', 'error');
        }

    } catch (error) {
        console.error('Delete conversation error:', error);
        showNotification('Xatolik yuz berdi', 'error');
    }
}

function showNotification(message, type = 'info') {
    // Simple notification (you can enhance this)
    const notification = document.createElement('div');
    notification.className = `chat-notification chat-notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 100000;
        animation: slideInRight 0.3s ease-out;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ===== USER PROFILE =====

function showUserProfile() {
    if (!chatState.currentUserId) {
        return;
    }

    const modal = document.getElementById('userProfileModal');
    if (!modal) return;

    // If we have stored user data, use it
    if (chatState.currentUserData) {
        populateUserProfile(chatState.currentUserData);
        modal.style.display = 'flex';
        return;
    }

    // Otherwise, fetch user data
    fetchUserProfile(chatState.currentUserId);
}

async function fetchUserProfile(userId) {
    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/users`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN
            }
        });

        const data = await response.json();

        if (data.success && data.data) {
            // Find the user in the list
            const user = data.data.find(u => u.id === userId);
            if (user) {
                chatState.currentUserData = user;
                populateUserProfile(user);
                document.getElementById('userProfileModal').style.display = 'flex';
            }
        }
    } catch (error) {
        console.error('Error fetching user profile:', error);
    }
}

function populateUserProfile(user) {
    // Profile photo/avatar
    const avatarElement = document.getElementById('profileAvatarLarge');
    if (avatarElement) {
        avatarElement.textContent = user.avatar || user.name.charAt(0).toUpperCase();
    }

    // Name
    const nameElement = document.getElementById('profileUserName');
    if (nameElement) {
        nameElement.textContent = user.name;
    }

    // Status
    const statusDot = document.getElementById('profileStatusDot');
    const statusText = document.getElementById('profileStatusText');
    if (statusDot && statusText) {
        if (user.is_online) {
            statusDot.classList.add('online');
            statusText.textContent = 'Online';
        } else {
            statusDot.classList.remove('online');
            statusText.textContent = 'Offline';
        }
    }

    // Email
    const emailElement = document.getElementById('profileEmail');
    if (emailElement && user.email) {
        emailElement.textContent = user.email;
    }

    // Nickname
    const nicknameContainer = document.getElementById('profileNicknameContainer');
    const nicknameElement = document.getElementById('profileNickname');
    if (user.nickname) {
        nicknameContainer.style.display = 'block';
        nicknameElement.textContent = '@' + user.nickname;
    } else {
        nicknameContainer.style.display = 'none';
    }

    // Role (if available)
    const roleContainer = document.getElementById('profileRoleContainer');
    if (user.role) {
        roleContainer.style.display = 'block';
        document.getElementById('profileRole').textContent = user.role;
    } else {
        roleContainer.style.display = 'none';
    }
}

function closeUserProfile() {
    const modal = document.getElementById('userProfileModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// ===== TELEGRAM SETTINGS =====

async function showTelegramSettings() {
    const modal = document.getElementById('telegramSettingsModal');
    if (!modal) return;

    modal.style.display = 'flex';
    await loadTelegramSettings();
}

async function loadTelegramSettings() {
    const loading = document.getElementById('telegramLoading');
    const currentSettings = document.getElementById('telegramCurrentSettings');
    const editForm = document.getElementById('telegramEditForm');

    // Show loading
    loading.style.display = 'block';
    currentSettings.style.display = 'none';
    editForm.style.display = 'none';

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/settings`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        loading.style.display = 'none';

        if (data.success && data.data) {
            const settings = data.data;

            // Check if Telegram is enabled
            if (settings.telegram_enabled && settings.telegram_bot_username) {
                // Show current settings
                document.getElementById('currentBotUsername').textContent = settings.telegram_bot_username;
                document.getElementById('currentWebhookUrl').textContent = settings.telegram_webhook_url;
                currentSettings.style.display = 'block';
            } else {
                // Show edit form for initial setup
                editForm.style.display = 'block';
            }
        } else {
            // Show edit form if no settings
            editForm.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading Telegram settings:', error);
        loading.style.display = 'none';
        editForm.style.display = 'block';
        showNotification('Xatolik yuz berdi', 'error');
    }
}

function showTelegramEditForm() {
    document.getElementById('telegramCurrentSettings').style.display = 'none';
    document.getElementById('telegramEditForm').style.display = 'block';
    document.getElementById('cancelTelegramBtn').style.display = 'block';

    // Load current values if available
    const currentToken = ''; // Can't retrieve encrypted token
    const currentUsername = document.getElementById('currentBotUsername').textContent;

    document.getElementById('telegramBotUsername').value = currentUsername || '';
}

function cancelTelegramEdit() {
    document.getElementById('telegramEditForm').style.display = 'none';
    document.getElementById('telegramCurrentSettings').style.display = 'block';
    document.getElementById('cancelTelegramBtn').style.display = 'none';

    // Clear form
    document.getElementById('telegramBotToken').value = '';
    document.getElementById('telegramBotUsername').value = '';
}

async function saveTelegramSettings() {
    const token = document.getElementById('telegramBotToken').value.trim();
    const username = document.getElementById('telegramBotUsername').value.trim();

    if (!token) {
        showNotification('Bot token kiriting', 'error');
        return;
    }

    if (!username) {
        showNotification('Bot username kiriting', 'error');
        return;
    }

    if (!username.startsWith('@')) {
        showNotification('Bot username @ bilan boshlanishi kerak', 'error');
        return;
    }

    const loading = document.getElementById('telegramLoading');
    const editForm = document.getElementById('telegramEditForm');

    loading.style.display = 'block';
    editForm.style.display = 'none';

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/settings/telegram`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                telegram_bot_token: token,
                telegram_bot_username: username
            })
        });

        const data = await response.json();

        loading.style.display = 'none';

        if (data.success) {
            showNotification('Sozlamalar saqlandi', 'success');

            // Update UI with new settings
            document.getElementById('currentBotUsername').textContent = data.data.telegram_bot_username;
            document.getElementById('currentWebhookUrl').textContent = data.data.telegram_webhook_url;
            document.getElementById('telegramStatusDesc').textContent = 'Bot faol';

            // Show current settings view
            document.getElementById('telegramCurrentSettings').style.display = 'block';
            document.getElementById('cancelTelegramBtn').style.display = 'none';

            // Clear form
            document.getElementById('telegramBotToken').value = '';
            document.getElementById('telegramBotUsername').value = '';
        } else {
            showNotification(data.message || 'Xatolik yuz berdi', 'error');
            editForm.style.display = 'block';
        }
    } catch (error) {
        console.error('Error saving Telegram settings:', error);
        loading.style.display = 'none';
        editForm.style.display = 'block';
        showNotification('Xatolik yuz berdi', 'error');
    }
}

async function testTelegramBot() {
    const loading = document.getElementById('telegramLoading');
    const currentSettings = document.getElementById('telegramCurrentSettings');

    loading.style.display = 'block';
    currentSettings.style.display = 'none';

    try {
        const response = await fetch(`${window.CHAT_BASE_URL}/api/chat/settings/telegram/test`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CHAT_CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        loading.style.display = 'none';
        currentSettings.style.display = 'block';

        if (data.success) {
            showNotification('Bot faol va ishlayapti!', 'success');
        } else {
            showNotification(data.message || 'Bot bilan bog\'lanishda xatolik', 'error');
        }
    } catch (error) {
        console.error('Error testing Telegram bot:', error);
        loading.style.display = 'none';
        currentSettings.style.display = 'block';
        showNotification('Xatolik yuz berdi', 'error');
    }
}

function closeTelegramSettings() {
    const modal = document.getElementById('telegramSettingsModal');
    if (modal) {
        modal.style.display = 'none';
    }

    // Reset views
    document.getElementById('telegramLoading').style.display = 'none';
    document.getElementById('cancelTelegramBtn').style.display = 'none';

    // Clear form
    document.getElementById('telegramBotToken').value = '';
    document.getElementById('telegramBotUsername').value = '';
}

// ===== UTILITIES =====

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

console.log('Chat popup JS loaded successfully');

<!-- Floating Chat Button -->
<div id="chatFloatingBtn" class="chat-floating-btn" onclick="toggleChatPopup()">
    <i class="fas fa-comments"></i>
    <span class="chat-unread-badge" id="chatUnreadBadge" style="display: none;">0</span>
</div>

<!-- Chat Popup Modal -->
<div id="chatPopup" class="chat-popup" style="display: none;">
    <!-- Chat Header -->
    <div class="chat-popup-header">
        <div class="chat-header-title">
            <i class="fas fa-comments me-2"></i>
            <span id="chatHeaderTitle">Xabarlar</span>
        </div>
        <div class="chat-header-actions">
            <button class="chat-header-btn" onclick="minimizeChatPopup()" title="Minimize">
                <i class="fas fa-minus"></i>
            </button>
            <button class="chat-header-btn" onclick="closeChatPopup()" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Chat Body -->
    <div class="chat-popup-body">
        <!-- Conversations View (Default) -->
        <div id="chatConversationsView" class="chat-view active">
            <!-- Search Bar -->
            <div class="chat-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="chatConversationSearchInput" placeholder="Suhbatlarni qidirish..." onkeyup="searchConversations()">
            </div>

            <!-- Conversations List -->
            <div id="chatConversationsList" class="chat-users-list">
                <div class="chat-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Yuklanmoqda...</p>
                </div>
            </div>
        </div>

        <!-- Contacts View (All Users) -->
        <div id="chatContactsView" class="chat-view">
            <!-- Search Bar -->
            <div class="chat-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="chatContactSearchInput" placeholder="Kontakt qidirish..." onkeyup="searchContacts()">
            </div>

            <!-- Contacts List -->
            <div id="chatContactsList" class="chat-users-list">
                <div class="chat-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Yuklanmoqda...</p>
                </div>
            </div>
        </div>

        <!-- Chat Messages View -->
        <div id="chatMessagesView" class="chat-view">
            <!-- Back Button -->
            <div class="chat-back-header">
                <button class="chat-back-btn" onclick="backToUsersList()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="chat-user-info" onclick="showUserProfile()">
                    <div class="chat-avatar" id="chatCurrentAvatar">A</div>
                    <div>
                        <div class="chat-user-name" id="chatCurrentUserName">User</div>
                        <div class="chat-user-status" id="chatCurrentUserStatus">Online</div>
                    </div>
                </div>
                <div class="chat-header-actions">
                    <button class="chat-header-btn" onclick="toggleConversationMenu()" title="Menu">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="chat-conversation-menu" id="conversationMenu" style="display: none;">
                        <button onclick="deleteCurrentConversation()" class="chat-conversation-menu-item danger">
                            <i class="fas fa-trash"></i> Suhbatni o'chirish
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="chatMessagesContainer" class="chat-messages-container">
                <div class="chat-empty-state">
                    <i class="fas fa-comments"></i>
                    <p>Hali xabarlar yo'q</p>
                </div>
            </div>

            <!-- Request Pending Notice (for students) -->
            <div id="chatRequestPending" class="chat-request-notice" style="display: none;">
                <i class="fas fa-clock"></i>
                <p>Suhbat so'rovi kutilmoqda</p>
                <small>Foydalanuvchi sizning so'rovingizni qabul qilguncha xabar yubora olmaysiz</small>
            </div>

            <!-- Request Actions (for non-students receiving requests) -->
            <div id="chatRequestActions" class="chat-request-actions" style="display: none;">
                <div class="chat-request-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Talaba sizga xabar yubormoqchi</span>
                </div>
                <div class="chat-request-buttons">
                    <button class="chat-request-accept" onclick="acceptChatRequest()">
                        <i class="fas fa-check"></i> Qabul qilish
                    </button>
                    <button class="chat-request-reject" onclick="rejectChatRequest()">
                        <i class="fas fa-times"></i> Rad etish
                    </button>
                </div>
            </div>

            <!-- Message Input -->
            <div class="chat-input-area" id="chatInputArea">
                <button class="chat-input-btn" onclick="document.getElementById('chatFileInput').click()" title="Fayl yuklash">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="chatFileInput" style="display: none" onchange="handleChatFileUpload(this.files[0])" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">

                <input type="text" id="chatMessageInput" class="chat-input" placeholder="Xabar yozing..." onkeypress="handleChatKeyPress(event)">

                <button class="chat-send-btn" onclick="sendChatMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="chat-popup-bottom-nav">
        <button class="chat-nav-btn active" data-view="conversations" onclick="showChatView('conversations')" title="Chatlar">
            <i class="fas fa-comments"></i>
            <span>Chatlar</span>
        </button>
        <button class="chat-nav-btn" data-view="contacts" onclick="showChatView('contacts')" title="Kontaktlar">
            <i class="fas fa-address-book"></i>
            <span>Kontaktlar</span>
        </button>
        <button class="chat-nav-btn" data-view="settings" onclick="showChatSettings()" title="Sozlamalar">
            <i class="fas fa-cog"></i>
            <span>Sozlamalar</span>
        </button>
    </div>
</div>

<!-- Settings Modal -->
<div id="chatSettingsModal" class="chat-settings-modal" style="display: none;">
    <div class="chat-settings-content">
        <div class="chat-settings-header">
            <h5><i class="fas fa-cog me-2"></i>Sozlamalar</h5>
            <button class="chat-settings-close" onclick="closeChatSettings()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-settings-body">
            <!-- Profile Section -->
            <div class="chat-settings-section">
                <div class="chat-settings-item" onclick="showNicknameModal()">
                    <div class="chat-settings-icon">
                        <i class="fas fa-at"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Nickname</div>
                        <div class="chat-settings-desc" id="currentNickname">@{{ auth()->user()->nickname ?? 'Belgilanmagan' }}</div>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>

                <div class="chat-settings-item">
                    <div class="chat-settings-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Profil</div>
                        <div class="chat-settings-desc">{{ auth()->user()->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Notifications Section -->
            <div class="chat-settings-section">
                <div class="chat-settings-section-title">Bildirishnomalar</div>

                <div class="chat-settings-item">
                    <div class="chat-settings-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Tovush</div>
                        <div class="chat-settings-desc">Yangi xabar tovushi</div>
                    </div>
                    <label class="chat-toggle">
                        <input type="checkbox" id="notificationSound" checked onchange="toggleNotificationSound(this.checked)">
                        <span class="chat-toggle-slider"></span>
                    </label>
                </div>

                <div class="chat-settings-item">
                    <div class="chat-settings-icon">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Desktop bildirishnomalar</div>
                        <div class="chat-settings-desc">Brauzer orqali xabar</div>
                    </div>
                    <label class="chat-toggle">
                        <input type="checkbox" id="desktopNotifications" onchange="toggleDesktopNotifications(this.checked)">
                        <span class="chat-toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Appearance Section -->
            <div class="chat-settings-section">
                <div class="chat-settings-section-title">Ko'rinish</div>

                <div class="chat-settings-item" onclick="showThemeModal()">
                    <div class="chat-settings-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Mavzu</div>
                        <div class="chat-settings-desc" id="currentThemeDesc">Yorug' rejim</div>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            <!-- Privacy Section -->
            <div class="chat-settings-section">
                <div class="chat-settings-section-title">Maxfiylik</div>

                <div class="chat-settings-item">
                    <div class="chat-settings-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Online holat</div>
                        <div class="chat-settings-desc">Boshqalarga online ekanligingizni ko'rsatish</div>
                    </div>
                    <label class="chat-toggle">
                        <input type="checkbox" id="showOnlineStatus" checked onchange="toggleOnlineStatus(this.checked)">
                        <span class="chat-toggle-slider"></span>
                    </label>
                </div>

                <div class="chat-settings-item">
                    <div class="chat-settings-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">O'qilganlik</div>
                        <div class="chat-settings-desc">Xabarlarni o'qiganimni ko'rsatish</div>
                    </div>
                    <label class="chat-toggle">
                        <input type="checkbox" id="showReadReceipts" checked>
                        <span class="chat-toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Telegram Section (Admin Only) -->
            @if(auth()->user()->hasAnyRole(['SuperAdmin', 'ChatAdmin']))
            <div class="chat-settings-section">
                <div class="chat-settings-section-title">Telegram Bot</div>

                <div class="chat-settings-item" onclick="showTelegramSettings()">
                    <div class="chat-settings-icon">
                        <i class="fab fa-telegram"></i>
                    </div>
                    <div class="chat-settings-text">
                        <div class="chat-settings-title">Telegram sozlamalari</div>
                        <div class="chat-settings-desc" id="telegramStatusDesc">Bot sozlamalarini boshqarish</div>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Nickname Modal -->
<div id="nicknameModal" class="chat-settings-modal" style="display: none;">
    <div class="chat-settings-content" style="max-width: 400px;">
        <div class="chat-settings-header">
            <h5><i class="fas fa-at me-2"></i>Nickname o'zgartirish</h5>
            <button class="chat-settings-close" onclick="closeNicknameModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-settings-body">
            <div class="chat-input-group">
                <label>Nickname</label>
                <input type="text" id="nicknameInput" class="chat-settings-input" placeholder="@nickname" value="{{ auth()->user()->nickname }}">
                <small class="chat-input-help">Faqat harflar, raqamlar va pastki chiziq (_)</small>
            </div>
            <button class="chat-settings-save-btn" onclick="saveNickname()">
                <i class="fas fa-save me-2"></i>Saqlash
            </button>
        </div>
    </div>
</div>

<!-- Theme Modal -->
<div id="themeModal" class="chat-settings-modal" style="display: none;">
    <div class="chat-settings-content" style="max-width: 400px;">
        <div class="chat-settings-header">
            <h5><i class="fas fa-palette me-2"></i>Mavzu tanlash</h5>
            <button class="chat-settings-close" onclick="closeThemeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-settings-body">
            <div class="theme-options">
                <div class="theme-option" data-theme="light" onclick="selectTheme('light')">
                    <div class="theme-option-preview theme-light">
                        <div class="theme-preview-header"></div>
                        <div class="theme-preview-body"></div>
                    </div>
                    <div class="theme-option-info">
                        <div class="theme-option-title">
                            <i class="fas fa-sun"></i> Yorug' rejim
                        </div>
                        <div class="theme-option-check" id="themeCheckLight">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="theme-option" data-theme="dark" onclick="selectTheme('dark')">
                    <div class="theme-option-preview theme-dark">
                        <div class="theme-preview-header"></div>
                        <div class="theme-preview-body"></div>
                    </div>
                    <div class="theme-option-info">
                        <div class="theme-option-title">
                            <i class="fas fa-moon"></i> Qorong'i rejim
                        </div>
                        <div class="theme-option-check" id="themeCheckDark">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="theme-option" data-theme="auto" onclick="selectTheme('auto')">
                    <div class="theme-option-preview theme-auto">
                        <div class="theme-preview-header"></div>
                        <div class="theme-preview-body"></div>
                    </div>
                    <div class="theme-option-info">
                        <div class="theme-option-title">
                            <i class="fas fa-adjust"></i> Avtomatik
                        </div>
                        <div class="theme-option-check" id="themeCheckAuto">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Profile Modal -->
<div id="userProfileModal" class="chat-settings-modal" style="display: none;">
    <div class="chat-settings-content" style="max-width: 400px;">
        <div class="chat-settings-header">
            <h5><i class="fas fa-user me-2"></i>Foydalanuvchi ma'lumotlari</h5>
            <button class="chat-settings-close" onclick="closeUserProfile()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-settings-body">
            <!-- Profile Photo -->
            <div class="user-profile-photo-container">
                <div class="user-profile-photo" id="profilePhotoLarge">
                    <div class="user-profile-avatar" id="profileAvatarLarge">A</div>
                </div>
            </div>

            <!-- User Info -->
            <div class="user-profile-info">
                <div class="user-profile-name" id="profileUserName">User Name</div>
                <div class="user-profile-status-indicator">
                    <span class="user-profile-status-dot" id="profileStatusDot"></span>
                    <span class="user-profile-status-text" id="profileStatusText">Online</span>
                </div>
            </div>

            <!-- Details -->
            <div class="user-profile-details">
                <div class="user-profile-detail-item">
                    <div class="user-profile-detail-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="user-profile-detail-text">
                        <div class="user-profile-detail-label">Email</div>
                        <div class="user-profile-detail-value" id="profileEmail">user@example.com</div>
                    </div>
                </div>

                <div class="user-profile-detail-item" id="profileNicknameContainer" style="display: none;">
                    <div class="user-profile-detail-icon">
                        <i class="fas fa-at"></i>
                    </div>
                    <div class="user-profile-detail-text">
                        <div class="user-profile-detail-label">Nickname</div>
                        <div class="user-profile-detail-value" id="profileNickname">@username</div>
                    </div>
                </div>

                <div class="user-profile-detail-item" id="profileRoleContainer" style="display: none;">
                    <div class="user-profile-detail-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div class="user-profile-detail-text">
                        <div class="user-profile-detail-label">Rol</div>
                        <div class="user-profile-detail-value" id="profileRole">User</div>
                    </div>
                </div>
            </div>

            <!-- Block Button -->
            <div class="user-profile-actions">
                <button class="user-profile-block-btn" onclick="toggleBlockUser()" id="profileBlockBtn">
                    <i class="fas fa-ban"></i> Bloklash
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Telegram Settings Modal (Admin Only) -->
@if(auth()->user()->hasAnyRole(['SuperAdmin', 'ChatAdmin']))
<div id="telegramSettingsModal" class="chat-settings-modal" style="display: none;">
    <div class="chat-settings-content" style="max-width: 500px;">
        <div class="chat-settings-header">
            <h5><i class="fab fa-telegram me-2"></i>Telegram Bot sozlamalari</h5>
            <button class="chat-settings-close" onclick="closeTelegramSettings()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-settings-body">
            <!-- Current Settings Display -->
            <div class="telegram-settings-current" id="telegramCurrentSettings" style="display: none;">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Telegram bot faol</strong>
                </div>

                <div class="telegram-info-item">
                    <label><i class="fab fa-telegram"></i> Bot Username:</label>
                    <div class="telegram-info-value" id="currentBotUsername">@academysam_bot</div>
                </div>

                <div class="telegram-info-item">
                    <label><i class="fas fa-link"></i> Webhook URL:</label>
                    <div class="telegram-info-value" id="currentWebhookUrl"></div>
                </div>

                <div class="telegram-actions">
                    <button class="btn btn-primary" onclick="testTelegramBot()">
                        <i class="fas fa-vial"></i> Bot testlash
                    </button>
                    <button class="btn btn-secondary" onclick="showTelegramEditForm()">
                        <i class="fas fa-edit"></i> Sozlamalarni o'zgartirish
                    </button>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="telegram-settings-form" id="telegramEditForm">
                <div class="form-group">
                    <label for="telegramBotToken"><i class="fas fa-key"></i> Bot Token:</label>
                    <input type="text" id="telegramBotToken" class="chat-settings-input" placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                    <small class="form-text">@BotFather dan olingan token</small>
                </div>

                <div class="form-group">
                    <label for="telegramBotUsername"><i class="fab fa-telegram"></i> Bot Username:</label>
                    <input type="text" id="telegramBotUsername" class="chat-settings-input" placeholder="@yourbot">
                    <small class="form-text">Bot username (@ bilan)</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Webhook URL avtomatik yaratiladi</strong>
                    <p>Domeningiz asosida webhook URL avtomatik generatsiya qilinadi va Telegram serveriga o'rnatiladi.</p>
                </div>

                <div class="telegram-form-actions">
                    <button class="chat-settings-save-btn" onclick="saveTelegramSettings()">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                    <button class="chat-settings-cancel-btn" onclick="cancelTelegramEdit()" id="cancelTelegramBtn" style="display: none;">
                        <i class="fas fa-times"></i> Bekor qilish
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="telegram-loading" id="telegramLoading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Yuklanmoqda...</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Include Chat Popup CSS -->
<link rel="stylesheet" href="{{ asset('css/chat-popup.css') }}">

<!-- Include Chat Popup JS -->
<script>
    // Pass CSRF token and base URL to JavaScript
    window.CHAT_CSRF_TOKEN = '{{ csrf_token() }}';
    window.CHAT_BASE_URL = '{{ url('/') }}';
    window.CHAT_AUTH_USER_ID = {{ auth()->id() }};
</script>
<script src="{{ asset('js/chat-popup.js') }}"></script>

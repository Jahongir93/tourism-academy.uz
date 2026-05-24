@extends('layouts.dashboard-new')

@section('title', 'Xabarlar')
@section('page-title', 'Xabarlar')

@section('styles')
<style>
/* ── Layout ───────────────────────────────────────── */
.chat-page { height: calc(100vh - 110px); display:flex; background:var(--c-bg); border-radius:12px; overflow:hidden; box-shadow:0 2px 16px rgba(0,0,0,.08); }

/* ── Sidebar ──────────────────────────────────────── */
.chat-sidebar { width:320px; flex-shrink:0; display:flex; flex-direction:column; border-right:1px solid var(--c-border); background:var(--c-bg); }
.sidebar-hd { padding:.875rem 1rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--c-border); background:var(--c-bg); }
.sidebar-hd h6 { margin:0; font-weight:700; font-size:.95rem; color:var(--c-text); }
.sidebar-actions { display:flex; gap:6px; }
.sidebar-btn { width:32px; height:32px; border-radius:8px; border:1px solid var(--c-border); background:var(--c-bg); color:var(--c-text-2); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:all .15s; font-size:.8rem; }
.sidebar-btn:hover { background:var(--c-teal); color:#fff; border-color:var(--c-teal); }

/* Quick contact chips */
.quick-contacts { padding:.5rem .75rem; display:flex; gap:.375rem; flex-wrap:wrap; border-bottom:1px solid var(--c-border); }
.quick-chip { padding:.2rem .55rem; border-radius:20px; font-size:.7rem; font-weight:600; cursor:pointer; border:1.5px solid; transition:all .15s; }
.quick-chip:hover { filter:brightness(.92); }

/* Search */
.sidebar-search { padding:.625rem .75rem; border-bottom:1px solid var(--c-border); position:relative; }
.sidebar-search .form-control { background:var(--c-bg-2,#f3f4f6); border:1px solid var(--c-border); border-radius:8px; font-size:.83rem; padding:.4rem .75rem .4rem 2rem; color:var(--c-text); }
.sidebar-search .form-control:focus { border-color:var(--c-teal); box-shadow:0 0 0 2px rgba(20,184,166,.15); background:#fff; }
.search-icon { position:absolute; left:1.125rem; top:50%; transform:translateY(-50%); color:var(--c-text-3); font-size:.75rem; pointer-events:none; }
.search-dropdown { position:absolute; top:calc(100% - 1px); left:.75rem; right:.75rem; background:var(--c-bg); border:1px solid var(--c-border); border-radius:0 0 10px 10px; box-shadow:0 6px 20px rgba(0,0,0,.12); max-height:280px; overflow-y:auto; z-index:200; display:none; }
.search-dropdown.show { display:block; }

/* Conversation list */
.conv-list { flex:1; overflow-y:auto; }
.conv-item { display:flex; align-items:center; padding:.75rem 1rem; cursor:pointer; border-bottom:1px solid var(--c-border); transition:background .12s; gap:.75rem; position:relative; }
.conv-item:hover { background:rgba(20,184,166,.05); }
.conv-item.active { background:rgba(20,184,166,.1); border-left:3px solid var(--c-teal); }
.conv-item.active .conv-name { color:var(--c-teal); }
.conv-avatar { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; flex-shrink:0; position:relative; color:#fff; }
.conv-avatar.online::after { content:''; position:absolute; bottom:1px; right:1px; width:11px; height:11px; background:#10b981; border:2px solid var(--c-bg); border-radius:50%; }
.conv-body { flex:1; min-width:0; }
.conv-name { font-size:.875rem; font-weight:600; color:var(--c-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.conv-preview { font-size:.775rem; color:var(--c-text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:1px; }
.conv-meta { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
.conv-time { font-size:.7rem; color:var(--c-text-3); }
.conv-badge { background:var(--c-teal); color:#fff; font-size:.65rem; font-weight:700; padding:2px 6px; border-radius:10px; min-width:18px; text-align:center; }

/* ── Main area ────────────────────────────────────── */
.chat-main { flex:1; display:flex; flex-direction:column; min-width:0; background:var(--c-bg); }
.chat-empty { flex:1; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:1rem; }
.chat-empty-icon { width:80px; height:80px; background:rgba(20,184,166,.1); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; color:var(--c-teal); }
.chat-empty h5 { margin:0; color:var(--c-text); font-weight:600; }
.chat-empty p { margin:0; color:var(--c-text-3); font-size:.85rem; }

/* Chat header */
.chat-hd { padding:.75rem 1rem; display:flex; align-items:center; gap:.75rem; border-bottom:1px solid var(--c-border); background:var(--c-bg); flex-shrink:0; }
.chat-hd-info { flex:1; min-width:0; }
.chat-hd-name { font-size:.9rem; font-weight:700; color:var(--c-text); margin:0; }
.chat-hd-status { font-size:.73rem; color:var(--c-text-3); }
.chat-hd-actions { display:flex; gap:6px; }

/* Messages */
.messages-wrap { flex:1; overflow-y:auto; padding:1rem; background:var(--c-bg); display:flex; flex-direction:column; gap:.375rem; }
.date-sep { text-align:center; margin:.5rem 0; }
.date-sep span { background:rgba(100,116,139,.12); color:var(--c-text-3); font-size:.7rem; padding:3px 10px; border-radius:10px; }
.msg-row { display:flex; }
.msg-row.own { justify-content:flex-end; }
.msg-row.own .msg-bubble { background:rgba(20,184,166,.15); border-bottom-right-radius:4px; }
.msg-row.other .msg-bubble { background:var(--c-bg-2,#f3f4f6); border-bottom-left-radius:4px; }
.msg-row.other .msg-sender { font-size:.7rem; color:var(--c-teal); font-weight:600; margin-bottom:2px; }
.msg-bubble { max-width:68%; padding:.5rem .75rem; border-radius:12px; word-break:break-word; }
.msg-text { font-size:.875rem; color:var(--c-text); line-height:1.4; }
.msg-footer { display:flex; align-items:center; justify-content:flex-end; gap:4px; margin-top:3px; }
.msg-time { font-size:.65rem; color:var(--c-text-3); }
.msg-img { max-width:240px; border-radius:8px; cursor:pointer; display:block; margin-bottom:4px; }
.msg-file { display:flex; align-items:center; gap:.5rem; padding:.4rem .6rem; background:rgba(100,116,139,.1); border-radius:8px; margin-bottom:4px; font-size:.8rem; color:var(--c-text); text-decoration:none; }
.msg-file:hover { background:rgba(100,116,139,.18); color:var(--c-text); }
.msg-voice { display:flex; align-items:center; gap:.5rem; }
.msg-voice audio { max-width:200px; height:32px; }

/* Input area */
.chat-input-wrap { padding:.625rem .875rem; border-top:1px solid var(--c-border); background:var(--c-bg); flex-shrink:0; }
.input-row { display:flex; align-items:flex-end; gap:.5rem; background:var(--c-bg-2,#f3f4f6); border:1px solid var(--c-border); border-radius:12px; padding:.375rem .5rem; transition:border-color .15s; }
.input-row:focus-within { border-color:var(--c-teal); background:#fff; }
.input-side-btn { width:34px; height:34px; border-radius:8px; border:none; background:transparent; color:var(--c-text-3); display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; transition:all .15s; font-size:.85rem; }
.input-side-btn:hover { color:var(--c-teal); background:rgba(20,184,166,.1); }
.msg-textarea { flex:1; border:none; background:transparent; resize:none; outline:none; font-size:.875rem; color:var(--c-text); max-height:120px; min-height:34px; line-height:1.45; padding:.25rem 0; }
.send-btn { width:36px; height:36px; border-radius:9px; border:none; background:var(--c-teal); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; transition:all .15s; font-size:.85rem; }
.send-btn:hover { filter:brightness(.9); }
.send-btn:disabled { opacity:.5; cursor:not-allowed; }

/* Emoji picker */
.emoji-popup { position:absolute; bottom:calc(100% + 8px); left:0; background:var(--c-bg); border:1px solid var(--c-border); border-radius:12px; box-shadow:0 6px 24px rgba(0,0,0,.14); padding:10px; z-index:500; display:none; width:300px; }
.emoji-popup.show { display:block; }
.emoji-grid { display:grid; grid-template-columns:repeat(8,1fr); gap:2px; max-height:180px; overflow-y:auto; }
.emoji-btn { font-size:1.3rem; cursor:pointer; padding:3px; border-radius:6px; text-align:center; transition:background .1s; }
.emoji-btn:hover { background:rgba(100,116,139,.12); }

/* Voice recording */
.recording-bar { display:none; align-items:center; gap:.75rem; padding:.4rem .875rem; background:rgba(239,68,68,.06); border-top:1px solid rgba(239,68,68,.15); font-size:.82rem; }
.recording-bar.show { display:flex; }
.rec-dot { width:8px; height:8px; background:#ef4444; border-radius:50%; animation:recblink .8s infinite; }
@keyframes recblink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* Scrollbar */
.conv-list::-webkit-scrollbar, .messages-wrap::-webkit-scrollbar { width:4px; }
.conv-list::-webkit-scrollbar-thumb, .messages-wrap::-webkit-scrollbar-thumb { background:var(--c-border); border-radius:4px; }

/* Mobile */
@media(max-width:767px){
    .chat-sidebar { position:absolute; width:100%; height:100%; z-index:10; transform:translateX(0); transition:transform .25s; }
    .chat-sidebar.slide-out { transform:translateX(-100%); }
    .chat-main { width:100%; }
    .back-btn { display:flex !important; }
}
.back-btn { display:none; }

/* Search result items */
.sr-item { display:flex; align-items:center; gap:.625rem; padding:.5rem .875rem; cursor:pointer; transition:background .1s; }
.sr-item:hover { background:rgba(20,184,166,.06); }
.sr-name { font-size:.82rem; font-weight:600; color:var(--c-text); }
.sr-sub { font-size:.72rem; color:var(--c-text-3); }
</style>
@endsection

@section('content')
<div class="chat-page" id="chatPage">

    {{-- ═══ SIDEBAR ═══════════════════════════════════════════ --}}
    <div class="chat-sidebar" id="chatSidebar">

        {{-- Header --}}
        <div class="sidebar-hd">
            <h6><i class="fas fa-comments me-2" style="color:var(--c-teal)"></i>Xabarlar</h6>
            <div class="sidebar-actions">
                <button class="sidebar-btn" onclick="Chat.nickname()" title="Nikname">
                    <i class="fas fa-at"></i>
                </button>
                <button class="sidebar-btn" onclick="Chat.newModal()" title="Yangi suhbat">
                    <i class="fas fa-pen-to-square"></i>
                </button>
            </div>
        </div>

        {{-- Quick contact chips --}}
        <div class="quick-contacts">
            <button class="quick-chip" style="color:var(--c-rose);border-color:var(--c-rose)" onclick="Chat.contact('admin')">
                <i class="fas fa-user-shield me-1"></i>Admin
            </button>
            <button class="quick-chip" style="color:var(--c-sky);border-color:var(--c-sky)" onclick="Chat.contact('dean')">
                <i class="fas fa-user-tie me-1"></i>Dekan
            </button>
            <button class="quick-chip" style="color:var(--c-violet);border-color:var(--c-violet)" onclick="Chat.contact('prorector')">
                <i class="fas fa-user-graduate me-1"></i>Prorektor
            </button>
        </div>

        {{-- Search --}}
        <div class="sidebar-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="form-control" id="sideSearch" placeholder="Qidirish...">
            <div class="search-dropdown" id="sideDropdown"></div>
        </div>

        {{-- Conversation list --}}
        <div class="conv-list" id="convList">
            <div class="text-center py-5 text-muted" style="font-size:.82rem">
                <div class="spinner-border spinner-border-sm mb-2" style="color:var(--c-teal)"></div>
                <div>Yuklanmoqda...</div>
            </div>
        </div>
    </div>

    {{-- ═══ MAIN AREA ══════════════════════════════════════════ --}}
    <div class="chat-main" id="chatMain">

        {{-- Empty state --}}
        <div class="chat-empty" id="chatEmpty">
            <div class="chat-empty-icon"><i class="fas fa-comments"></i></div>
            <h5>Xush kelibsiz!</h5>
            <p>Suhbatni boshlash uchun chap tarafdagi foydalanuvchini tanlang</p>
        </div>

        {{-- Active conversation --}}
        <div id="chatActive" style="display:none;flex:1;display:flex;flex-direction:column;height:100%;overflow:hidden;">

            {{-- Chat header --}}
            <div class="chat-hd">
                <button class="back-btn input-side-btn" onclick="Chat.backToList()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="conv-avatar" id="hdAvatar" style="width:38px;height:38px;font-size:.9rem;"></div>
                <div class="chat-hd-info">
                    <div class="chat-hd-name" id="hdName">—</div>
                    <div class="chat-hd-status" id="hdStatus">Yuklanmoqda...</div>
                </div>
                <div class="chat-hd-actions">
                    <button class="sidebar-btn" title="Qo'shimcha" onclick="Chat.moreMenu()">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="messages-wrap" id="messagesWrap"></div>

            {{-- Input --}}
            <div class="chat-input-wrap" style="position:relative">
                {{-- Emoji popup --}}
                <div class="emoji-popup" id="emojiPopup">
                    <div class="emoji-grid" id="emojiGrid"></div>
                </div>

                {{-- Recording bar --}}
                <div class="recording-bar" id="recBar">
                    <div class="rec-dot"></div>
                    <span style="color:#ef4444;font-weight:600">Yozilmoqda</span>
                    <span id="recTime" style="color:var(--c-text-3)">0:00</span>
                    <span style="flex:1"></span>
                    <button class="sidebar-btn" onclick="Chat.cancelRec()" title="Bekor qilish"><i class="fas fa-times" style="color:#ef4444"></i></button>
                    <button class="sidebar-btn" onclick="Chat.stopRec()" title="Yuborish"><i class="fas fa-check" style="color:#10b981"></i></button>
                </div>

                <div class="input-row">
                    <button class="input-side-btn" onclick="Chat.toggleEmoji()" title="Emoji">
                        <i class="fas fa-face-smile"></i>
                    </button>
                    <input type="file" id="fileInput" style="display:none" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx" onchange="Chat.sendFile(this.files[0])">
                    <button class="input-side-btn" onclick="document.getElementById('fileInput').click()" title="Fayl">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <textarea class="msg-textarea" id="msgInput" placeholder="Xabar yozing..." rows="1"></textarea>
                    <button class="input-side-btn" id="voiceBtn" onclick="Chat.toggleRec()" title="Ovozli xabar">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button class="send-btn" id="sendBtn" onclick="Chat.send()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODALS ════════════════════════════════════════════════ --}}

{{-- New chat modal --}}
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen-to-square me-2" style="color:var(--c-teal)"></i>Yangi suhbat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom">
                    <input type="text" class="form-control" id="newSearch" placeholder="Ism, @nikname yoki ID...">
                </div>
                <div id="newUserList" style="max-height:340px;overflow-y:auto">
                    <div class="text-center py-4 text-muted" style="font-size:.83rem">
                        <i class="fas fa-users mb-2 d-block" style="font-size:1.5rem;opacity:.3"></i>
                        Qidirish uchun ism yozing
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Nickname modal --}}
<div class="modal fade" id="nicknameModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-at me-2" style="color:var(--c-teal)"></i>Nikname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;color:var(--c-text-2)">Joriy nikname</label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" class="form-control" id="curNickname" readonly value="{{ auth()->user()->nickname ?? '' }}" placeholder="Belgilanmagan">
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size:.82rem;color:var(--c-text-2)">Yangi nikname</label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" class="form-control" id="newNickname" placeholder="nikname" maxlength="50"
                               value="{{ auth()->user()->nickname ?? '' }}">
                    </div>
                    <div style="font-size:.73rem;color:var(--c-text-3);margin-top:4px">3–50 belgi: harf, raqam, _</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Bekor</button>
                <button class="btn btn-sm" style="background:var(--c-teal);color:#fff" onclick="Chat.saveNickname()">
                    <i class="fas fa-save me-1"></i>Saqlash
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Contact modal --}}
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2" style="color:var(--c-sky)"></i>Murojaat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert" style="background:rgba(20,184,166,.08);border:1px solid rgba(20,184,166,.2);color:var(--c-text);border-radius:8px;font-size:.83rem">
                    <i class="fas fa-info-circle me-2" style="color:var(--c-teal)"></i>
                    Murojaatingiz <strong id="contactTarget">—</strong>ga yuboriladi
                </div>
                <input type="hidden" id="contactRole">
                <textarea class="form-control" id="contactMsg" rows="4" placeholder="Xabaringizni yozing..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Bekor</button>
                <button class="btn btn-sm" style="background:var(--c-sky);color:#fff" onclick="Chat.sendContact()">
                    <i class="fas fa-paper-plane me-1"></i>Yuborish
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
'use strict';

/* ── Constants ─────────────────────────────────────── */
const AUTH_ID  = {{ auth()->id() }};
const CSRF     = '{{ csrf_token() }}';
const EMOJIS   = ['😀','😃','😄','😁','😊','😍','🥰','😘','🤗','🤩','🤔','😐','😏','😒','🙄','😌','😔','😴','😷','🤢','🥵','🥶','😇','🤠','🥳','😎','🤓','😕','😟','😮','😳','🥺','😢','😭','😱','😤','😡','😠','🤬','👍','👎','👌','✌️','🤞','🙏','💪','❤️','🧡','💛','💚','💙','💜','🖤','💔','🔥','⭐','✨','💯','✅','❌','🎉','🎊','🎁','🎀','🎈'];
const AVATAR_COLORS = ['#14b8a6','#8b5cf6','#f59e0b','#ef4444','#3b82f6','#10b981','#ec4899','#6366f1'];

/* ── State ─────────────────────────────────────────── */
let activeConvId   = null;   // cached conversation ID — no re-fetch on send
let activeUserId   = null;
let activeUserName = '';
let lastMsgId      = 0;      // incremental loading
let pollTimer      = null;
let mediaRec       = null;
let recTimer       = null;
let recStart       = 0;
let isRec          = false;
let audioChunks    = [];

/* ── Helpers ───────────────────────────────────────── */
function h(str){ const d=document.createElement('div'); d.textContent=String(str); return d.innerHTML; }
function avatarColor(name){ let s=0; for(let c of name||'?') s+=c.charCodeAt(0); return AVATAR_COLORS[s%AVATAR_COLORS.length]; }
function initials(name){ return (name||'?').slice(0,2).toUpperCase(); }
function fmtTime(ts){ const d=new Date(ts); return d.getHours().toString().padStart(2,'0')+':'+d.getMinutes().toString().padStart(2,'0'); }
function fmtDate(ts){
    const d=new Date(ts), t=new Date();
    const same=d.toDateString()===t.toDateString();
    if(same) return 'Bugun';
    const y=new Date(t); y.setDate(t.getDate()-1);
    if(d.toDateString()===y.toDateString()) return 'Kecha';
    return d.toLocaleDateString('uz-UZ',{day:'2-digit',month:'short'});
}
function toast(msg, ok=true){
    const el=document.createElement('div');
    el.className='alert position-fixed fade show';
    el.style.cssText=`top:20px;right:20px;z-index:9999;min-width:240px;padding:.6rem .875rem;font-size:.82rem;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.12);${ok?'background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#065f46':'background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#b91c1c'}`;
    el.innerHTML=`<i class="fas fa-${ok?'check-circle':'exclamation-circle'} me-2"></i>${h(msg)}`;
    document.body.appendChild(el);
    setTimeout(()=>el.remove(), 3000);
}
async function apiFetch(url, opts={}){
    const r = await fetch(url, { credentials:'same-origin', headers:{ 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', ...opts.headers }, ...opts });
    if(!r.ok){ const t=await r.text(); throw new Error(t); }
    return r.json();
}

/* ── Build avatar element ───────────────────────────── */
function mkAvatar(name, size=44, cls=''){
    return `<div class="conv-avatar ${cls}" style="width:${size}px;height:${size}px;font-size:${size*.38}px;background:${avatarColor(name)}">${initials(name)}</div>`;
}

/* ── Conversation list ──────────────────────────────── */
async function loadConversations(){
    try {
        const convs = await apiFetch('/chat/conversations');
        renderConvList(convs);
    } catch(e){
        document.getElementById('convList').innerHTML='<p class="text-center text-muted py-4" style="font-size:.82rem">Suhbatlar yuklanmadi</p>';
    }
}
function renderConvList(convs){
    const list = document.getElementById('convList');
    if(!convs || convs.length===0){
        list.innerHTML='<p class="text-center text-muted py-5" style="font-size:.82rem"><i class="fas fa-comments d-block mb-2" style="font-size:1.8rem;opacity:.2"></i>Hali suhbatlar yo\'q</p>';
        return;
    }
    list.innerHTML = convs.map(c=>{
        const name    = c.name || 'Foydalanuvchi';
        const raw     = c.last_message || '';
        const preview = raw.length ? h(raw).slice(0,42)+(raw.length>42?'…':'') : '<em style="opacity:.5">Xabar yo\'q</em>';
        const unread  = c.unread_count > 0 ? `<div class="conv-badge">${c.unread_count}</div>` : '';
        const time    = c.last_message_at ? fmtTime(c.last_message_at) : '';
        const online  = c.is_online ? 'online' : '';
        return `<div class="conv-item" id="conv-${c.id}" data-id="${c.id}" data-uid="${c.other_user_id||0}" data-name="${h(name)}" onclick="Chat.openConv(this)">
            ${mkAvatar(name, 44, online)}
            <div class="conv-body">
                <div class="conv-name">${h(name)}</div>
                <div class="conv-preview">${preview}</div>
            </div>
            <div class="conv-meta">
                <div class="conv-time">${time}</div>
                ${unread}
            </div>
        </div>`;
    }).join('');
}

/* ── Open conversation from list ────────────────────── */
function openConv(el){
    activeConvId   = parseInt(el.dataset.id);
    activeUserId   = parseInt(el.dataset.uid);
    activeUserName = el.dataset.name;
    activateChat(activeUserName, false);
    document.querySelectorAll('.conv-item').forEach(i=>i.classList.remove('active'));
    el.classList.add('active');
    // remove badge
    const badge = el.querySelector('.conv-badge');
    if(badge) badge.remove();
}

/* ── Open chat from user search ─────────────────────── */
async function openUserChat(userId, userName){
    activeUserId   = userId;
    activeUserName = userName;
    activateChat(userName, true);

    try {
        const data = await apiFetch('/chat/create',{ method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({user_id:userId}) });
        activeConvId = data.conversation_id;
        loadMessages(false);
        startPoll();
        // refresh sidebar
        loadConversations();
    } catch(e){
        toast('Suhbat yaratib bo\'lmadi', false);
    }
}

/* ── Activate chat panel ────────────────────────────── */
function activateChat(name, loading){
    document.getElementById('chatEmpty').style.display = 'none';
    const active = document.getElementById('chatActive');
    active.style.display = 'flex';
    document.getElementById('hdName').textContent = name;
    document.getElementById('hdStatus').textContent = loading ? 'Yuklanmoqda...' : 'Suhbat';
    document.getElementById('hdAvatar').style.background = avatarColor(name);
    document.getElementById('hdAvatar').textContent = initials(name);
    // Mobile: slide sidebar out
    if(window.innerWidth < 768){
        document.getElementById('chatSidebar').classList.add('slide-out');
    }
    if(!loading){
        loadMessages(false);
        startPoll();
    }
}

/* ── Load messages ──────────────────────────────────── */
async function loadMessages(incremental){
    if(!activeConvId) return;
    const url = incremental && lastMsgId > 0
        ? `/chat/${activeConvId}/messages?after=${lastMsgId}`
        : `/chat/${activeConvId}/messages`;
    try {
        const msgs = await apiFetch(url);
        if(incremental){
            if(msgs && msgs.length) appendMsgs(msgs);
        } else {
            lastMsgId = 0;
            renderMsgs(msgs || []);
        }
    } catch(e){ /* silent */ }
}

/* ── Render full message list ───────────────────────── */
function renderMsgs(msgs){
    const wrap = document.getElementById('messagesWrap');
    if(!msgs.length){
        wrap.innerHTML='<div class="text-center text-muted py-5" style="font-size:.82rem"><i class="fas fa-comment-dots d-block mb-2" style="font-size:2rem;opacity:.15"></i>Hali xabarlar yo\'q</div>';
        return;
    }
    let html='', lastDate='';
    msgs.forEach(msg=>{
        const day = fmtDate(msg.created_at_raw || msg.formatted_time || Date.now());
        if(day !== lastDate){ lastDate=day; html+=`<div class="date-sep"><span>${day}</span></div>`; }
        html += buildMsg(msg);
        if(msg.id > lastMsgId) lastMsgId = msg.id;
    });
    wrap.innerHTML = html;
    wrap.scrollTop = wrap.scrollHeight;
}

/* ── Append only new messages ───────────────────────── */
function appendMsgs(msgs){
    const wrap = document.getElementById('messagesWrap');
    const atBottom = wrap.scrollHeight - wrap.scrollTop - wrap.clientHeight < 60;
    const empty = wrap.querySelector('.text-muted');
    if(empty) empty.remove();
    msgs.forEach(msg=>{
        const tmp = document.createElement('div');
        tmp.innerHTML = buildMsg(msg);
        wrap.appendChild(tmp.firstElementChild);
        if(msg.id > lastMsgId) lastMsgId = msg.id;
    });
    if(atBottom) wrap.scrollTop = wrap.scrollHeight;
}

/* ── Build single message HTML ──────────────────────── */
function buildMsg(msg){
    const own  = msg.is_own || msg.user_id == AUTH_ID;
    const side = own ? 'own' : 'other';
    const time = msg.created_at || fmtTime(Date.now());

    let body = '';
    if(msg.type === 'image' && msg.file_url){
        body = `<img src="${h(msg.file_url)}" class="msg-img" onclick="window.open('${h(msg.file_url)}','_blank')">`;
    } else if(msg.type === 'file' && msg.file_url){
        const fname = msg.file_url.split('/').pop();
        body = `<a href="${h(msg.file_url)}" target="_blank" download class="msg-file"><i class="fas fa-file me-1"></i>${h(fname)}</a>`;
    } else if(msg.type === 'voice' && msg.file_url){
        body = `<div class="msg-voice"><i class="fas fa-microphone" style="color:var(--c-teal)"></i><audio controls><source src="${h(msg.file_url)}"></audio></div>`;
    } else {
        body = `<div class="msg-text">${h(msg.message||'')}</div>`;
    }

    const edited = msg.is_edited ? ' <i class="fas fa-pencil" style="font-size:.6rem;opacity:.5"></i>' : '';
    return `<div class="msg-row ${side}">
        <div class="msg-bubble">
            ${!own&&msg.user_name?`<div class="msg-sender">${h(msg.user_name)}</div>`:''}
            ${body}
            <div class="msg-footer"><span class="msg-time">${time}${edited}</span></div>
        </div>
    </div>`;
}

/* ── Send text message ──────────────────────────────── */
async function send(){
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();
    if(!text || !activeConvId) return;
    input.value = '';
    input.style.height = '';

    // Optimistic append
    appendMsgs([{id:0, is_own:true, message:text, type:'text', created_at:fmtTime(Date.now())}]);

    try {
        const msg = await apiFetch(`/chat/${activeConvId}/send`,{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({message:text})
        });
        if(msg && msg.id && msg.id > lastMsgId) lastMsgId = msg.id;
    } catch(e){ toast('Xabar yuborilmadi', false); }
}

/* ── Send file / voice ──────────────────────────────── */
async function sendUpload(formData, kind){
    if(!activeConvId){ toast('Avval suhbatni oching', false); return; }
    try {
        const r = await apiFetch(`/chat/upload-${kind}`,{ method:'POST', body:formData });
        if(r.success && r.message) appendMsgs([r.message]);
        else toast('Fayl yuborilmadi', false);
    } catch(e){ toast('Fayl yuborilmadi', false); }
    document.getElementById('fileInput').value = '';
}
function sendFile(file){
    if(!file) return;
    if(file.size > 10*1024*1024){ toast("Fayl 10MB dan kichik bo'lishi kerak", false); return; }
    const fd = new FormData();
    fd.append('file', file);
    fd.append('conversation_id', activeConvId);
    sendUpload(fd, 'file');
}

/* ── Polling: only fetch new messages ───────────────── */
function startPoll(){
    if(pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(()=>{ if(activeConvId) loadMessages(true); }, 4000);
}

/* ── Voice recording ────────────────────────────────── */
async function startRec(){
    try {
        const stream = await navigator.mediaDevices.getUserMedia({audio:true});
        mediaRec = new MediaRecorder(stream);
        audioChunks = [];
        isRec = true;
        document.getElementById('recBar').classList.add('show');
        document.getElementById('voiceBtn').innerHTML='<i class="fas fa-stop" style="color:#ef4444"></i>';
        recStart = Date.now();
        recTimer = setInterval(()=>{
            const s = Math.floor((Date.now()-recStart)/1000);
            document.getElementById('recTime').textContent = Math.floor(s/60)+':'+(s%60).toString().padStart(2,'0');
        },1000);
        mediaRec.ondataavailable = e => audioChunks.push(e.data);
        mediaRec.onstop = async ()=>{
            stream.getTracks().forEach(t=>t.stop());
            if(audioChunks.length && isRec){
                const blob = new Blob(audioChunks,{type:'audio/webm'});
                const fd = new FormData();
                fd.append('voice', blob, 'voice_'+Date.now()+'.webm');
                fd.append('conversation_id', activeConvId);
                await sendUpload(fd,'voice');
            }
        };
        mediaRec.start();
    } catch(e){ toast('Mikrofonga ruxsat berilmadi', false); }
}
function stopRec(){
    if(mediaRec && isRec){ mediaRec.stop(); }
    isRec = false;
    resetRecUI();
}
function cancelRec(){
    audioChunks = [];
    isRec = false;
    if(mediaRec) mediaRec.stop();
    resetRecUI();
}
function resetRecUI(){
    clearInterval(recTimer);
    document.getElementById('recBar').classList.remove('show');
    document.getElementById('voiceBtn').innerHTML='<i class="fas fa-microphone"></i>';
    document.getElementById('recTime').textContent='0:00';
}

/* ── Emoji ──────────────────────────────────────────── */
function buildEmoji(){
    const g = document.getElementById('emojiGrid');
    g.innerHTML = EMOJIS.map(e=>`<div class="emoji-btn" onclick="Chat.emoji('${e}')">${e}</div>`).join('');
}
function toggleEmoji(){
    const p = document.getElementById('emojiPopup');
    p.classList.toggle('show');
}
function insertEmoji(e){
    const t = document.getElementById('msgInput');
    t.value += e;
    t.focus();
    document.getElementById('emojiPopup').classList.remove('show');
}

/* ── Sidebar search (debounced) ─────────────────────── */
let _sTimer = null;
function onSideSearch(q){
    clearTimeout(_sTimer);
    const drop = document.getElementById('sideDropdown');
    if(q.length < 2){ drop.classList.remove('show'); return; }
    _sTimer = setTimeout(async ()=>{
        try {
            const data = await apiFetch('/chat/search-users?query='+encodeURIComponent(q));
            renderDropdown(drop, data.users||[], q);
        } catch(e){ drop.classList.remove('show'); }
    }, 280);
}
function renderDropdown(drop, users, q){
    if(!users.length){ drop.innerHTML='<div class="py-3 text-center text-muted" style="font-size:.8rem">Topilmadi</div>'; drop.classList.add('show'); return; }
    drop.innerHTML = users.map(u=>`
        <div class="sr-item" onclick="Chat.openUser(${u.id},'${h(u.name)}')">
            ${mkAvatar(u.name, 32)}
            <div>
                <div class="sr-name">${h(u.name)}${u.nickname?` <span style="color:var(--c-teal);font-size:.7rem">@${h(u.nickname)}</span>`:''}</div>
                ${u.roles&&u.roles.length?`<div class="sr-sub">${h(u.roles[0])}</div>`:''}
            </div>
        </div>`).join('');
    drop.classList.add('show');
}

/* ── New chat modal search ──────────────────────────── */
let _nTimer = null;
function onNewSearch(q){
    clearTimeout(_nTimer);
    const list = document.getElementById('newUserList');
    if(q.length < 2){ list.innerHTML='<div class="text-center py-4 text-muted" style="font-size:.82rem">Qidirish uchun ism yozing</div>'; return; }
    _nTimer = setTimeout(async ()=>{
        try {
            const data = await apiFetch('/chat/search-users?query='+encodeURIComponent(q));
            const users = data.users||[];
            if(!users.length){ list.innerHTML='<div class="text-center py-4 text-muted" style="font-size:.82rem">Topilmadi</div>'; return; }
            list.innerHTML = users.map(u=>`
                <div class="sr-item" onclick="Chat.openUser(${u.id},'${h(u.name)}');bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide()">
                    ${mkAvatar(u.name, 38)}
                    <div>
                        <div class="sr-name">${h(u.name)}${u.nickname?` <span style="color:var(--c-teal);font-size:.7rem">@${h(u.nickname)}</span>`:''}</div>
                        <div class="sr-sub">${u.roles&&u.roles.length?h(u.roles[0]):''} ${u.identifier?h(u.identifier):''}</div>
                    </div>
                </div>`).join('');
        } catch(e){}
    }, 280);
}

/* ── Nickname ───────────────────────────────────────── */
async function saveNickname(){
    const val = document.getElementById('newNickname').value.trim();
    if(!val){ toast('Nikname kiriting', false); return; }
    try {
        await apiFetch('/chat/update-nickname',{ method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({nickname:val}) });
        document.getElementById('curNickname').value = val;
        bootstrap.Modal.getInstance(document.getElementById('nicknameModal')).hide();
        toast('Nikname saqlandi');
    } catch(e){ toast('Xatolik yuz berdi', false); }
}

/* ── Contact role ───────────────────────────────────── */
const ROLE_NAMES = {admin:'Admin (Rahbariyat)', dean:'Dekan', prorector:'Prorektor'};
function openContact(role){
    document.getElementById('contactRole').value = role;
    document.getElementById('contactTarget').textContent = ROLE_NAMES[role]||role;
    document.getElementById('contactMsg').value = '';
    new bootstrap.Modal(document.getElementById('contactModal')).show();
}
async function sendContact(){
    const role = document.getElementById('contactRole').value;
    const msg  = document.getElementById('contactMsg').value.trim();
    if(!msg){ toast('Xabar yozing', false); return; }
    try {
        await apiFetch('/chat/contact-role',{ method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({role, message:msg}) });
        bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
        toast('Murojaatingiz yuborildi');
    } catch(e){ toast('Yuborilmadi', false); }
}

/* ── More menu (placeholder) ────────────────────────── */
function moreMenu(){
    toast('Tez orada qo\'shiladi');
}

/* ── Public Chat API ────────────────────────────────── */
window.Chat = {
    openConv,
    openUser : openUserChat,
    send,
    sendFile,
    toggleEmoji,
    emoji    : insertEmoji,
    toggleRec: ()=> isRec ? stopRec() : (activeConvId ? startRec() : toast('Avval suhbatni oching',false)),
    stopRec,
    cancelRec,
    backToList(){ document.getElementById('chatSidebar').classList.remove('slide-out'); },
    newModal(){ new bootstrap.Modal(document.getElementById('newChatModal')).show(); },
    nickname(){ new bootstrap.Modal(document.getElementById('nicknameModal')).show(); },
    contact  : openContact,
    sendContact,
    saveNickname,
    moreMenu,
};

/* ── Init ───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function(){
    buildEmoji();
    loadConversations();

    // Auto-resize textarea
    const ta = document.getElementById('msgInput');
    ta.addEventListener('input', function(){
        this.style.height = '';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    ta.addEventListener('keydown', e=>{
        if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); send(); }
    });

    // Sidebar search
    document.getElementById('sideSearch').addEventListener('input', e=>onSideSearch(e.target.value.trim()));
    document.addEventListener('click', e=>{
        if(!e.target.closest('.sidebar-search')) document.getElementById('sideDropdown').classList.remove('show');
        if(!e.target.closest('.chat-input-wrap')) document.getElementById('emojiPopup').classList.remove('show');
    });

    // New chat search
    document.getElementById('newSearch').addEventListener('input', e=>onNewSearch(e.target.value.trim()));
});
})();
</script>
@endpush

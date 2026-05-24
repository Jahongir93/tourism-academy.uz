<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'phone',
        'password',
        'hemis_id',
        'user_type',
        'otp_code',
        'otp_expires_at',
        'email_verified_at',
        'phone_verified_at',
        'employee_type',
        'status',
        'is_online',
        'last_seen_at',
        'is_profile_complete',
        'provider',
        'provider_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_online' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        if ($this->user_type === 'uzbek') {
            return $this->phone_verified_at !== null;
        }
        return $this->email_verified_at !== null;
    }

    /**
     * Generate OTP for phone verification
     */
    public function generateOTP(): string
    {
        $otp = rand(100000, 999999);
        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);
        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOTP($otp): bool
    {
        if ($this->otp_code === $otp && $this->otp_expires_at > Carbon::now()) {
            $this->update([
                'phone_verified_at' => Carbon::now(),
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Get dashboard route based on user role
     */
    public function getDashboardRoute(): string
    {
        if ($this->hasRole('SuperAdmin')) {
            return '/admin/dashboard';
        } elseif ($this->hasRole('Teacher')) {
            return '/teacher/dashboard';
        } elseif ($this->hasRole('Student')) {
            return '/student/dashboard';
        } elseif ($this->hasRole('PR')) {
            return '/pr/dashboard';
        } elseif ($this->hasRole('Marketing')) {
            return '/marketing/dashboard';
        } elseif ($this->hasRole('HR')) {
            return '/hr/dashboard';
        } elseif ($this->hasRole('ChatAdmin')) {
            return '/chat-admin/dashboard';
        }
        return '/dashboard';
    }

    /**
     * Get user's attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get today's attendance
     */
    public function todayAttendance(): HasOne
    {
        return $this->hasOne(Attendance::class)->whereDate('date', Carbon::today());
    }

    /**
     * Get user's face encoding
     */
    public function faceEncoding(): HasOne
    {
        return $this->hasOne(FaceEncoding::class);
    }

    /**
     * Check if user has registered face
     */
    public function hasRegisteredFace(): bool
    {
        return $this->faceEncoding()->exists();
    }

    /**
     * Get student relationship if user is a student
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get teacher relationship if user is a teacher
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get employee relationship if user is an employee
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get user's notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get user's chat rooms
     */
    public function chatRooms()
    {
        return $this->belongsToMany(Chat\ChatRoom::class, 'chat_room_members', 'user_id', 'room_id')
            ->withPivot(['role', 'is_muted', 'muted_until', 'last_seen_at', 'unread_count'])
            ->withTimestamps();
    }

    /**
     * Get user's chat messages
     */
    public function chatMessages()
    {
        return $this->hasMany(Chat\ChatMessage::class);
    }

    /**
     * Get display name (nickname or name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?? $this->name;
    }

    /**
     * Get @mention format
     */
    public function getMentionAttribute(): string
    {
        return '@' . ($this->nickname ?? 'user' . $this->id);
    }

    /**
     * Update online status
     */
    public function updateOnlineStatus(bool $isOnline = true): void
    {
        $this->update([
            'is_online' => $isOnline,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Check if user is currently online (active in last 5 minutes)
     */
    public function isCurrentlyOnline(): bool
    {
        if ($this->is_online && $this->last_seen_at) {
            return $this->last_seen_at->diffInMinutes(now()) < 5;
        }
        return false;
    }

    /**
     * Get user's unique identifier for chat (student_id or employee_code)
     */
    public function getChatIdentifierAttribute(): ?string
    {
        if ($this->student) {
            return $this->student->student_id;
        }
        if ($this->employee) {
            return $this->employee->employee_code;
        }
        return null;
    }

    /**
     * Find user by nickname
     */
    public static function findByNickname(string $nickname): ?self
    {
        return static::where('nickname', $nickname)->first();
    }

    /**
     * Find user by student_id or employee_code
     */
    public static function findByChatIdentifier(string $identifier): ?self
    {
        // Try to find by student_id
        $student = Student::where('student_id', $identifier)->first();
        if ($student && $student->user_id) {
            return static::find($student->user_id);
        }

        // Try to find by employee_code
        $employee = Employee::where('employee_code', $identifier)->first();
        if ($employee && $employee->user_id) {
            return static::find($employee->user_id);
        }

        return null;
    }

    /**
     * Get chat conversations
     */
    public function chatConversations()
    {
        return $this->belongsToMany(ChatConversation::class, 'chat_participants', 'user_id', 'conversation_id')
            ->withPivot(['last_seen_at', 'unread_count', 'is_admin'])
            ->withTimestamps();
    }
}

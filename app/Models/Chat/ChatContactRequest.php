<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatContactRequest extends Model
{
    protected $table = 'chat_contact_requests';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'to_role',
        'message',
        'status',
        'handled_by',
        'response',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('to_role', $role);
    }

    public function accept(?string $response = null, int $handledBy = null): void
    {
        $this->update([
            'status' => 'accepted',
            'response' => $response,
            'handled_by' => $handledBy,
        ]);
    }

    public function reject(?string $response = null, int $handledBy = null): void
    {
        $this->update([
            'status' => 'rejected',
            'response' => $response,
            'handled_by' => $handledBy,
        ]);
    }
}

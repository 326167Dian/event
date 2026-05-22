<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }


    // public function approve(string $adminNote = null)
    // {
    //     $this->update(['status' => 'approved', 'admin_note' => $adminNote]);
    //     // trigger notification / email
    //     $this->user->notify(new \App\Notifications\RegistrationApproved($this));
    // }


    // public function reject(string $reason = null)
    // {
    //     $this->update(['status' => 'rejected', 'admin_note' => $reason]);
    //     $this->user->notify(new \App\Notifications\RegistrationRejected($this));
    // }
}

<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['start_date', 'end_date', 'present']);
    }



    public function notAvailable()
    {
        $notAvailable = [];

        foreach ($this->users as $user) {
            $start = $user->pivot->start_date;
            $end = $user->pivot->end_date;

            $numUsers = $this->users()
                ->wherePivot('start_date', $start)
                ->wherePivot('end_date', $end)
                ->count();


            if($user->id == auth()->user()->id) {
                $notAvailable[] = [
                    'title' => 'Reservé',
                    'start' => $start,
                    'end' => $end,
                ];
            } else if ($numUsers >= $this->capacity) {
                $notAvailable[] = [
                    'title' => 'Complet',
                    'start' => $start,
                    'end' => $end,
                ];
            }
        }

        return $notAvailable;
    }
}

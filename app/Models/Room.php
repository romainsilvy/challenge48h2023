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

    protected $appends = [
        'presents',
        'booked',
        'not_presents'
    ];

    public function getPresentsAttribute()
    {
        $rooms = $this->users()
            ->wherePivot('present', true)
            ->wherePivot('start_date', '<=', now())
            ->wherePivot('end_date', '>=', now())
            ->get()->count();

        return $rooms;
    }

    public function getNotPresentsAttribute()
    {
        return $this->booked - $this->presents;
    }

    public function getBookedAttribute()
    {
        $rooms = $this->users()
            ->wherePivot('present', false)
            ->wherePivot('start_date', '<=', now())
            ->wherePivot('end_date', '>=', now())
            ->get()->count();

        return $rooms;
    }

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


            if ($user->id == auth()->user()->id) {
                $notAvailable[] = [
                    'title' => 'Reservé',
                    'start' => $start,
                    'end' => $end,
                ];
            } else if ($numUsers >= $this->capacity) {
                $newEntry = [
                    'title' => 'Complet',
                    'start' => $start,
                    'end' => $end,
                ];

                $entryExists = array_reduce($notAvailable, function ($carry, $entry) use ($newEntry) {
                    return $carry || ($entry['start'] == $newEntry['start'] && $entry['end'] == $newEntry['end']);
                }, false);

                // Si une entrée identique n'existe pas encore, ajouter la nouvelle entrée
                if (!$entryExists) {
                    $notAvailable[] = $newEntry;
                }
            }
        }

        return $notAvailable;
    }
}

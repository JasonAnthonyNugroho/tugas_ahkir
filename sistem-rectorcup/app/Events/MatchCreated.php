<?php

namespace App\Events;

use App\Models\Pertandingan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pertandingan;

    public function __construct(Pertandingan $pertandingan)
    {
        // Load relationships needed for the view
        $this->pertandingan = $pertandingan->load(['teamA', 'teamB', 'sport']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('scores'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->pertandingan->id,
            'sport_nama' => $this->pertandingan->sport->nama_sport,
            'sport_icon' => $this->pertandingan->sport->icon ?? 'bi-trophy',
            'team_a_name' => $this->pertandingan->teamA->name,
            'team_b_name' => $this->pertandingan->teamB->name,
            'score_a' => $this->pertandingan->score_a,
            'score_b' => $this->pertandingan->score_b,
            'lokasi' => $this->pertandingan->lokasi,
            'waktu_tanding' => $this->pertandingan->waktu_tanding->format('d M Y, H:i'),
            'status' => $this->pertandingan->status,
            'detail_url' => route('pertandingan.show', $this->pertandingan->id),
        ];
    }
}

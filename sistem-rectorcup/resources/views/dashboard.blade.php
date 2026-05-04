@extends('layouts.app')

@section('title', 'Dashboard Live')

@section('content')
    <div class="mb-4">
        <h2 class="font-weight-bold mb-1">Pertandingan Berlangsung</h2>
        <p class="text-muted">Pantau hasil pertandingan secara real-time.</p>
    </div>

    @if($pertandingans->isEmpty())
        <div class="card border-0 py-5 text-center" style="background: rgba(255,255,255,0.02); border-radius: 24px;">
            <div class="card-body">
                <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                    style="width: 80px; height: 80px; background: rgba(255,255,255,0.05) !important;">
                    <i class="bi bi-calendar-x text-muted h2 mb-0"></i>
                </div>
                <h5 class="font-weight-bold text-white">Tidak Ada Pertandingan</h5>
                <p class="text-muted mx-auto mb-0" style="max-width: 400px;">Saat ini tidak ada pertandingan yang sedang berlangsung atau terjadwal untuk filter ini.</p>
            </div>
        </div>
    @else
        <div class="row" id="matchContainer">
            @foreach($pertandingans as $p)
                <div class="col-md-6 col-xl-4 mb-4 match-card" data-id="{{ $p->id }}">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 24px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border) !important; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <span class="badge badge-primary px-3 py-1" style="border-radius: 100px;">
                                    <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} mr-2"></i>
                                    {{ $p->sport->nama_sport ?? 'Tournament' }}
                                </span>
                                <div class="badge-live-container">
                                    @if($p->status == 'live')
                                        <div class="badge-live">
                                            <span class="live-dot"></span> LIVE
                                        </div>
                                    @else
                                        <span class="badge badge-dark px-3 py-1 text-uppercase" style="border-radius: 100px; background: rgba(255,255,255,0.05);">
                                            Terjadwal
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row text-center align-items-center py-3">
                                <div class="col-5">
                                    <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                    <div class="display-4 font-weight-bold text-white score-a">{{ $p->score_a }}</div>
                                </div>
                                <div class="col-2 p-0">
                                    <div class="text-muted font-weight-bold small">VS</div>
                                </div>
                                <div class="col-5">
                                    <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">{{ $p->teamB?->name ?? 'TBD' }}</h4>
                                    <div class="display-4 font-weight-bold text-white score-b">{{ $p->score_b }}</div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center" style="border-color: rgba(255,255,255,0.05) !important;">
                                <div class="small text-muted">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-geo-alt mr-2 text-primary"></i> {{ $p->lokasi }}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 mr-2 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($p->waktu_tanding)->format('d M, H:i') }}
                                    </div>
                                </div>
                                <a href="{{ route('pertandingan.show', $p->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                    Detail <i class="bi bi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('styles')
<style>
    .match-card:hover .card {
        transform: translateY(-5px);
        background: rgba(255,255,255,0.06) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
    }

    .badge-live {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 4px 12px;
        border-radius: 100px;
        font-weight: bold;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        margin-right: 6px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    /* Fix untuk mencegah kemiringan UI */
    .card {
        transform: none !important;
    }
    
    .match-card {
        transform: none !important;
    }
    
    .score-a, .score-b {
        transform-origin: center !important;
    }
    
    .live-dot {
        transform-origin: center !important;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for score updates
    window.Echo.channel('scores')
        .listen('.score.updated', function(data) {
            console.log('Score updated:', data);
            updateMatchScore(data);
        })
        .listen('.match.created', function(data) {
            console.log('Match created:', data);
            addNewMatch(data);
        });

    // Function to update match score in UI
    function updateMatchScore(data) {
        const matchCard = document.querySelector(`[data-id="${data.id}"]`);
        if (matchCard) {
            // Update scores with animation
            const scoreAElement = matchCard.querySelector('.score-a');
            const scoreBElement = matchCard.querySelector('.score-b');
            
            if (scoreAElement && scoreBElement) {
                // Add animation class
                scoreAElement.style.transition = 'all 0.3s ease';
                scoreBElement.style.transition = 'all 0.3s ease';
                
                // Flash effect
                scoreAElement.style.transform = 'scale(1.1)';
                scoreBElement.style.transform = 'scale(1.1)';
                scoreAElement.style.color = '#10b981';
                scoreBElement.style.color = '#10b981';
                
                // Update scores
                scoreAElement.textContent = data.score_a;
                scoreBElement.textContent = data.score_b;
                
                // Reset animation after 300ms
                setTimeout(() => {
                    scoreAElement.style.transform = 'scale(1)';
                    scoreBElement.style.transform = 'scale(1)';
                    scoreAElement.style.color = '';
                    scoreBElement.style.color = '';
                }, 300);
            }
            
            // Update status badge if needed
            updateMatchStatus(matchCard, data.status);
            
            // Show notification
            showNotification('Skor diperbarui!', 'success');
        }
    }
    
    // Function to add new match to UI
    function addNewMatch(data) {
        const matchContainer = document.getElementById('matchContainer');
        if (matchContainer) {
            // Check if match already exists
            if (document.querySelector(`[data-id="${data.id}"]`)) {
                return;
            }
            
            // Create new match card HTML
            const newMatchHTML = `
                <div class="col-md-6 col-xl-4 mb-4 match-card" data-id="${data.id}">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 24px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border) !important; transition: all 0.3s ease; animation: slideInUp 0.5s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <span class="badge badge-primary px-3 py-1" style="border-radius: 100px;">
                                    <i class="bi ${data.sport_icon} mr-2"></i>
                                    ${data.sport_nama}
                                </span>
                                <div class="badge-live-container">
                                    ${data.status === 'live' ? `
                                        <div class="badge-live">
                                            <span class="live-dot"></span> LIVE
                                        </div>
                                    ` : `
                                        <span class="badge badge-dark px-3 py-1 text-uppercase" style="border-radius: 100px; background: rgba(255,255,255,0.05);">
                                            Terjadwal
                                        </span>
                                    `}
                                </div>
                            </div>

                            <div class="row text-center align-items-center py-3">
                                <div class="col-5">
                                    <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">${data.team_a_name}</h4>
                                    <div class="display-4 font-weight-bold text-white score-a">${data.score_a}</div>
                                </div>
                                <div class="col-2 p-0">
                                    <div class="text-muted font-weight-bold small">VS</div>
                                </div>
                                <div class="col-5">
                                    <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">${data.team_b_name}</h4>
                                    <div class="display-4 font-weight-bold text-white score-b">${data.score_b}</div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center" style="border-color: rgba(255,255,255,0.05) !important;">
                                <div class="small text-muted">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-geo-alt mr-2 text-primary"></i> ${data.lokasi}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 mr-2 text-primary"></i>
                                        ${data.waktu_tanding}
                                    </div>
                                </div>
                                <a href="${data.detail_url}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                    Detail <i class="bi bi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add new match to the beginning of container
            matchContainer.insertAdjacentHTML('afterbegin', newMatchHTML);
            
            // Show notification
            showNotification('Pertandingan baru ditambahkan!', 'info');
        }
    }
    
    // Function to update match status
    function updateMatchStatus(matchCard, status) {
        const statusContainer = matchCard.querySelector('.badge-live-container');
        if (statusContainer) {
            if (status === 'live') {
                statusContainer.innerHTML = `
                    <div class="badge-live">
                        <span class="live-dot"></span> LIVE
                    </div>
                `;
            } else {
                statusContainer.innerHTML = `
                    <span class="badge badge-dark px-3 py-1 text-uppercase" style="border-radius: 100px; background: rgba(255,255,255,0.05);">
                        Terjadwal
                    </span>
                `;
            }
        }
    }
    
    // Function to show notifications
    function showNotification(message, type = 'info') {
        const colors = {
            success: '#10b981',
            info: '#3b82f6',
            warning: '#f59e0b',
            error: '#ef4444'
        };
        
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 right-0 m-3 p-3 text-white rounded shadow-lg';
        toast.style.cssText = `
            background: ${colors[type] || colors.info};
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            min-width: 250px;
        `;
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-bell-fill mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection

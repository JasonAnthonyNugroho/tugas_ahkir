<?php $__env->startSection('title', 'Bracket: ' . $tournament->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold text-white mb-1">
                <i class="bi bi-trophy mr-2"></i><?php echo e($tournament->name); ?>

            </h2>
            <p class="text-muted mb-0">
                <span class="badge badge-primary mr-2"><?php echo e($tournament->sport->nama_sport); ?></span>
                <span class="badge badge-secondary"><?php echo e($tournament->year); ?></span>
                <?php if($tournament->start_date && $tournament->end_date): ?>
                    <span class="badge badge-info ml-2">
                        <i class="bi bi-calendar mr-1"></i>
                        <?php echo e(\Carbon\Carbon::parse($tournament->start_date)->format('d M')); ?> - <?php echo e(\Carbon\Carbon::parse($tournament->end_date)->format('d M Y')); ?>

                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <form action="<?php echo e(route('admin.tournament.delete', $tournament)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger shadow-sm" style="border-radius: 12px; padding: 10px 20px; font-weight: 600;" onclick="return confirm('Hapus tournament ini beserta seluruh jadwal pertandingannya?')">
                    <i class="bi bi-trash mr-2"></i>Hapus Turnamen
                </button>
            </form>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert border-0 shadow-sm mb-4 py-3" style="border-radius: 16px; background: rgba(16, 185, 129, 0.15); color: #10b981; border-left: 4px solid #10b981 !important;">
            <i class="bi bi-check-circle-fill mr-2"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert border-0 shadow-sm mb-4 py-3" style="border-radius: 16px; background: rgba(239, 68, 68, 0.15); color: #f87171; border-left: 4px solid #ef4444 !important;">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <div class="row mb-5">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.1)); border-radius: 20px; border: 1px solid rgba(99, 102, 241, 0.2) !important; backdrop-filter: blur(10px);">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(99, 102, 241, 0.2); color: #818cf8;">
                        <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase font-weight-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Tim Peserta</div>
                        <h3 class="font-weight-bold mb-0 text-white"><?php echo e($tournament->teams->count()); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); border-radius: 20px; border: 1px solid rgba(16, 185, 129, 0.2) !important; backdrop-filter: blur(10px);">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.2); color: #34d399;">
                        <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase font-weight-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Match Selesai</div>
                        <h3 class="font-weight-bold mb-0 text-white"><?php echo e($tournament->pertandingans->where('status', 'finished')->count()); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(185, 28, 28, 0.1)); border-radius: 20px; border: 1px solid rgba(239, 68, 68, 0.2) !important; backdrop-filter: blur(10px);">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.2); color: #f87171;">
                        <i class="bi bi-broadcast" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase font-weight-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Sedang Live</div>
                        <h3 class="font-weight-bold mb-0 text-white"><?php echo e($tournament->pertandingans->where('status', 'live')->count()); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.1)); border-radius: 20px; border: 1px solid rgba(245, 158, 11, 0.2) !important; backdrop-filter: blur(10px);">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                        <i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase font-weight-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Match Tersisa</div>
                        <h3 class="font-weight-bold mb-0 text-white"><?php echo e($tournament->pertandingans->where('round', 1)->whereNull('winner_id')->count()); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 mb-4" style="background: rgba(255,255,255,0.03); border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="text-white font-weight-bold mb-0">
                <i class="bi bi-diagram-3 mr-2"></i>Tournament Bracket
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="bracket-wrapper">
                <div class="bracket-tree" id="bracketTree">
                    <?php $__currentLoopData = $rounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roundNum => $matches): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bracket-column" data-round="<?php echo e($roundNum); ?>">
                            <div class="round-title"><?php echo e($matches->first()->babak); ?></div>
                            
                            <div class="round-matches">
                                <?php $__currentLoopData = $matches->sortBy('match_number'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bracket-match-card <?php echo e($match->winner_id ? 'completed' : ''); ?> <?php echo e($match->status === 'live' ? 'live' : ''); ?>"
                                         data-match-id="<?php echo e($match->id); ?>">
                                        
                                        
                                        <div class="match-header">
                                            <div class="match-date">
                                                <?php if($match->match_date): ?>
                                                    <i class="bi bi-clock mr-1"></i>
                                                    <?php echo e($match->match_date->format('d M, H:i')); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">TBA</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($match->status === 'live'): ?>
                                                <span class="live-badge">LIVE</span>
                                            <?php elseif($match->status === 'finished'): ?>
                                                <span class="finished-badge">DONE</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        
                                        <div class="team-row <?php echo e($match->winner_id == $match->team_a_id ? 'winner' : ($match->winner_id ? 'loser' : '')); ?>

                                                    <?php echo e($match->team_a_id ? '' : 'tbd'); ?>

                                                    <?php echo e(!$match->team_a_id && $match->team_b_id ? 'bye' : ''); ?>">
                                            <div class="team-info">
                                                <?php if($match->team_a_id): ?>
                                                    <span class="team-name"><?php echo e($match->teamA->name); ?></span>
                                                <?php else: ?>
                                                    <span class="team-name tbd">TBD</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="team-score">
                                                <?php if($match->status !== 'scheduled'): ?>
                                                    <?php echo e($match->score_a); ?>

                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="team-row <?php echo e($match->winner_id == $match->team_b_id ? 'winner' : ($match->winner_id ? 'loser' : '')); ?>

                                                    <?php echo e($match->team_b_id ? '' : 'tbd'); ?>

                                                    <?php echo e(!$match->team_b_id && $match->team_a_id ? 'bye' : ''); ?>">
                                            <div class="team-info">
                                                <?php if($match->team_b_id): ?>
                                                    <span class="team-name"><?php echo e($match->teamB->name); ?></span>
                                                <?php else: ?>
                                                    <span class="team-name tbd">TBD</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="team-score">
                                                <?php if($match->status !== 'scheduled'): ?>
                                                    <?php echo e($match->score_b); ?>

                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="match-actions">
                                            <?php if($match->status === 'scheduled' && $match->team_a_id && $match->team_b_id): ?>
                                                <form action="<?php echo e(route('pertandingan.bulk-live')); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="match_ids[]" value="<?php echo e($match->id); ?>">
                                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                                        <i class="bi bi-play-fill mr-1"></i>Start
                                                    </button>
                                                </form>
                                            <?php elseif($match->status === 'live' || $match->status === 'finished'): ?>
                                                <a href="<?php echo e(route('admin.skor')); ?>" class="btn btn-sm btn-primary w-100">
                                                    <i class="bi bi-pencil mr-1"></i>Manage
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary w-100" disabled>
                                                    <i class="bi bi-clock mr-1"></i>Wait
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        
                                        
                                        <?php if($roundNum < $rounds->keys()->last()): ?>
                                            <div class="connector-right"></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            
            <?php if($thirdPlaceMatch): ?>
                <div class="mt-4">
                    <h6 class="text-white font-weight-bold mb-3">
                        <i class="bi bi-award mr-2"></i>Perebutan Juara 3
                    </h6>
                    <div class="bracket-match-card bronze-match" style="max-width: 300px;">
                        <div class="match-header">
                            <small class="match-number">Bronze</small>
                            <?php if($thirdPlaceMatch->status === 'live'): ?>
                                <span class="live-badge">LIVE</span>
                            <?php elseif($thirdPlaceMatch->status === 'finished'): ?>
                                <span class="finished-badge">DONE</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="team-row <?php echo e($thirdPlaceMatch->winner_id == $thirdPlaceMatch->team_a_id ? 'winner' : ($thirdPlaceMatch->winner_id ? 'loser' : '')); ?>

                                    <?php echo e($thirdPlaceMatch->team_a_id ? '' : 'tbd'); ?>">
                            <div class="team-info">
                                <?php if($thirdPlaceMatch->team_a_id): ?>
                                    <span class="team-name"><?php echo e($thirdPlaceMatch->teamA->name); ?></span>
                                <?php else: ?>
                                    <span class="team-name tbd">TBD</span>
                                <?php endif; ?>
                            </div>
                            <div class="team-score">
                                <?php if($thirdPlaceMatch->status !== 'scheduled'): ?>
                                    <?php echo e($thirdPlaceMatch->score_a); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="team-row <?php echo e($thirdPlaceMatch->winner_id == $thirdPlaceMatch->team_b_id ? 'winner' : ($thirdPlaceMatch->winner_id ? 'loser' : '')); ?>

                                    <?php echo e($thirdPlaceMatch->team_b_id ? '' : 'tbd'); ?>">
                            <div class="team-info">
                                <?php if($thirdPlaceMatch->team_b_id): ?>
                                    <span class="team-name"><?php echo e($thirdPlaceMatch->teamB->name); ?></span>
                                <?php else: ?>
                                    <span class="team-name tbd">TBD</span>
                                <?php endif; ?>
                            </div>
                            <div class="team-score">
                                <?php if($thirdPlaceMatch->status !== 'scheduled'): ?>
                                    <?php echo e($thirdPlaceMatch->score_b); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="match-actions">
                            <?php if($thirdPlaceMatch->status === 'scheduled' && $thirdPlaceMatch->team_a_id && $thirdPlaceMatch->team_b_id): ?>
                                <form action="<?php echo e(route('pertandingan.bulk-live')); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="match_ids[]" value="<?php echo e($thirdPlaceMatch->id); ?>">
                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                        <i class="bi bi-play-fill mr-1"></i>Start
                                    </button>
                                </form>
                            <?php elseif($thirdPlaceMatch->status === 'live' || $thirdPlaceMatch->status === 'finished'): ?>
                                <a href="<?php echo e(route('admin.skor')); ?>" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-pencil mr-1"></i>Manage
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card border-0" style="background: rgba(255,255,255,0.03); border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="text-white font-weight-bold mb-0">
                <i class="bi bi-people mr-2"></i>Tim Peserta
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <?php $__currentLoopData = $tournament->teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 col-lg-2 mb-3">
                        <div class="card bg-dark border-secondary">
                            <div class="card-body p-3 text-center">
                                <div class="d-inline-flex align-items-center justify-content-center mb-2 text-white font-weight-bold"
                                     style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 10px; font-size: 1rem;">
                                    <?php echo e(strtoupper(substr($team->name, 0, 1))); ?>

                                </div>
                                <div class="text-white font-weight-bold small"><?php echo e($team->name); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?php echo e($team->prodi); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    
    .bracket-wrapper {
        overflow-x: auto;
        padding: 20px 0;
    }
    
    .bracket-tree {
        display: flex;
        gap: 40px;
        min-width: max-content;
    }
    
    .bracket-column {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 220px;
    }
    
    .round-title {
        text-align: center;
        color: #94a3b8;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(99, 102, 241, 0.3);
    }
    
    .round-matches {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    
    .bracket-match-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .bracket-match-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    
    .bracket-match-card.completed {
        border-color: rgba(16, 185, 129, 0.3);
    }
    
    .bracket-match-card.live {
        border-color: #ef4444;
        animation: livePulse 2s infinite;
    }
    
    @keyframes livePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }
    
    .match-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 10px;
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        gap: 8px;
    }
    
    .match-number {
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .match-date {
        color: #94a3b8;
        font-size: 0.7rem;
        flex: 1;
        text-align: center;
        white-space: nowrap;
    }
    
    .match-date i {
        font-size: 0.65rem;
    }
    
    .live-badge {
        background: #ef4444;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        animation: blink 1s infinite;
    }
    
    .finished-badge {
        background: #10b981;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    
    .team-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.2s;
    }
    
    .team-row:last-child {
        border-bottom: none;
    }
    
    .team-row.winner {
        background: rgba(16, 185, 129, 0.15);
    }
    
    .team-row.winner .team-name {
        color: #10b981;
        font-weight: 700;
    }
    
    .team-row.winner .team-score {
        color: #10b981;
        font-weight: 700;
    }
    
    .team-row.loser {
        opacity: 0.6;
    }
    
    .team-row.loser .team-score {
        color: #ef4444;
    }
    
    .team-row.tbd {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .team-row.tbd .team-name {
        color: #64748b;
        font-style: italic;
    }
    
    .team-row.bye {
        background: rgba(16, 185, 129, 0.1);
    }
    
    .team-row.bye .team-name::after {
        content: '(BYE)';
        margin-left: 8px;
        font-size: 0.7rem;
        color: #10b981;
    }
    
    .team-info {
        flex: 1;
        min-width: 0;
    }
    
    .team-name {
        color: #f8fafc;
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .team-name.tbd {
        color: #64748b;
    }
    
    .team-score {
        color: #94a3b8;
        font-weight: 600;
        font-size: 1rem;
        min-width: 30px;
        text-align: center;
        padding: 4px 8px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
    }
    
    
    .match-actions {
        padding: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .match-actions .btn {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    
    .bronze-match {
        border-color: rgba(245, 158, 11, 0.3);
    }
    
    .bronze-match .match-header {
        background: rgba(245, 158, 11, 0.1);
    }
    
    
    .connector-right {
        position: absolute;
        right: -20px;
        top: 50%;
        width: 20px;
        height: 2px;
        background: rgba(99, 102, 241, 0.4);
    }
    
    .connector-right::after {
        content: '';
        position: absolute;
        right: 0;
        top: -4px;
        width: 0;
        height: 0;
        border-top: 5px solid transparent;
        border-bottom: 5px solid transparent;
        border-left: 8px solid rgba(99, 102, 241, 0.4);
    }
    
    
    @media (max-width: 768px) {
        .bracket-tree {
            flex-direction: column;
            gap: 20px;
        }
        
        .bracket-column {
            min-width: 100%;
        }
        
        .round-matches {
            gap: 10px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\!UKDW\tugas-akhir\sistem-rectorcup\resources\views/admin/bracket-view.blade.php ENDPATH**/ ?>
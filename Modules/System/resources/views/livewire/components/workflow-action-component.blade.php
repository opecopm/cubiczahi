<div class="workflow-component">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible mb-2">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible mb-2">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$instance || !$instance->currentStep)
        <div class="card mb-3">
            <div class="card-body">
                <span class="text-secondary small">Workflow not started or not available for this document.</span>
            </div>
        </div>
    @else
        {{-- Workflow status ribbon --}}
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge {{ $instance->currentStep->is_final ? 'bg-success' : 'bg-info' }}">
                        {{ $instance->currentStep->name }}
                    </span>
                    <div class="d-flex align-items-center overflow-hidden flex-nowrap gap-1 small text-secondary">
                        @foreach($instance->workflow->steps->sortBy('id') as $step)
                            <span class="{{ $instance->current_step_id == $step->id ? 'fw-bold text-primary' : 'text-secondary' }}">
                                {{ $step->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="text-muted">›</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if(!$instance->currentStep->is_final)
            <div class="card mb-3">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        @if($instance->assigned_to_id && $instance->assigned_to_type === 'App\\Models\\User' && $instance->assignedTo)
                            <span class="avatar avatar-sm">
                                {{ strtoupper(substr($instance->assignedTo->name ?? '?', 0, 2)) }}
                            </span>
                        @else
                            <span class="avatar avatar-sm {{ $instance->assigned_to_id ? 'bg-dark-lt' : 'bg-secondary-lt' }}">
                                @if(!$instance->assigned_to_id)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4"/><path d="M17 17l5 5"/><path d="M22 17l-5 5"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
                                @endif
                            </span>
                        @endif
                        <div>
                            <div class="text-secondary small text-uppercase fw-bold" style="letter-spacing:.5px;font-size:.65rem;">Current Assignee</div>
                            <div class="fw-bold">
                                @if($instance->assigned_to_id)
                                    @if($instance->assigned_to_type === 'App\\Models\\User')
                                        {{ $instance->assignedTo?->name ?? 'User ID: '.$instance->assigned_to_id }}
                                    @elseif($instance->assigned_to_type === 'Spatie\\Permission\\Models\\Role')
                                        Role: {{ $instance->assignedTo?->name ?? 'Unknown Role' }}
                                    @elseif($instance->assigned_to_type === 'Modules\\IAM\\Models\\Team')
                                        Team: {{ $instance->assignedTo?->name ?? 'Unknown Team' }}
                                    @else
                                        ID: {{ $instance->assigned_to_id }}
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">Waiting for assignment...</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        @if($instance->assigned_to_id && $availableActions->isEmpty())
                            <span class="badge bg-warning-lt">In Progress</span>
                            <div class="text-secondary small mt-1">Awaiting their action</div>
                        @elseif($availableActions->isNotEmpty())
                            <span class="badge bg-success-lt">Action Required</span>
                            <div class="text-success small mt-1 fw-bold">It's your turn</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Action buttons --}}
        @if($availableActions->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($availableActions as $action)
                    @php($code = strtolower((string) $action->action_code))
                    @php($btn = str_contains($code, 'approve') ? 'success' : (str_contains($code, 'reject') ? 'danger' : (str_contains($code, 'resubmit') || str_contains($code, 'return') ? 'warning' : (str_contains($code, 'submit') ? 'info' : 'primary'))))
                    <button wire:click="confirmAction('{{ $action->action_code }}', '{{ $action->action_name }}')"
                        class="btn btn-sm btn-{{ $btn }}">
                        {{ $action->action_name }}
                    </button>
                @endforeach
            </div>
        @endif

        @if($lastLog and $showLogs)
            <div class="card mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="small text-secondary d-flex align-items-center flex-wrap gap-1">
                            <span>Last action:</span>
                            <span class="fw-bold text-dark">{{ strtoupper((string) $lastLog->action_code) }}</span>
                            @if($lastLog->user)
                                <span class="avatar avatar-xs">{{ strtoupper(substr($lastLog->user->name ?? '?', 0, 2)) }}</span>
                            @else
                                <span class="avatar avatar-xs bg-secondary-lt">SY</span>
                            @endif
                            <span class="text-secondary">({{ $lastLog->created_at?->format('Y-m-d H:i') }})</span>
                        </span>
                        <button type="button" class="btn btn-link btn-sm p-0" wire:click="toggleTimeline">
                            {{ $showTimeline ? 'Hide timeline' : 'View timeline' }}
                        </button>
                    </div>

                    @if(trim((string) ($lastLog->comment ?? '')) !== '')
                        <div class="small text-secondary mt-2">{{ $lastLog->comment }}</div>
                    @endif

                    @if($showTimeline)
                        <hr class="my-3">
                        @if(empty($timelineLogs))
                            <div class="small text-secondary">No timeline records.</div>
                        @else
                            <div class="list-group list-group-flush wf-log-list">
                                @foreach($timelineLogs as $item)
                                    @php($code = strtolower((string) ($item['action_code'] ?? '')))
                                    @php($color = str_contains($code, 'approve') ? 'success' : (str_contains($code, 'reject') ? 'danger' : (str_contains($code, 'resubmit') || str_contains($code, 'return') ? 'warning' : (str_contains($code, 'submit') ? 'info' : 'primary'))))
                                    <div class="list-group-item px-0 wf-log-item wf-log-item-{{ $color }}" wire:key="wf-log-{{ $item['id'] }}">
                                        <div class="d-flex justify-content-between">
                                            <div class="small d-flex align-items-center gap-1">
                                                <span class="avatar avatar-xs">{{ $item['user_initials'] ?? 'SY' }}</span>
                                                <span class="fw-bold">{{ strtoupper((string) ($item['action_code'] ?? '')) }}</span>
                                                @if(($item['from_step'] ?? null) || ($item['to_step'] ?? null))
                                                    <span class="text-secondary">— {{ $item['from_step'] ?? '—' }} → {{ $item['to_step'] ?? '—' }}</span>
                                                @endif
                                            </div>
                                            <div class="small text-secondary">{{ $item['created_at'] ?? '' }}</div>
                                        </div>
                                        @if(trim((string) ($item['comment'] ?? '')) !== '')
                                            <div class="small text-secondary mt-1">{{ $item['comment'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    @endif

    {{-- Comment Modal --}}
    <div class="modal modal-blur fade @if($showCommentModal) show d-block @endif" tabindex="-1" role="dialog"
        style="background: rgba(0,0,0,0.5); z-index: 1060;" @if($showCommentModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action: {{ $selectedActionName }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showCommentModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary">Are you sure you want to proceed with <strong>{{ $selectedActionName }}</strong>? This action will transition the process to the next step and notify the responsible parties.</p>
                    <div class="mb-3">
                        <label class="form-label">Internal Comment / Note (Optional)</label>
                        <textarea class="form-control" wire:model.defer="comment" rows="3" placeholder="Explain the action taken..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <input type="file" class="form-control" multiple wire:model="actionAttachments">
                        @error('actionAttachments.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if(!empty($actionAttachments))
                            <div class="small text-secondary mt-1">{{ count($actionAttachments) }} file(s) selected</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showCommentModal', false)">Cancel</button>
                    <button type="button" class="btn btn-primary"
                        wire:click="performAction"
                        wire:loading.attr="disabled"
                        wire:target="performAction">
                        <span wire:loading.remove wire:target="performAction">Confirm Action</span>
                        <span wire:loading wire:target="performAction">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .wf-log-list {
            border: 1px solid var(--tblr-border-color);
            border-radius: var(--tblr-border-radius);
            overflow: hidden;
        }
        .wf-log-item {
            border-left: 3px solid transparent;
        }
        .wf-log-item-primary { border-left-color: var(--tblr-primary); }
        .wf-log-item-info    { border-left-color: var(--tblr-info); }
        .wf-log-item-success { border-left-color: var(--tblr-success); }
        .wf-log-item-warning { border-left-color: var(--tblr-warning); }
        .wf-log-item-danger  { border-left-color: var(--tblr-danger); }
    </style>
</div>

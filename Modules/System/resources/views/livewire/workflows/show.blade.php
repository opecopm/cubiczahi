<div>
    @include('system::livewire.partials.workflow-show-nav-tabs')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle"><a href="{{ route('admin.system.workflows.index') }}">Workflows</a></div>
                    <h2 class="page-title">{{ $workflow->name }}</h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.system.workflows.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(request('tab', 'steps') === 'steps')
                <div class="row g-3">
                    {{-- STEPS --}}
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">Workflow Steps</h3>
                                <div class="card-options">
                                    <button class="btn btn-sm btn-primary" wire:click="openStepModal">Add Step</button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach($workflow->steps as $step)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $step->is_initial ? 'bg-success' : ($step->is_final ? 'bg-dark' : 'bg-info') }} badge-pill">
                                                    {{ $step->is_initial ? 'Start' : ($step->is_final ? 'End' : 'Step') }}
                                                </span>
                                                <div>
                                                    <div class="fw-bold">{{ $step->name }}</div>
                                                    <div class="text-secondary small">{{ $step->code }}</div>
                                                </div>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-ghost-secondary" wire:click="openStepModal({{ $step->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/></svg>
                                                </button>
                                                <button class="btn btn-sm btn-ghost-danger"
                                                        wire:click="deleteStep({{ $step->id }})"
                                                        wire:confirm="Delete this step?">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- TRANSITIONS + SLA + MATCH RULES --}}
                    <div class="col-lg-8">
                        {{-- Transitions --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">State Transitions (Rules)</h3>
                                <div class="card-options">
                                    <button class="btn btn-sm btn-primary" wire:click="openTransitionModal">Add Transition</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>From Step</th>
                                            <th>Action / Code</th>
                                            <th>To Step</th>
                                            <th>Assignee Rule</th>
                                            <th>Notifications</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workflow->transitions as $transition)
                                            <tr>
                                                <td class="text-secondary">{{ $transition->fromStep ? $transition->fromStep->name : 'Any Active' }}</td>
                                                <td>
                                                    <div class="fw-bold">{{ $transition->action_name }}</div>
                                                    <code class="text-secondary small">{{ $transition->action_code }}</code>
                                                </td>
                                                <td><span class="badge bg-info-lt">{{ $transition->toStep->name }}</span></td>
                                                <td>
                                                    @if($transition->assignments->isNotEmpty())
                                                        @php
                                                            $userAssignees = $transition->assignments->filter(fn($a) => ($a->assignment_rule ?? '') === 'explicit' && ($a->assignable_type ?? '') === 'App\\Models\\User')->map(fn($a) => $a->assignable)->filter();
                                                            $otherAssignments = $transition->assignments->reject(fn($a) => ($a->assignment_rule ?? '') === 'explicit' && ($a->assignable_type ?? '') === 'App\\Models\\User');
                                                        @endphp
                                                        @if($userAssignees->isNotEmpty())
                                                            <div class="avatar-list avatar-list-stacked">
                                                                @foreach($userAssignees as $user)
                                                                    <span class="avatar avatar-xs" title="{{ trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: ($user->email ?? '') }}">
                                                                        {{ strtoupper(substr($user->first_name ?? $user->email ?? '?', 0, 1)) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @foreach($otherAssignments as $asg)
                                                            <span class="badge bg-secondary-lt small">{{ ucfirst($asg->assignment_rule) }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-secondary small">No Rule</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transition->notification_rules && count($transition->notification_rules['notify'] ?? []))
                                                        <span class="badge bg-info-lt">{{ count($transition->notification_rules['notify']) }} Roles</span>
                                                    @else
                                                        <span class="text-secondary small">None</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-ghost-secondary" wire:click="openTransitionModal({{ $transition->id }})">Edit</button>
                                                        <button class="btn btn-sm btn-ghost-danger"
                                                                wire:click="deleteTransition({{ $transition->id }})"
                                                                wire:confirm="Delete this transition?">Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- SLA Rules --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">SLA & Escalation Rules (In Hours)</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Urgent</label>
                                        <input type="number" class="form-control" wire:model.live="escalationRules.urgent">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">High</label>
                                        <input type="number" class="form-control" wire:model.live="escalationRules.high">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Medium</label>
                                        <input type="number" class="form-control" wire:model.live="escalationRules.medium">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Low</label>
                                        <input type="number" class="form-control" wire:model.live="escalationRules.low">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-primary btn-sm" wire:click="saveSLA">Update SLA Rules</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Match Rules --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Workflow Match Rules</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-secondary small mb-3">
                                    Define conditions to select this workflow (e.g. doc_type_id, request_type). Use comma-separated values for multiple matches.
                                </p>
                                @foreach(($matchRules ?? []) as $i => $row)
                                    <div class="row g-2 align-items-center mb-2" wire:key="match-rule-{{ $i }}">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" wire:model.live="matchRules.{{ $i }}.field" placeholder="doc_type_id or documentType.code">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" wire:model.live="matchRules.{{ $i }}.value" placeholder="5 or LTR,MOM">
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-sm btn-ghost-danger" wire:click="removeMatchRuleRow({{ $i }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addMatchRuleRow">+ Add Rule</button>
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="saveMatchRules">Save Rules</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(request('tab') === 'instances')
                {{-- Instances Tab --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Active Workflow Instances</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Target Record</th>
                                    <th>Internal ID</th>
                                    <th>Current Step</th>
                                    <th>Assignee</th>
                                    <th>Status</th>
                                    <th>Last Movement</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($instances = \Modules\System\Models\WorkflowInstance::where('workflow_id', $this->workflowId)->latest()->paginate(25))
                                @forelse($instances as $instance)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $instance->target_reference }}</div>
                                            <div class="text-secondary small">{{ class_basename($instance->model_type) }}</div>
                                        </td>
                                        <td class="text-secondary">{{ $instance->model_id }}</td>
                                        <td><span class="badge bg-info-lt">{{ $instance->currentStep->name }}</span></td>
                                        <td class="text-secondary">
                                            @if($instance->assigned_to_id)
                                                ID: {{ $instance->assigned_to_id }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $instance->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                {{ ucfirst($instance->status) }}
                                            </span>
                                        </td>
                                        <td class="text-secondary">{{ $instance->updated_at->diffForHumans() }}</td>
                                        <td>
                                            @if($instance->target_url)
                                                <a href="{{ $instance->target_url }}" class="btn btn-sm btn-ghost-secondary">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-secondary py-4">No active instances found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($instances->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        {{ $instances->links() }}
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @include('system::livewire.workflows.partials.step-modal')
    @include('system::livewire.workflows.partials.transition-modal')
</div>

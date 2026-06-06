@if($showModal && $modalType === 'transition')
    <div class="modal modal-blur fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit' : 'Add' }} Transition</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">From Step</label>
                            <select class="form-select" wire:model="fromStepId">
                                <option value="">Any Active Step</option>
                                @foreach($workflow->steps as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To Step</label>
                            <select class="form-select" wire:model="toStepId">
                                <option value="">Select Destination</option>
                                @foreach($workflow->steps as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Action Display Name (Button Label)</label>
                            <input type="text" class="form-control" wire:model="actionName">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Action Code (e.g. resolve)</label>
                            <input type="text" class="form-control" wire:model="actionCode">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Required Permission (Optional)</label>
                            <input type="text" class="form-control" wire:model="permission">
                        </div>
                    </div>

                    <div class="subheader mb-2">Notifications & Messaging</div>
                    <div class="mb-3">
                        <label class="form-label">Notify Recipients:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="creator" wire:model="notificationRules.notify" id="notify-creator">
                                <label class="form-check-label" for="notify-creator">Creator</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="customer" wire:model="notificationRules.notify" id="notify-cust">
                                <label class="form-check-label" for="notify-cust">Customer/Client</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="assignee" wire:model="notificationRules.notify" id="notify-assign">
                                <label class="form-check-label" for="notify-assign">Assignee</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="transition_assignees" wire:model="notificationRules.notify" id="notify-all-assignees">
                                <label class="form-check-label" for="notify-all-assignees">All Assignees</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="role:Admin" wire:model="notificationRules.notify" id="notify-admin">
                                <label class="form-check-label" for="notify-admin">Admin</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Custom Message (Supports {reference}, {status})</label>
                        <textarea class="form-control" wire:model="customMessage" rows="2"></textarea>
                    </div>

                    <div class="subheader mb-2 mt-3">Field Updates (Optional)</div>
                    <div class="text-secondary small mb-2">Supports {reference}, {status}, {action}, {model}, {user_id}, {user_name}, {now}</div>

                    @foreach(($fieldUpdates ?? []) as $index => $row)
                        <div class="row g-2 align-items-center mb-2" wire:key="fu-row-{{ $index }}">
                            <div class="col-md-5">
                                <input type="text" class="form-control" wire:model="fieldUpdates.{{ $index }}.field" placeholder="Field name">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" wire:model="fieldUpdates.{{ $index }}.value" placeholder="Value">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-ghost-danger" wire:click="removeFieldUpdateRow({{ $index }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 0"/><path d="M4 7l1 12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2l1 -12"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addFieldUpdateRow">+ Add Field Update</button>
                    </div>

                    <div class="subheader mb-2 mt-3">Assignee Rules</div>

                    @foreach($assignmentsData as $index => $asg)
                        <div class="row g-2 align-items-start mb-3 p-2 bg-light rounded" wire:key="asg-row-{{ $index }}">
                            <div class="col-md-4">
                                <label class="form-label small">Rule</label>
                                <select class="form-select form-select-sm" wire:model.live="assignmentsData.{{ $index }}.assignment_rule">
                                    <option value="explicit">Explicit</option>
                                    <option value="creator">Creator</option>
                                    <option value="model_field">Field</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                @if(($assignmentsData[$index]['assignment_rule'] ?? '') === 'explicit')
                                    <div class="row g-2">
                                        <div class="col-5">
                                            <label class="form-label small">Type</label>
                                            <select class="form-select form-select-sm" wire:model.live="assignmentsData.{{ $index }}.assignable_type">
                                                <option value="App\Models\User">User</option>
                                                <option value="Spatie\Permission\Models\Role">Role</option>
                                                <option value="Modules\IAM\Models\Team">Team</option>
                                            </select>
                                        </div>
                                        <div class="col-7">
                                            <label class="form-label small">Select</label>
                                            <livewire:entity-select
                                                :key="'sel-'.$index.'-'.($assignmentsData[$index]['assignable_type'] ?? '')"
                                                :entity="($assignmentsData[$index]['assignable_type'] ?? '') === 'Spatie\Permission\Models\Role' ? 'role' : (($assignmentsData[$index]['assignable_type'] ?? '') === 'Modules\IAM\Models\Team' ? 'team' : 'user')"
                                                wire:model="assignmentsData.{{ $index }}.assignable_id"
                                                label="Select"
                                            />
                                        </div>
                                    </div>
                                @endif
                                @if(($assignmentsData[$index]['assignment_rule'] ?? '') === 'model_field')
                                    <label class="form-label small">Field</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="assignmentsData.{{ $index }}.assignment_value" placeholder="e.g. assigned_to_id">
                                @endif
                            </div>
                            <div class="col-md-1 text-end pt-4">
                                <button type="button" class="btn btn-sm btn-ghost-danger" wire:click="removeAssignmentRow({{ $index }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 0"/><path d="M4 7l1 12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2l1 -12"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addAssignmentRow">+ Add Rule</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveTransition">
                        {{ $updateMode ? 'Update' : 'Save' }} Transition
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Add New Customer',
        'breadcrumbs' => [
            [
                'label' => 'Customers',
                'url' => route('admin.crm.customers.index'),
                'icon' => 'back',
            ],
            [
                'label' => 'Create',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'title' => 'Back to Customers',
                'route' => 'admin.crm.customers.index',
                'icon' => 'ti ti-arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="store">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" wire:model="name">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company (EN)</label>
                                <input type="text" class="form-control" id="company_en" wire:model="company.en">
                                @error('company.en')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company ({{ strtoupper($secondaryLang) }})</label>
                                <input type="text" class="form-control" id="company_secondary"
                                    wire:model="company.{{ $secondaryLang }}">
                                @error("company.{$secondaryLang}")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" wire:model="email">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Phone Code</label>
                                <select class="form-select" id="phone_code" wire:model="phone_code">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->phone_code }}">+{{ $country->phone_code }}
                                            ({{ $country->iso2 }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('phone_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" wire:model="phone">
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Industry</label>
                                <input type="text" class="form-control" id="industry" wire:model="industry">
                                @error('industry')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="text" class="form-control" id="website" wire:model="website">
                                @error('website')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CRN</label>
                                <input type="text" class="form-control" id="crn" wire:model="crn">
                                @error('crn')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TRN</label>
                                <input type="text" class="form-control" id="trn" wire:model="trn">
                                @error('trn')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Group (<a href="#"
                                        wire:click.prevent="openAddGroupModal" class="text-primary">+ Add
                                        New</a>)</label>
                                <select id="customer_group_id" wire:model="customer_group_id" class="form-select">
                                    <option value="">Select One</option>
                                    @php
                                        $renderGroupOptions = function ($groups, $level = 0) use (
                                            &$renderGroupOptions,
                                        ) {
                                            foreach ($groups->where('parent_id', null) as $group) {
                                                echo '<option value="' .
                                                    $group->id .
                                                    '">' .
                                                    str_repeat('&mdash;&nbsp;', $level) .
                                                    e($group->name) .
                                                    '</option>';
                                                if ($group->children && $group->children->count()) {
                                                    foreach ($group->children as $child) {
                                                        echo '<option value="' .
                                                            $child->id .
                                                            '">' .
                                                            str_repeat('&mdash;&nbsp;', $level + 1) .
                                                            e($child->name) .
                                                            '</option>';
                                                        if ($child->children && $child->children->count()) {
                                                            $renderGroupOptions($child->children, $level + 2);
                                                        }
                                                    }
                                                }
                                            }
                                        };
                                        $renderGroupOptions($groups);
                                    @endphp
                                </select>
                                @error('customer_group_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-2">
                                <h4 class="form-label">Address (Billing)</h4>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <select id="country" wire:model.live="country" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <select id="state" wire:model.live="state" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->name }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <select id="city" wire:model="city" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->name }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="line1" wire:model="line1">
                                @error('line1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="line2" wire:model="line2">
                                @error('line2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" wire:model="postal_code">
                                @error('postal_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                        <div class="col-md-6 mt-2">
                            <h4 class="form-label">Address (Shipping)</h4>
                        </div>
                        <div class="col-md-6 mt-2 d-flex align-items-center justify-content-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="same_as_billing"
                                    wire:model.live="same_as_billing">
                                <label class="form-check-label" for="same_as_billing">Same as Billing Address</label>
                            </div>
                        </div>
                        @if (!$same_as_billing)
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <select id="shipping_country" wire:model.live="shipping_country" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('shipping_country')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <select id="shipping_state" wire:model.live="shipping_state" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($shipping_states as $state)
                                        <option value="{{ $state->name }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('shipping_state')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <select id="shipping_city" wire:model="shipping_city" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($shipping_cities as $city)
                                        <option value="{{ $city->name }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('shipping_city')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="shipping_line1"
                                    wire:model="shipping_line1">
                                @error('shipping_line1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="shipping_line2"
                                    wire:model="shipping_line2">
                                @error('shipping_line2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="shipping_postal_code"
                                    wire:model="shipping_postal_code">
                                @error('shipping_postal_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Save Customer
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade @if ($showAddGroupModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if ($showAddGroupModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer Group</h5>
                    <button type="button" class="btn-close" wire:click="$set('showAddGroupModal', false)"></button>
                </div>
                <form wire:submit.prevent="saveNewGroup">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model="newGroupName">
                                @error('newGroupName')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Parent Group</label>
                                <select class="form-select" wire:model="newGroupParentId">
                                    <option value="">No Parent</option>
                                    @php
                                        $renderGroupOptionsModal = function ($groups, $level = 0) use (&$renderGroupOptionsModal) {
                                            foreach ($groups->where('parent_id', null) as $group) {
                                                echo '<option value="' . $group->id . '">' . str_repeat('&mdash;&nbsp;', $level) . e($group->name) . '</option>';
                                                if ($group->children && $group->children->count()) {
                                                    foreach ($group->children as $child) {
                                                        echo '<option value="' . $child->id . '">' . str_repeat('&mdash;&nbsp;', $level + 1) . e($child->name) . '</option>';
                                                        if ($child->children && $child->children->count()) {
                                                            $renderGroupOptionsModal($child->children, $level + 2);
                                                        }
                                                    }
                                                }
                                            }
                                        };
                                        $renderGroupOptionsModal($groups);
                                    @endphp
                                </select>
                                @error('newGroupParentId')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showAddGroupModal', false)">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveNewGroup">
                            <span wire:loading wire:target="saveNewGroup" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

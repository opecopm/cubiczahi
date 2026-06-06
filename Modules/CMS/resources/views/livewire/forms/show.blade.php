    <form wire:submit.prevent="submit" id="contact_form">
        @if($successMessage)
            <div class="alert alert-success">
                {{ $successMessage }}
            </div>
        @endif
        <div class="row">
            @foreach($formModel->fields as $field)
                <div class="col-sm-{{ $field->width ?? 12 }}">
                    <div class="mb-3">
                        @if($field->type === 'textarea')
                            <textarea wire:model="data.{{ $field->name }}" class="form-control {{ $field->is_required ? 'required' : '' }}" rows="7" placeholder="{{ $field->placeholder }}"></textarea>

                        @elseif($field->type === 'select')
                            <select wire:model="data.{{ $field->name }}" class="form-select form-control {{ $field->is_required ? 'required' : '' }}">
                                <option value="">{{ $field->placeholder ?? '-- Select --' }}</option>
                                @php
                                    $options = is_string($field->options) ? json_decode($field->options, true) : $field->options;
                                    if (is_string($options)) $options = array_map('trim', explode(',', $options));
                                @endphp
                                @if(is_array($options))
                                    @foreach($options as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                @endif
                            </select>

                        @elseif($field->type === 'radio')
                            <div>
                                <label class="mb-2">{{ $field->placeholder ?? $field->label }}</label>
                                @php
                                    $options = is_string($field->options) ? json_decode($field->options, true) : $field->options;
                                    if (is_string($options)) $options = array_map('trim', explode(',', $options));
                                @endphp
                                @if(is_array($options))
                                    @foreach($options as $opt)
                                        <div class="form-check">
                                            <input class="form-check-input {{ $field->is_required ? 'required' : '' }}" type="radio" wire:model="data.{{ $field->name }}" value="{{ $opt }}" id="{{ $field->name }}_{{ $loop->index }}">
                                            <label class="form-check-label" for="{{ $field->name }}_{{ $loop->index }}">
                                                {{ $opt }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        @elseif($field->type === 'checkbox')
                            <div>
                                 <label class="mb-2">{{ $field->placeholder ?? $field->label }}</label>
                                 @php
                                    $options = is_string($field->options) ? json_decode($field->options, true) : $field->options;
                                    if (is_string($options)) $options = array_map('trim', explode(',', $options));
                                @endphp
                                @if(is_array($options))
                                    @foreach($options as $opt)
                                        <div class="form-check">
                                            <input class="form-check-input {{ $field->is_required ? 'required' : '' }}" type="checkbox" wire:model="data.{{ $field->name }}" value="{{ $opt }}" id="{{ $field->name }}_{{ $loop->index }}">
                                            <label class="form-check-label" for="{{ $field->name }}_{{ $loop->index }}">
                                                {{ $opt }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        @elseif($field->type === 'file')
                            <input type="file" wire:model="data.{{ $field->name }}" class="form-control {{ $field->is_required ? 'required' : '' }}">

                        @else
                            <input type="{{ $field->type }}" wire:model="data.{{ $field->name }}" class="form-control {{ $field->is_required ? 'required' : '' }}" placeholder="{{ $field->placeholder }}">
                        @endif

                        @if($field->help_text)
                            <div class="form-text">{{ $field->help_text }}</div>
                        @endif

                        @error('data.' . $field->name) <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endforeach

            @if($formModel->use_honeypot)
                <div style="display: none;">
                    <label>Keep this field blank</label>
                    <input type="text" wire:model="data.hp_email_check" name="hp_email_check">
                </div>
            @endif

            @if($formModel->use_captcha)
                <div class="col-12">
                    <div class="mb-3">
                         <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <div class="mb-5">
                    @php
                        $btnSettings = $formModel->button_settings;
                        $locale = app()->getLocale();
                    @endphp

                    {{-- Submit Button --}}
                    <button type="submit" class="theme-btn btn-style-one {{ $btnSettings['submit_class'] ?? '' }}" data-loading-text="Please wait...">
                        <span class="btn-title">{{ $btnSettings['submit_text'][$locale] ?? $btnSettings['submit_text']['en'] ?? 'Send message' }}</span>
                    </button>

                    {{-- Reset Button --}}
                    @if(!empty($btnSettings['use_reset']))
                        <button type="button" wire:click="$set('data', [])" class="theme-btn btn-style-one {{ $btnSettings['reset_class'] ?? '' }}" style="margin-left: 15px;">
                            <span class="btn-title">{{ $btnSettings['reset_text'][$locale] ?? $btnSettings['reset_text']['en'] ?? 'Reset' }}</span>
                        </button>
                    @endif

                    <div wire:loading wire:target="submit" class="ms-2 text-primary">
                         <i class="fa fa-circle-o-notch fa-spin"></i> Sending...
                    </div>
                </div>
            </div>
             <div class="col-md-12 col-lg-12 message-status"></div>
        </div>
    </form>

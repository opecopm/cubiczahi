<!-- Row Settings -->
<div class="row-settings">
    <h6 class="mb-3">Row Settings</h6>

    <div class="row mb-3">
        <div class="col-6">
            <div class="settings-field">
                <label class="form-label">Padding Top (px)</label>
                <input type="number" class="form-control"
                       wire:model.live="settings.padding_top">
            </div>
        </div>
        <div class="col-6">
            <div class="settings-field">
                <label class="form-label">Padding Bottom (px)</label>
                <input type="number" class="form-control"
                       wire:model.live="settings.padding_bottom">
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-6">
            <div class="settings-field">
                <label class="form-label">Margin Top (px)</label>
                <input type="number" class="form-control"
                       wire:model.live="settings.margin_top">
            </div>
        </div>
        <div class="col-6">
            <div class="settings-field">
                <label class="form-label">Margin Bottom (px)</label>
                <input type="number" class="form-control"
                       wire:model.live="settings.margin_bottom">
            </div>
        </div>
    </div>

    <div class="settings-field mb-3">
        <label class="form-label">Text Alignment</label>
        <select class="form-control" wire:model.live="settings.text_align">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
            <option value="justify">Justify</option>
        </select>
    </div>

    <div class="settings-field mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" class="form-control"
               wire:model.live="sort_order">
    </div>

    <div class="settings-field mb-3">
        <label class="form-label">Custom CSS Classes</label>
        <input type="text" class="form-control"
               wire:model.live="css_classes"
               placeholder="custom-class another-class">
    </div>

    <div class="settings-field mb-3">
        <label class="form-label">Custom CSS</label>
        <textarea class="form-control" rows="4"
                  wire:model.live="custom_css"
                  placeholder="/* Custom CSS */"></textarea>
    </div>

    <div class="d-flex gap-2">
        <button wire:click="updateElementSettings" class="btn btn-primary btn-sm">
            <i class="ti ti-device-floppy me-1"></i>
            Save Settings
        </button>
        <button wire:click="duplicateRow({{ $selectedElement }})" class="btn btn-warning btn-sm">
            <i class="ti ti-copy me-1"></i>
            Duplicate
        </button>
    </div>
</div>

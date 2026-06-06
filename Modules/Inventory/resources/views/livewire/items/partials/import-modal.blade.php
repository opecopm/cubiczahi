<div id="importModal" class="modal modal-blur fade @if($showImportModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showImportModal) aria-modal="true" @else aria-hidden="true" @endif>
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Items</h5>
                <button type="button" class="btn-close" wire:click="closeImportModal"></button>
            </div>
            <div class="modal-body">
                @if (session()->has('error'))
                    <div class="alert alert-danger text-white">
                        {!! session('error') !!}
                    </div>
                @endif
                <form wire:submit.prevent="import">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="import_file" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="import_file" wire:model="importFile">
                            <div wire:loading wire:target="importFile" class="text-info mt-1">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Uploading file...
                            </div>
                            @error('importFile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <p>Download a sample file for reference: <a href="{{url('import-templates/items_import_template.xlsx')}}" class="text-primary">Sample File</a></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeImportModal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary" @if(!$importFile) disabled @endif wire:loading.attr="disabled">
                            <span wire:loading wire:target="import" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="import">Import</span>
                            <span wire:loading wire:target="import">Importing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-add-param" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="param-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Format Surat <span class="text-danger">*</span></label>
                                <input name="format_surat" id="format_surat" type="text" class="form-control" value="{{ old('format_surat') }}"
                                placeholder="example: namaperusahaan/kodesurat">
                                <p id="error-format_surat" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Hospital <span class="text-danger">*</span></label>
                                <select name="hospital" id="hospital" class="form-control select">
                                    <option value="">-- Please Selected --</option>
                                    @foreach ($hospitals as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('hospital') == $data->id ? 'selected' : null }}>{{ $data->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p id="error-hospital" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-text">Save</span>
                        </button>
                        <button class="btn btn-danger btn-cancel" type="button">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
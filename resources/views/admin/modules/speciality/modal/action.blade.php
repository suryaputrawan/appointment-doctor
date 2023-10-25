<!-- Modal -->
<div class="modal fade" id="modal-add-specialities" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="speciality-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Specialities <span class="text-danger">*</span></label>
                                <input name="speciality" id="speciality" type="text" class="form-control" value="{{ old('speciality') }}">
                                <p id="error-speciality" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Image <span class="text-danger">(Max: 1Mb, Format: PNG) *</span></label>
                                <input name="picture" id="picture-upload" type="file" class="form-control" accept=".png" onchange="previewImages()">
                                <p id="error-picture" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div id="preview"></div>
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
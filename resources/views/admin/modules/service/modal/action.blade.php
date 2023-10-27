<!-- Modal -->
<div class="modal fade" id="modal-add-service" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="service-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Service Name <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Please insert description about this service... ">{{ old('description') }}</textarea>
                                <p id="error-description" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Picture <span class="text-danger">(Max: 1Mb, Format: jpg, jpeg, png) *</span></label>
                                <input name="picture" id="picture-upload" type="file" class="form-control" accept="image/*" onchange="previewImages()">
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
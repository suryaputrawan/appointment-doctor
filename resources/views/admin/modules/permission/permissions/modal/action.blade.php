<!-- Modal -->
<div class="modal fade" id="modal-add-permission" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="permission-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Permission Name <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-text">Save</span>
                        </button>
                        <button class="btn btn-danger btn-cancel" type="button">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="modal-add-hospital" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="hospital-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>RS / Clinic Name <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Address <span class="text-danger">*</span></label>
                                <input name="address" id="address" type="text" class="form-control" value="{{ old('address') }}">
                                <p id="error-address" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Telphone <span class="text-danger">*</span></label>
                                <input name="phone" id="phone" type="text" class="form-control" placeholder="Example : 0361 - 9999999" value="{{ old('phone') }}">
                                <p id="error-phone" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>WhatsApp <span class="text-danger">*</span></label>
                                <input name="whatsapp" id="whatsapp" type="text" class="form-control" placeholder="Example : 08123456789" value="{{ old('whatsapp') }}">
                                <p id="error-whatsapp" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input name="email" id="email" type="text" class="form-control" placeholder="Example : abc@example.com" value="{{ old('email') }}">
                                <p id="error-email" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Instagram Link </label>
                                <input name="instagram" id="instagram" type="text" class="form-control" placeholder="Example : " value="{{ old('instagram') }}">
                                <p id="error-instagram" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Facebook Link </label>
                                <input name="facebook" id="facebook" type="text" class="form-control" placeholder="Example : " value="{{ old('facebook') }}">
                                <p id="error-facebook" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Logo <span class="text-danger">(Max: 1Mb, Format: png) *</span></label>
                                <input name="logo" id="logo" type="file" class="form-control" accept=".png" onchange="previewImagesLogo()">
                                <p id="error-logo" style="color: red" class="error"></p>
                            </div>
                            <div id="preview"></div>
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
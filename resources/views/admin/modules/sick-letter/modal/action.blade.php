<!-- Modal -->
<div class="modal fade" id="modal-add-sick-letter" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="sick-letter-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Patient Name <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-12 col-sm-5">
                            <div class="form-group">
                                <label>Patient Email <span class="text-danger">*</span></label>
                                <input name="patient_email" id="patient_email" type="email" class="form-control" value="{{ old('patient_email') }}">
                                <p id="error-patient_email" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-2">
                            <div class="form-group">
                                <label>Age <span class="text-danger">*</span></label>
                                <input name="age" id="age" type="number" min="0" class="form-control" value="{{ old('age') }}">
                                <p id="error-age" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-2">
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-control select">
                                    <option value="">-Selected-</option>
                                    <option value="M" {{ old('gender') == "M" ? 'selected' : null }}>MALE</option>
                                    <option value="F" {{ old('gender') == "F" ? 'selected' : null }}>FEMALE</option>
                                </select>
                                <p id="error-gender" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>Profession</label>
                                <input name="profession" id="profession" type="text" class="form-control" value="{{ old('profession') }}">
                                <p id="error-profession" style="color: red" class="error"></p>
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

                    <hr>

                    <h6 class="text-danger">Lama Pasien Istirahat & Diagnosis</h6>
                    <div class="row form-row">
                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input name="start_date" id="start_date" type="date" class="form-control" value="{{ old('start_date') }}">
                                <p id="error-start_date" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input name="end_date" id="end_date" type="date" class="form-control" value="{{ old('end_date') }}">
                                <p id="error-end_date" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Diagnosis</label>
                                <input name="diagnosis" id="diagnosis" type="text" class="form-control" value="{{ old('diagnosis') }}">
                                <p id="error-diagnosis" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>

                    <hr>

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
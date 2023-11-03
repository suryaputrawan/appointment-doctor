<!-- Modal -->
<div class="modal fade" id="modal-add-practice-schedule" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="practice-schedule-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Hospital / Clinic <span class="text-danger">*</span></label>
                                <select name="hospital" id="hospital" class="select">
                                    <option value="">-- Please Selected --</option>
                                    @foreach ($hospital as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('hospital') == $data->id ? 'selected' : null }}>{{ $data->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p id="error-hospital" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Doctor Name <span class="text-danger">*</span></label>
                                <select name="doctor" id="doctor" class="select">
                                    <option value="">-- Please Selected --</option>
                                    @foreach ($doctor as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('doctor') == $data->id ? 'selected' : null }}>{{ $data->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p id="error-doctor" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input name="date" id="date" type="date" class="form-control" value="{{ old('date') }}">
                                <p id="error-date" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <input name="start_time" id="start-time" type="time" class="form-control" value="{{ old('start_time') }}">
                                <p id="error-start-time" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>End Time <span class="text-danger">*</span></label>
                                <input name="end_time" id="end-time" type="time" class="form-control" value="{{ old('end_time') }}">
                                <p id="error-end-time" style="color: red" class="error"></p>
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
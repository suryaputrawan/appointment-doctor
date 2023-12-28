<!-- Modal -->
<div class="modal fade" id="modal-add-off-duty" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="off-duty-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-8">
                            <div class="form-group">
                                <label>Doctor <span class="text-danger">*</span></label>
                                <select name="doctor" id="doctor" class="form-control select">
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
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input name="date" id="date" type="date" class="form-control" value="{{ old('date') }}">
                                <p id="error-date" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Reason <span class="text-danger">*</span></label>
                                <input name="reason" id="reason" type="reason" class="form-control" value="{{ old('reason') }}">
                                <p id="error-reason" style="color: red" class="error"></p>
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
<!-- Modal -->
<div class="modal fade" id="modal-add-user" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Fullname <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input name="username" id="username" type="text" class="form-control" value="{{ old('username') }}" style="text-transform:lowercase">
                                <p id="error-username" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input name="email" id="email" type="email" class="form-control" value="{{ old('email') }}">
                                <p id="error-email" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-9">
                            <div class="form-group">
                                <label>Hospital / Clinic <span class="text-danger">*</span></label>
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
                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" {{ old('status') == "1" ? 'selected' : null }}>AKTIF</option>
                                    <option value="0" {{ old('status') == "0" ? 'selected' : null }}>NON AKTIF</option>
                                </select>
                                <p id="error-status" style="color: red" class="error"></p>
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
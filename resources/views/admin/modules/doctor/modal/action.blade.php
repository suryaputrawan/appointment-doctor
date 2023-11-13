<!-- Modal -->
<div class="modal fade" id="modal-add-doctor" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="doctor-form">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>Doctor Name <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-9">
                            <div class="form-group">
                                <label>Specialization <span class="text-danger">*</span></label>
                                <input name="specialization" id="specialization" type="text" class="form-control" value="{{ old('specialization') }}">
                                <p id="error-specialization" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-control select">
                                    <option value="">-- Please Selected --</option>
                                    <option value="M" {{ old('gender') == "M" ? 'selected' : null }}>MALE</option>
                                    <option value="F" {{ old('gender') == "F" ? 'selected' : null }}>FEMALE</option>
                                </select>
                                <p id="error-gender" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Specialities <span class="text-danger">*</span></label>
                                <select name="specialities" id="specialities" class="form-control select">
                                    <option value="">-- Please Selected --</option>
                                    @foreach ($specialities as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('specialities') == $data->id ? 'selected' : null }}>{{ $data->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p id="error-specialities" style="color: red" class="error"></p>
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
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>About Me <span class="text-danger">*</span></label>
                                <textarea name="about_me" id="about-me" class="form-control" rows="3" placeholder="Please insert description about this doctor... ">{{ old('about_me') }}</textarea>
                                <p id="error-about" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-9">
                            <div class="form-group">
                                <label>Foto <span class="text-danger">(Max: 1Mb, Format: jpg, jpeg, png) *</span></label>
                                <input name="picture" id="picture-upload" type="file" class="form-control" accept="image/*" onchange="previewImages()">
                                <p id="error-picture" style="color: red" class="error"></p>
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
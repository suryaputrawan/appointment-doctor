<!-- Modal -->
<div class="modal fade" id="modal-add-doctor-location" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5><span id="title-span"></span>
            </div>
            <div class="modal-body">
                <form id="doctor-location-form">
                    @csrf
                    <input name="doctor_id" type="text" id="doctor-id" style="display: none">
                    <div class="row form-row">
                        <div class="col-12 col-sm-10">
                            <div class="form-group">
                                <label> Hospital / Clinic Name <span class="text-danger">*</span></label>
                                <select name="location" id="location" class="select">
                                    <option value="">-- Please Selected --</option>
                                    @foreach ($hospital as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('location') == $data->id ? 'selected' : null }}>{{ $data->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p id="error-location" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row form-row" id="item-form">
                        <div class="form-group col-12 col-md-11">
                            <div class="table-responsive">
                                <table id="tb-item" class="table table-stripped" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input name="day[]" id="day" type="text" class="form-control" value="{{ old('day') }}" placeholder="Example : MON - FRI" style="text-transform:uppercase">
                                                <p id="error-day" style="color: red" class="error"></p>
                                            </td>
                                            <td>
                                                <input name="time[]" id="time" type="text" class="form-control" value="{{ old('time') }}" placeholder="Example : 10:00 AM - 2:00 PM" style="text-transform:uppercase">
                                                <p id="error-time" style="color: red" class="error"></p>
                                            </td>
                                            {{-- <td>
                                                <button id="btn-item-delete" type="button" class="btn btn-sm btn-danger"><i class='fe fe-trash'></i></button>
                                            </td> --}}
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                         <div class="form-group col-12 col-md-1">
                            <button id="btn-item-add" type="button" class="btn btn-sm btn-primary"><i class='fe fe-plus'></i></button>
                         </div>
                    </div>

                    {{-- <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Day <span class="text-danger">*</span></label>
                                <input name="day" id="day" type="text" class="form-control" value="{{ old('day') }}" placeholder="Example : MON - FRI" style="text-transform:uppercase">
                                <p id="error-day" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Time <span class="text-danger">*</span></label>
                                <input name="time" id="time" type="text" class="form-control" value="{{ old('time') }}" placeholder="Example : 10:00 AM - 2:00 PM" style="text-transform:uppercase">
                                <p id="error-time" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div> --}}
                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-text">Save</span>
                        </button>
                        <button class="btn btn-warning btn-clear" type="button">Clear</button>
                        <button class="btn btn-danger btn-cancel" type="button">Close</button>
                    </div>
                </form>

                <div id="item-loading" class="card-body" style="display: none">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                        <p>Load data...</p>
                    </div>
                </div>

                <hr>

                <div class="row form-row">
                    <div class="col-sm-12 mt-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">Location List</h5>
                                <div class="table-responsive">
                                    <table id="datatable-location" class="datatable table table-stripped" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>RS/Clinic</th>
                                                <th>Day</th>
                                                <th>Time</th>
                                                <th style="width: 100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>			
                </div>    
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
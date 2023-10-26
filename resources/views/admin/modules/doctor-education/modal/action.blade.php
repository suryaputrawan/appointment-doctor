<!-- Modal -->
<div class="modal fade" id="modal-add-doctor-education" data-backdrop="static" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5><span id="title-span"></span>
            </div>
            <div class="modal-body">
                <form id="doctor-education-form">
                    @csrf
                    <input name="doctor_id" type="text" id="doctor-id" style="display: none">
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label>University Name <span class="text-danger">*</span></label>
                                <input name="university" id="university" type="text" class="form-control" value="{{ old('university') }}">
                                <p id="error-university" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Specialization <span class="text-danger">*</span></label>
                                <input name="specialization" id="specialization" type="text" class="form-control" value="{{ old('specialization') }}">
                                <p id="error-specialization" style="color: red" class="error"></p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>Start Year <span class="text-danger">*</span></label>
                                <input name="start_year" id="start-year" type="number" placeholder="YYYY" class="form-control" value="{{ old('start_year') }}">
                                <p id="error-start-year" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="form-group">
                                <label>End Year <span class="text-danger">*</span></label>
                                <input name="end_year" id="end-year" type="number" placeholder="YYYY" class="form-control" value="{{ old('end_year') }}">
                                <p id="error-end-year" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
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
                                <h5 class="mb-3">Education List</h5>
                                <div class="table-responsive">
                                    <table id="datatable-education" class="datatable table table-stripped" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px">#</th>
                                                <th>University</th>
                                                <th>Specialization</th>
                                                <th>Year</th>
                                                <th style="width: 100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="align-middle">
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
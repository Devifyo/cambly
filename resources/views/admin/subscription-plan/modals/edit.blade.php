<div class="modal fade" id="edit_subscription_plan" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- ID is already set to editPlanForm --}}
                <form id="editPlanForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                         <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name" name="name" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Price <span class="text-danger">*</span></label>
                                <input type="text" id="edit_price" name="price" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Credits per Cycle <span class="text-danger">*</span></label>
                                <input type="number" id="edit_credits_per_cycle" name="credits_per_cycle" class="form-control" required>
                            </div>
                        </div>
                         <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Is Popular? <span class="text-danger">*</span></label>
                                <select id="edit_is_popular" name="is_popular" class="form-select" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Status <span class="text-danger">*</span></label>
                                <select id="edit_status" name="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Subtitle</label>
                                <input type="text" id="edit_subtitle" name="subtitle" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Features</label>
                                <textarea id="edit_features" name="features" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Icon (Upload New)</label>
                                <input type="file" name="icon_path" class="form-control" accept="image/*">
                                <small class="form-text text-muted">Upload a new icon to replace the current one.</small>
                                <div id="current_icon_preview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Or Icon Link (External URL)</label>
                                <input type="text" id="edit_icon_link" name="icon_link" class="form-control" placeholder="https://example.com/icon.svg">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_subscription_plan" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- ADDED ID HERE: id="addPlanForm" --}}
                <form id="addPlanForm" method="POST" action="{{ route('admin.subscription.plan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., Pro Plan">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Price <span class="text-danger">*</span></label>
                                <input type="text" name="price" class="form-control" placeholder="e.g., 29.99">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Credits per Cycle <span class="text-danger">*</span></label>
                                <input type="number" name="credits_per_cycle" class="form-control" placeholder="e.g., 100">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Is Popular? <span class="text-danger">*</span></label>
                                <select class="form-select" name="is_popular">
                                    <option value="">Select Option</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="e.g., Best for professionals">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Full plan description..."></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Features</label>
                                <textarea class="form-control" name="features" rows="3" placeholder="Enter features, one per line..."></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Icon (Optional)</label>
                                <input type="file" name="icon_path" class="form-control" accept="image/*">
                            </div>
                        </div>
                        {{-- <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Or Icon Link (External URL)</label>
                                <input type="text" name="icon_link" class="form-control" placeholder="https://example.com/icon.svg">
                            </div>
                        </div> --}}
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
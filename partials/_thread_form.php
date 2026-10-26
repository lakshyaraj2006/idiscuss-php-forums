<button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#newThreadModal">&plus; Add Thread</button>

<!-- New Thread Modal -->
<div class="modal fade" id="newThreadModal" tabindex="-1" aria-labelledby="newThreadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newThreadModalLabel">Add New Thread</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/forum/api/threads/createThread" method="post" id="newThreadForm">
                    <input type="hidden" name="catId" id="catId" value="<?php echo $category_id; ?>">
                    <div class="form-group">
                        <label for="newThreadTitle">Problem title</label>
                        <input type="text" class="form-control" maxlength="50" minlength="1" id="newThreadTitle" name="newThreadTitle" placeholder="Enter your problem here">
                    </div>
                    <div class="form-group">
                        <label for="newThreadShortDesc">Problem short description</label>
                        <input type="text" class="form-control" maxlength="50" minlength="1" id="newThreadShortDesc" name="newThreadShortDesc" placeholder="Briefly describe your problem here">
                    </div>
                    <div class="form-group">
                        <label for="newThreadContent">Problem description</label>
                        <textarea class="form-control" rows="8" id="newThreadContent" name="newThreadContent" placeholder="Ellaborate your problem here"></textarea>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <button type="submit" class="btn btn-danger">Add</button>
                        <div class="text-muted mb-0 d-flex align-items-center invisible" id="saveLocalIndicator">
                            <div class="spinner-border spinner-border-sm mr-1" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <span class="text-muted">Saving changes locally...</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
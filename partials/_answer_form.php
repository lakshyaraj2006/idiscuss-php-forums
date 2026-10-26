<button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#newAnswerModal">&plus; Add Answer</button>

<!-- New Answer Modal -->
<div class="modal fade" id="newAnswerModal" tabindex="-1" aria-labelledby="newAnswerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAnswerModalLabel">Add New Answer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/forum/api/answers/createAnswer" method="post" id="newAnswerForm">
                    <input type="hidden" name="threadId" id="threadId" value="<?php echo $thread_id; ?>">
                    <div class="form-group">
                        <label for="newAnswerContent">Answer</label>
                        <textarea class="form-control" rows="8" id="newAnswerContent" name="newAnswerContent" placeholder="Ellaborate your answer here"></textarea>
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
<div class="modal" id="modal_presentation_items_editor" tabindex="-1" role="dialog">
        <form>
            <div class="modal-dialog modal-fluid" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Items - <span role="entry-title"></span></h4>
                    </div>
                    <div class="modal-body">
                        <ul class="sortable-items clearfix" role="sortable-items">
                            <li class="index active" style="display: none"></li>
                            <li class="add" title="Upload">
                                <input type="file" accept="image/*" id="presentation_items_uploader" multiple style="display: none">
                                <label for="presentation_items_uploader" class="button-overlay">
                                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                </label>
                            </li>
                        </ul>
                        <div role="no-interact-overlay"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

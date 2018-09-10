<div class="modal fade" id="{!! $id !!}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="text-align: left;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Delete {!! $object !!}?</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete {!! $name !!}?</p>
                @if(isset($mailchimp))
                    <div class="well">
                        <img src="/images/freddie.png" width="20" />
                        &nbsp;&nbsp;
                        This will also delete {!! $name !!} from MailChimp.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <form class="form-horizontal" role="form" method="POST" action="{!! $uri !!}">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
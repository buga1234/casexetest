@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="well well-sm">
                    <div class="row">
                        <div class="col-sm-6 col-md-8">
                            <h4>
                                {{$user->name}}
                                <span class="label @if($user->offline == 1) label-default @elseif($user->offline == 0) label-success @endif">

                                    @if($user->offline == 1) Offline @elseif($user->offline == 0) Online @endif

                            </span>
                            </h4>
                            <p>
                            <div><span>Email: </span>{{$user->email}}</div>
                            <div><span>Status: </span>{{$user->status}}</div>
                            </p>
                            <!-- Split button -->
                            <div class="btn-group">
                                @if (Auth::check())
                                    @if ($user->inContactList)
                                        <button type="button" class="btn btn-xs btn-primary" data-toggle="modal"
                                                data-target="#send_message_modal">
                                            Send message
                                        </button>

                                        <a class="btn btn-xs btn-danger"
                                           href="{{action('ContactListController@removeFromContactList', [
                                                   'contact_list_user_id' => Auth::user()->id,
                                                   'contactListId' => $user->c_list_id,
                                                   'userId' => $user->id ])}}">Remove from contact list</a>
                                    @else
                                        <a class="btn btn-xs btn-primary"
                                           href="{{action('ContactListController@addToContactList', [
                                                   'contact_list_user_id' => Auth::user()->id,
                                                   'contactListId' => $user->c_list_id,
                                                   'userId' => $user->id ])}}">Add to contact list</a>

                                    @endif;
                                @else
                                    You should <a href="{{ url('/login') }}">login</a> to send a message
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="send_message_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <form method="post" action="{{action('MessageController@sendMessage')}}">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Change status</h4>
                    </div>
                    <div class="modal-body">


                        <textarea class="form-control" placeholder="type a message" name="message"></textarea>

                        <input type="hidden" name="contact_list_user_id" value="{{Auth::user()->id}}"/>
                        <input type="hidden" name="contactListId" value="{{$user->c_list_id}}"/>
                        <input type="hidden" name="userId" value="{{$user->id}}"/>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="clearfix"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
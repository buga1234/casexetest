@extends('layouts.app')


@section('header-title')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>My profile</h1>
            </div>
        </div>
    </div>

@endsection



@section('content')

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="row">
                    <div class="col-sm-6 col-md-8">
                        <h4>{{ Auth::user()->name}}
                            <span class="label @if($user->offline == 1) label-default @elseif($user->offline == 0) label-success @endif">

                                    @if($user->offline == 1) Offline @elseif($user->offline == 0) Online @endif

                            </span>
                        </h4>
                        <p>
                        <div><span>Email: </span>{{$user->email}}</div>
                        <div><span>Status: </span>{{$user->status}}</div>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-warning btn-xs" data-toggle="modal"
                                data-target="#change_status_modal">
                            Change status
                        </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="change_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <form method="post" action="{{action('UserController@changeStatus')}}">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Change status</h4>
                    </div>
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="status">Status</label>
                            <input id="status" type="text" value="{{$user->status}}" name="status"
                                   class="form-control"/>
                        </div>
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}"/>
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



    <script src="https://use.fontawesome.com/45e03a14ce.js"></script>
    <div class="main_section">
        <div class="container">
            <div class="chat_container">
                <div class="col-sm-3 chat_sidebar">
                    <div class="row">

                        <div class="member_list">
                            <ul class="list-unstyled">
                                @if (count($contactList) > 0)
                                    @foreach($contactList as $c_user)
                                        <li class="left clearfix @if($activeUserId == $c_user->reciever_id) active @endif">
                                            <a href="{{route('home')}}?contact_list_user_id={{Auth::user()->id}}&userId={{$c_user->reciever_id}}&action=getChat">
                                                <div class="chat-body clearfix">
                                                    <div class="header_sec">
                                                        <strong class="primary-font">{{$c_user->reciever_name}}</strong>
                                                    </div>
                                                    <div class="contact_sec">
                                                        <span class="badge pull-right">{{$c_user->unread_count}}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="left clearfix">
                                        No contacts
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <!--chat_sidebar-->

                @if($chat)
                    <div class="col-sm-9 message_section">
                        <div class="row">

                            <div class="chat_area">
                                <ul class="list-unstyled">

                                    @foreach($chat as $message)
                                        <li class="left clearfix @if (!$message->recieved) admin_chat @endif">
                                            <div class="chat-body1 clearfix">
                                                <p>{{$message->message_text}}</p>
                                                <div class="chat_time @if ($message->recieved) pull-left @else pull-right @endif">
                                                    {{$message->sender_name}} &nbsp;&nbsp;
                                                </div>
                                                <div class="chat_time @if ($message->recieved) pull-left @else pull-right @endif">
                                                    {{$message->message_timestamp}} &nbsp;&nbsp;
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div><!--chat_area-->
                            <div class="message_write">

                                <form method="post" action="{{action('MessageController@sendMessage')}}">
                                    <textarea class="form-control" placeholder="type a message"
                                              name="message"></textarea>
                                    <input type="hidden" name="contact_list_user_id" value="{{Auth::user()->id}}"/>
                                    <input type="hidden" name="contactListId" value="{{$user->contact_list_id}}"/>
                                    <input type="hidden" name="userId" value="{{$activeUserId}}"/>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="clearfix"></div>
                                    <div class="chat_bottom">
                                        <input type="submit" class="pull-right btn btn-success" value="Send"/>
                                    </div>


                                </form>

                            </div>
                        </div>
                    </div> <!--message_section-->
                @endif
            </div>
        </div>
    </div>

    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="pull-left">Contact list</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        @if (count($contactList) > 0)
                            <table class="table">
                                <tr>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Email
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                </tr>
                                @foreach($contactList as $user)
                                    <tr>
                                        <td>
                                            {{$user->reciever_name}}
                                        </td>
                                        <td>
                                            {{$user->reciever_email}}
                                        </td>
                                        <td>
                                            {{$user->reciever_status}}
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-info"
                                               href="{{route('user', ['userId' => $user->reciever_id])}}">Show
                                                info</a>
                                            <a class="btn btn-xs btn-danger"
                                               href="{{action('ContactListController@removeFromContactList', [
                                                   'contact_list_user_id' => Auth::user()->id,
                                                   'userId' => $user->reciever_id ])}}">Remove</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            No contacts
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Users list</div>
                    <div class="panel-body">

                        <table class="table">
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Status
                                </th>
                            </tr>
                            @foreach($users as $user_item)
                                @if($user_item->id !=  Auth::user()->id)
                                    <tr>
                                        <td>
                                            {{$user_item->id}}
                                        </td>
                                        <td>
                                            {{$user_item->name}}
                                        </td>
                                        <td>
                                            {{$user_item->email}}
                                        </td>
                                        <td>
                                            {{$user_item->status}}
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-info"
                                               href="{{route('user', ['userId' => $user_item->id])}}">Show
                                                info</a>

                                            @if(!$user_item->inContactList)

                                                <a class="btn btn-xs btn-primary"
                                                   href="{{action('ContactListController@addToContactList', [
                                                   'contact_list_user_id' => Auth::user()->id,
                                                   'userId' => $user_item->id ])}}">Add to contact list</a>

                                            @endif

                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>

                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

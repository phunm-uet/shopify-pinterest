@extends('layouts.app')
@include('includes.slidebar')
@section('content')
<div style="margin-left: 315px; margin-top:-15px; margin-bottom:20px;">
        <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#">Dashboard</a></li>
                <li class="active">Products</li>
        </ol>
        <div class="products">
            <table class="table table-hover table-reponsive">
                <thead>
                    <tr>
                        <th>No</th>
                        <th style="display: none;"></th>
                        <th>Collection Title</th>
                        <th>TimeOut (s)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($collections as $collection)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td style="display:none" class="collection_id">{{$collection['collection_id']}}</td>
                            <td>
                                <a href="{{$collection['collection_link']}}" target="_blank" class="collection_link">
                                    <span class="collection_title">{{str_limit($collection['collection_title'],50,'....')}}</span>
                                </a>
                            </td>
                            <td>{{$collection['timeout'] == 0 ? '---' : $collection['timeout']}}</td>
                            <td>{{$collection['status'] == 0 ? '---' : ($collection['status'] == 1 ? 'Running' : 'Stoped') }}</td>
                            <td>
                                @if($collection['status'] == 0)
                                    <a href="javascript:void(0)" class="btn btn-success btn-xs add_to_queue">Add To Queue</a>                                                           
                                @elseif ($collection['status'] == 1)
                                    <a href="javascript:void(0)" class="btn btn-danger btn-xs stop_queue">Stop</a> 
                                    <a href="javascript:void(0)" class="btn btn-info btn-xs config_queue">Config Timeout</a>
                                @else 
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs start_queue">Start</a>  
                                    <a href="javascript:void(0)" class="btn btn-info btn-xs config_queue">Config Timeout</a>                                   
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="modal fade" id="config-queue">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Config Timeout</h4>
                        </div>
                        <div class="modal-body">                    
                            <div>
                                <legend id="collection_title_modal"></legend>       
                                <div class="form-group">
                                    <label for="">Timeout(s)</label>
                                    <input type="hidden" id="collection_id_modal">
                                    <input type="hidden" id="status">
                                    <input type="number" class="form-control" id="timeout-setting" placeholder="3" required="required">
                                </div>
                                <button type="button" class="btn btn-primary btn-block" id="save-config">Save Config</button>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
</div>
@endsection

@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });        
        $('document').ready(function(){
            $('.add_to_queue, .config_queue').on('click',function(){
                var collectionId = $(this).parents('tr').children('.collection_id').text()
                var collectionTitle = $(this).parents('tr').find('.collection_title').text()
                $("#config-queue").modal();
                $("#collection_id_modal").val(collectionId);
                var btnText = $(this).text();
                $("#status").val(1);
                $("#collection_title_modal").text(collectionTitle);
            })
            
            $(".start_queue, .stop_queue").on('click',function(){
                var collectionId = $(this).parents('tr').children('.collection_id').text()
                var status = 0;
                var btnText = $(this).text();
                if(confirm("Are you sure?")){
                    if(btnText === 'Start') status = 1
                    else status = -1;
                    $.ajax({
                        'type': 'POST',
                        'url': '/api/update-queue',
                        'data': {
                            collection_id: collectionId,
                            status: status
                        }
                    }).done(function(data){
                        $("#config-queue").modal('hide')
                        location.reload();
                    }).fail(function(){
                        alert('Save Failed');
                        $("#config-queue").modal('hide')
                    })
                }
            })
            // Save config on modal 
            $("#save-config").on('click',function(){
                var collectionId = $("#collection_id_modal").val();
                var timeOut = $("#timeout-setting").val()
                var status = $("#status").val();
                $.ajax({
                    'type': 'POST',
                    'url': '/api/update-queue',
                    'data': {
                        collection_id: collectionId,
                        timeout: timeOut,
                        status: status
                    }
                }).done(function(data){
                    alert(data.message);
                    $("#config-queue").modal('hide')
                    location.reload();
                }).fail(function(){
                    alert('Save Failed');
                    $("#config-queue").modal('hide')
                })
            })
            $("#start_queue").on('click',function(){

            })
            $("#stop_queue").on('click',function(){

            })
            })

    </script>
@endsection
@extends('layouts.app')
@include('includes.slidebar')
@section('content')
<div style="margin-left: 315px; margin-top:-15px; margin-bottom:20px;">
        <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product_id</th>
                            <th>Product Title</th>
                            <th>Collection</th>
                            <th>Created at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $history)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$history->product_id}}</td>
                                <td><a href="{{$history->product_link}}" target="_blank">{{str_limit($history->product_title,100,'...')}}</a></td>
                                <td>{{$history->collection_id}}</a></td>
                                <td>{{$history->created_at}}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <?php echo $histories->render(); ?>
            </div>
</div>

    
@endsection
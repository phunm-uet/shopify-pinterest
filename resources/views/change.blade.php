@extends('layouts.app')
@include('includes.slidebar')
@section('content')
<div style="margin-left: 315px; margin-top:-15px; margin-bottom:20px;">
    <div class="col-md-8 col-md-offset-2">
        
        <form action="/post-change-password" method="POST" id="form">
            <legend>Change Password</legend>
            {{ csrf_field() }}
            
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Success!</strong> Update password sucess
                </div>
            @endif
            <div class="form-group">
                <label for="">Username</label>
                <input type="text" class="form-control" disabled value="{{Auth::user()->username}}">
            </div>
            <div class="form-group">
                <label for="">Username</label>
                <input type="text" class="form-control" value="{{Auth::user()->name}}" name="name">
            </div>

            <div class="form-group">
                <label for="">New Password</label>
                <input type="text" class="form-control" name="new_password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
        
    </div>
</div>
@endsection
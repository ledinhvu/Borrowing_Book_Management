@extends('admin.layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i> {{trans('labels.chart_borrow')}}</div>
        <div align="center" class="form-group">
            {{Form::select('year', $items, config('define.year'),['id'=>'yearborrow','class'=>'input input-sm'])}}
            {{Form::selectMonth('monthborrow',config('define.month'),['id'=>'monthborrow','class'=>'input input-sm'])}}
            <button type="button" id="showborrow" class="btn btn-sm btn-info">{{trans('labels.btnchart')}}</button>
        </div>
        <div id="chart" class="charts" align="center"></div>
    </div>
    <div class="panel panel-default">
        <div class="panel panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i> {{trans('labels.chart_user')}}</div>
        <div align="center" class="form-group">
            {{Form::select(null, $itemuser, null,['id'=>'year','class'=>'input input-sm'])}}
            <button type="button" id="show" class="btn btn-sm btn-info">{{trans('labels.btnchart')}}</button>
        </div>
        <div id="user" class="charts" align="center"></div>
    </div>
@endsection
@section('script')
    <script src="{{ url('backend/js/chart.js')}}"></script>
    <script type="text/javascript">
        var path_chart_user = {!! json_encode(config('path.path_chart_user')) !!};
        var path_chart_borrow = {!! json_encode(config('path.path_chart_borrow')) !!};
        var year = {!! json_encode(config('define.year')) !!};
        var month = {!! json_encode(config('define.month')) !!};
        var borrow = '{{ trans('labels.borrow') }}';
        var quantity = '{{ trans('labels.quantity') }}';
        var quantity_user = '{{ trans('labels.quantity_user') }}';
    </script> 
@endsection
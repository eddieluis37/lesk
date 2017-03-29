@extends('layouts.master')

@section('head_extra')
@endsection

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('ticket::general.page.show.box-title') }}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">

                    {!! Form::model($item, ['route' => 'ticket.index', 'method' => 'GET']) !!}

                        <div class="form-group">
                            {!! Form::label('name', trans('ticket::general.columns.name')) !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'readonly']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('ticket::general.columns.description')) !!}
                            {!! Form::text('description', null, ['class' => 'form-control', 'readonly']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::submit(trans('general.button.close'), ['class' => 'btn btn-primary']) !!}
                            @permission('ticket.edit-item')
                                <a href="{!! route('ticket.edit', $item->id) !!}" title="{{ trans('general.button.edit') }}" class='btn btn-default'>{{ trans('general.button.edit') }}</a>
                            @endpermission
                            @if ( $item->available )
                                @permission('ticket.sign-out')
                                    <a href="{!! route('ticket.sign-out', $item->id) !!}" title="{{ trans('ticket::general.button.sign-out') }}" class='btn btn-default'><i class="fa fa-sign-out fa-colour-green"></i>&nbsp;{{ trans('ticket::general.button.sign-out') }}</a>
                                @endpermission
                            @else
                                @permission('ticket.sign-in')
                                    <a href="{!! route('ticket.sign-in', $item->id) !!}" title="{{ trans('ticket::general.button.sign-in') }}" class='btn btn-default'><i class="fa fa-sign-in fa-colour-blue"></i>&nbsp;{{ trans('ticket::general.button.sign-in') }}</a>
                                @endpermission
                            @endif
                        </div>

                    {!! Form::close() !!}

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('ticket::general.columns.user_name') }}</th>
                                <th>{{ trans('ticket::general.columns.reason') }}</th>
                                <th>{{ trans('ticket::general.columns.from_date') }}</th>
                                <th>{{ trans('ticket::general.columns.to_date') }}</th>
                                <th>{{ trans('ticket::general.columns.return_date') }}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{ trans('ticket::general.columns.user_name') }}</th>
                                <th>{{ trans('ticket::general.columns.reason') }}</th>
                                <th>{{ trans('ticket::general.columns.from_date') }}</th>
                                <th>{{ trans('ticket::general.columns.to_date') }}</th>
                                <th>{{ trans('ticket::general.columns.return_date') }}</th>
                            </tr>
                            </tfoot>
                            <tbody>
                                @foreach($item->tickets as $res)
                                    <tr>
                                        <td>{{ $res->user->username }}</td>
                                        <td>{{ $res->reason }}</td>
                                        <td>{{ date('Y-m-d', strtotime($res->from_date)) }}</td>
                                        <td>{{ date('Y-m-d', strtotime($res->to_date)) }}</td>
                                        <td>{{ ($res->return_date)?date('Y-m-d', strtotime($res->return_date)):'' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
@endsection

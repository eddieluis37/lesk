@extends('layouts.master')

@section('head_extra')
@endsection

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('ticket::general.page.index.box-title') }}</h3>
                    &nbsp;
                    @permission('ticket.create-item')
                        <a class="btn btn-default btn-sm" href="{!! route('ticket.create') !!}" title="{{ trans('ticket::general.button.item.create') }}">
                            <i class="fa fa-plus-square"></i>
                        </a>
                    @endpermission

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <div class="box-body">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('ticket::general.columns.available') }}</th>
                                <th>{{ trans('ticket::general.columns.name') }}</th>
                                <th>{{ trans('ticket::general.columns.description') }}</th>
                                <th>{{ trans('ticket::general.columns.user_name') }}</th>
                                <th>{{ trans('ticket::general.columns.return_date') }}</th>
                                <th>{{ trans('ticket::general.columns.actions') }}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{ trans('ticket::general.columns.available') }}</th>
                                <th>{{ trans('ticket::general.columns.name') }}</th>
                                <th>{{ trans('ticket::general.columns.description') }}</th>
                                <th>{{ trans('ticket::general.columns.user_name') }}</th>
                                <th>{{ trans('ticket::general.columns.return_date') }}</th>
                                <th>{{ trans('ticket::general.columns.actions') }}</th>
                            </tr>
                            </tfoot>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            @if ( $item->available )
                                                <i class="fa fa-circle fa-colour-green" title="{{ trans('ticket::general.status.available') }}"></i>
                                            @else
                                                <i class="fa fa-times fa-colour-orange" title="{{ trans('ticket::general.status.unavailable') }}"></i>
                                            @endif
                                        </td>
                                        <td>{!! link_to_route('ticket.show', $item->name, [$item->id], []) !!}</td>
                                        <td>{{ $item->description }}</td>

                                        @if ( $item->available )
                                            <td></td>
                                            <td>
                                            </td>
                                        @else
                                            <td>
                                                {{ $item->latest_ticket()->user->username }}
                                            </td>
                                            <td>
                                                {{ $item->latest_ticket()->to_date->toDateString() }}
                                            </td>
                                        @endif

                                        <td>
                                            @if ( $item->available )
                                                @permission('ticket.sign-out')
                                                    <a href="{!! route('ticket.sign-out', $item->id) !!}" title="{{ trans('ticket::general.button.sign-out') }}"><i class="fa fa-sign-out fa-colour-green"></i></a>
                                                @endpermission
                                            @else
                                                @permission('ticket.sign-in')
                                                    <a href="{!! route('ticket.confirm-sign-in', $item->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('ticket::general.button.sign-in') }}"><i class="fa fa-sign-in fa-colour-blue"></i></a>
                                                @endpermission
                                            @endif

                                            @permission('ticket.edit-item')
                                                <a href="{!! route('ticket.edit', $item->id) !!}" title="{{ trans('general.button.edit') }}"><i class="fa fa-pencil-square-o"></i></a>
                                            @endpermission


                                                @permission('ticket.delete-item')
                                                <a href="{!! route('ticket.confirm-delete-item', $item->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
                                            @endpermission
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $items->render() !!}
                    </div> <!-- table-responsive -->

                </div><!-- /.box-body -->

            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
@endsection

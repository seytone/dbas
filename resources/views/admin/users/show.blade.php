@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.user.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
					<tr>
                        <th>
                            Rol
                        </th>
                        <td>
                            @foreach($user->roles()->pluck('name') as $role)
                                <span class="label label-info label-many">{{ $role }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.id') }}
                        </th>
                        <td>
                            {{ $user->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.name') }}
                        </th>
                        <td>
                            {{ $user->getFullname() }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <td>
                            {{ $user->email }}
                        </td>
                    </tr>
                    <tr class="{{ $user->hasRole('Vendedor') ? '' : 'd-none' }}">
                        <th>
                            Comisión Licencias Perpetuas
                        </th>
                        <td>
                            {{ $user->seller->commission_1 }}%
                        </td>
                    </tr>
                    <tr class="{{ $user->hasRole('Vendedor') ? '' : 'd-none' }}">
                        <th>
                            Comisión Suscripciones Anuales
                        </th>
                        <td>
                            {{ $user->seller->commission_2 }}%
                        </td>
                    </tr>
                    <tr class="{{ $user->hasRole('Vendedor') ? '' : 'd-none' }}">
                        <th>
                            Comisión Hardware y Otros
                        </th>
                        <td>
                            {{ $user->seller->commission_3 }}%
                        </td>
                    </tr>
                    <tr class="{{ $user->hasRole('Vendedor') ? '' : 'd-none' }}">
                        <th>
                            Comisión Servicios
                        </th>
                        <td>
                            {{ $user->seller->commission_4 }}%
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-dark" href="{{ url()->previous() }}">
                Regresar
            </a>
        </div>


    </div>
</div>
@endsection
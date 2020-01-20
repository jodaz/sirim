@extends('cruds.form')

@section('title', 'Registro de Roles')

@section('form')
  <!-- general form elements -->
  <div class="card card-primary">
    <div class="card-header alert alert-danger">
        @if ($typeForm == 'create')
            <h5 class="card-title">Registro de Roles</h5>

            @section('breadcrumbs')
                {{ Breadcrumbs::render('administration/roles/create') }}
            @endsection
		@else
			<h5>Editar rol: {{ @$row->name }}</h5>

            @section('breadcrumbs')
                {{ Breadcrumbs::render('roles.edit', $row) }}
            @endsection
		@endif
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    @if ($typeForm == 'create')
			{!! Form::open(['route' => "roles".'.store', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data',]) !!}
		@else
			{!! Form::model($row, ['route' => ["roles".'.update', $row->id], 'method' => 'patch', 'autocomplete' => 'off', 'role' => 'form', 'enctype' => 'multipart/form-data',]) !!}
		@endif
      <div class="card-body">
        <div class="row">
          <div class="form-group col-md-12">
            <label class="control-label"> Nombre <span class="text-danger">*</span></label>
            {!! Form::text("name", old('name', @$row->name), ["Placeholder" => "Nombre", "class" => "form-control", "onkeyup" => "upperCase(this);"]) !!}

            @error('name')
              <div class="text text-danger">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-12">
            <label class="control-label"> Slug <span class="text-danger">*</span></label>
            {!! Form::text("slug", old('slug', @$row->slug), ["Placeholder" => "Slug", "class" => "form-control"]) !!}

            @error('slug')
              <div class="text text-danger">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-12">
            <label class="control-label"> Descripción <span class="text-danger">*</span></label>
            {!! Form::text("description", old('description', @$row->description), ["Placeholder" => "Descripción", "class" => "form-control", "onkeyup" => "upperCase(this);"]) !!}

            @error('description')
              <div class="text text-danger">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-12">
            <label class="control-label"> Lista de Permisos <span class="text-danger">*</span></label>
            <ul class="list-unstyled">
              @foreach($permissions as $permission)
                <li>
                  <label>
                    {{ Form::checkbox('permissions[]', $permission->id, null) }}
                    {{ $permission->name }}
                    <em>({{ $permission->description ?: 'Sin descripción' }})</em>
                  </label>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
      <!-- /.card-body -->
      <div class="card-footer">
        <a href="{{ url()->previous() }}" class="btn btn-secondary" id="cancel"><i class="flaticon-reply"></i> Regresar</a>

        @if($typeForm == 'update')
          <button type="submit" class="btn btn-primary">
                <i class="flaticon2-refresh"></i>
                Actualizar
          </button>
        @else
          <button  type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            Registrar
          </button>
        @endif
      </div>
    {!! Form::close() !!}
  </div>
  <!-- /.card -->

@endsection

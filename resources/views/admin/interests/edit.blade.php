@extends("layouts.admin")

@section('title', __('messages.Edit_Interest'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.interests.index') }}">{{ __('messages.Interests') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Edit_Interest') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Edit_Interest') }}</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.interests.update', $interest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label>{{ __('messages.Name_AR') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                   value="{{ old('name_ar', $interest->name_ar) }}" required>
                            @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Name_EN') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                   value="{{ old('name_en', $interest->name_en) }}" required>
                            @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Icon') }}</label>
                            <input type="text" name="icon" class="form-control"
                                   value="{{ old('icon', $interest->icon) }}"
                                   placeholder="e.g. 🎵 or fa-music">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active"
                                       name="is_active" value="1"
                                       {{ old('is_active', $interest->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    {{ __('messages.Is_Active') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-save"></i> {{ __('messages.Update') }}
                            </button>
                            <a href="{{ route('admin.interests.index') }}" class="btn btn-secondary">
                                {{ __('messages.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

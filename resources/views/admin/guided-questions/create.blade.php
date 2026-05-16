@extends("layouts.admin")

@section('title', __('messages.New_Question'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.guided-questions.index') }}">{{ __('messages.Guided_Questions') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.New_Question') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.New_Question') }}</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.guided-questions.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>{{ __('messages.Question_Text') }} <span class="text-danger">*</span></label>
                            <textarea name="text_ar" class="form-control @error('text_ar') is-invalid @enderror"
                                      rows="4" required>{{ old('text_ar') }}</textarea>
                            @error('text_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Hint') }}</label>
                            <input type="text" name="hint_ar" class="form-control @error('hint_ar') is-invalid @enderror"
                                   value="{{ old('hint_ar') }}" maxlength="255">
                            @error('hint_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('messages.Category') }}</label>
                                    <input type="text" name="category_ar" class="form-control"
                                           value="{{ old('category_ar') }}" maxlength="60"
                                           placeholder="{{ __('messages.Category') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('messages.Sort_Order') }}</label>
                                    <input type="number" name="sort_order" class="form-control"
                                           value="{{ old('sort_order', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active"
                                       name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">{{ __('messages.Active') }}</label>
                            </div>
                        </div>

                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-save"></i> {{ __('messages.Submit') }}
                            </button>
                            <a href="{{ route('admin.guided-questions.index') }}" class="btn btn-secondary">
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

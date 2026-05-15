@extends("layouts.admin")

@section('title', __('messages.Edit_Question'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.guided-questions.index') }}">{{ __('messages.Guided_Questions') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.Edit_Question') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.Edit_Question') }}</h4>
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

                    <form action="{{ route('admin.guided-questions.update', $question->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label>{{ __('messages.Question_Text') }} <span class="text-danger">*</span></label>
                            <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror"
                                      rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('messages.Category') }}</label>
                                    <input type="text" name="category" class="form-control"
                                           value="{{ old('category', $question->category) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('messages.Sort_Order') }}</label>
                                    <input type="number" name="sort_order" class="form-control"
                                           value="{{ old('sort_order', $question->sort_order) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('messages.Version') }}</label>
                                    <input type="number" name="version" class="form-control"
                                           value="{{ old('version', $question->version) }}" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-save"></i> {{ __('messages.Update') }}
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

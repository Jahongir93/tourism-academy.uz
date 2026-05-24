@extends('layouts.dashboard-new')

@section('title', "O'quv rejani import qilish")
@section('page-title', "O'quv rejani import qilish")

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">O'quv rejani yuklash</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.academic.curriculum.doImport') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Ta'lim yo'nalishi <span class="text-danger">*</span></label>
                            <select name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                <option value="">Ta'lim yo'nalishini tanlang</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->code }} - {{ $program->name_uz }} ({{ ucfirst($program->level) }}, {{ ucfirst($program->education_form) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">O'quv yili <span class="text-danger">*</span></label>
                            <select name="academic_year" class="form-select @error('academic_year') is-invalid @enderror" required>
                                <option value="">O'quv yilini tanlang</option>
                                @php
                                    $currentYear = date('Y');
                                    for ($i = -1; $i <= 2; $i++) {
                                        $year = $currentYear + $i;
                                        $yearStr = $year . '-' . ($year + 1);
                                        $selected = old('academic_year') == $yearStr ? 'selected' : '';
                                        echo "<option value=\"$yearStr\" $selected>$yearStr</option>";
                                    }
                                @endphp
                            </select>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Excel fayl <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" 
                                   accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Qo'llab-quvvatlanadigan formatlar: .xlsx, .xls, .csv</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Import qilish qoidalari:</h6>
                            <ul class="mb-0 small">
                                <li>Excel fayl quyidagi ustunlarni o'z ichiga olishi kerak: Semestr, Fan kodi, Fan nomi, Ma'ruza soatlari, Amaliyot soatlari, Seminar soatlari, Laboratoriya soatlari, Mustaqil ta'lim soatlari, Kreditlar, Fan turi</li>
                                <li>Fan kodlari bazada mavjud fanlar bilan mos kelishi kerak</li>
                                <li>Fan turi: majburiy yoki tanlov bo'lishi kerak</li>
                                <li>Agar mavjud o'quv reja bo'lsa, u yangilanadi</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('structure.academic.curriculum.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Import qilish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Namuna fayl</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">O'quv rejani to'g'ri import qilish uchun namuna faylni yuklab oling va uni to'ldiring.</p>
                    
                    <a href="#" class="btn btn-outline-primary w-100 mb-3">
                        <i class="fas fa-download me-1"></i> Namuna faylni yuklab olish
                    </a>
                    
                    <h6>Excel fayl tuzilishi:</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Ustun</th>
                                <th>Tavsif</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td>A</td>
                                <td>Semestr (1-8)</td>
                            </tr>
                            <tr>
                                <td>B</td>
                                <td>Fan kodi</td>
                            </tr>
                            <tr>
                                <td>C</td>
                                <td>Fan nomi</td>
                            </tr>
                            <tr>
                                <td>D</td>
                                <td>Ma'ruza soatlari</td>
                            </tr>
                            <tr>
                                <td>E</td>
                                <td>Amaliyot soatlari</td>
                            </tr>
                            <tr>
                                <td>F</td>
                                <td>Seminar soatlari</td>
                            </tr>
                            <tr>
                                <td>G</td>
                                <td>Laboratoriya soatlari</td>
                            </tr>
                            <tr>
                                <td>H</td>
                                <td>Mustaqil ta'lim</td>
                            </tr>
                            <tr>
                                <td>I</td>
                                <td>Kreditlar</td>
                            </tr>
                            <tr>
                                <td>J</td>
                                <td>Fan turi (majburiy/tanlov)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
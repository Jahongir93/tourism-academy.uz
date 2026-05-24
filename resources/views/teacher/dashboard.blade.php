@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">O'qituvchi kabineti</h1>
            <p class="text-muted">Xush kelibsiz, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- Statistika kartlari -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Mening guruhlarim
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalGroups ?? 5 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jami talabalar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalStudents ?? 124 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bugungi darslar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $todayLessons ?? 4 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tekshirilmagan ishlar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $pendingAssignments ?? 12 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biriktirilgan fanlar va guruhlar -->
    @if(isset($assignments) && $assignments->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-book-reader mr-2"></i>Menga biriktirilgan fanlar va guruhlar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($assignments as $assignment)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-primary mb-2">
                                        <i class="fas fa-book mr-2"></i>
                                        {{ $assignment['subject']->name_uz ?? $assignment['subject']->name ?? 'N/A' }}
                                    </h6>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $assignment['academic_year']->name ?? 'N/A' }} |
                                            {{ $assignment['semester'] ?? 1 }}-semestr
                                        </small>
                                    </div>
                                    <div class="mt-2">
                                        <strong class="text-dark"><i class="fas fa-users mr-1"></i>Guruhlar:</strong>
                                        <div class="mt-1">
                                            @forelse($assignment['groups'] as $group)
                                                <a href="{{ route('teacher.journal.index', ['group' => $group->id]) }}"
                                                   class="badge badge-info mr-1 mb-1" style="font-size: 0.85em;">
                                                    {{ $group->name }}
                                                </a>
                                            @empty
                                                <span class="text-muted">Guruh biriktirilmagan</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light py-2">
                                    <a href="{{ route('teacher.journal.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-journal-whills mr-1"></i>Jurnalga o'tish
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                Sizga hali fan va guruh biriktirilmagan. Administrator bilan bog'laning.
            </div>
        </div>
    </div>
    @endif

    <!-- Asosiy modullar -->
    <div class="row">
        <!-- LMS moduli -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-laptop-code mr-2"></i>LMS Moduli
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">O'quv materiallari va onlayn darslar</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.lms.materials') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-book mr-2 text-primary"></i>Dars materiallari
                        </a>
                        <a href="{{ route('teacher.lms.videos') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-video mr-2 text-danger"></i>Video darslar
                        </a>
                        <a href="{{ route('teacher.lms.tests') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tasks mr-2 text-success"></i>Onlayn testlar
                        </a>
                        <a href="{{ route('teacher.lms.upload') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-upload mr-2 text-info"></i>Material yuklash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guruhlar moduli -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-users mr-2"></i>Guruhlar
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Mening guruhlarim va talabalar</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.groups.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list mr-2 text-primary"></i>Barcha guruhlar
                        </a>
                        <a href="{{ route('teacher.groups.students') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-friends mr-2 text-success"></i>Talabalar ro'yxati
                        </a>
                        <a href="{{ route('teacher.groups.messages') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-envelope mr-2 text-warning"></i>Xabar yuborish
                        </a>
                        <a href="{{ route('teacher.groups.statistics') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-pie mr-2 text-info"></i>Statistika
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jurnal moduli -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-book-open mr-2"></i>Elektron Jurnal
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Elektron jurnal va baholar</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.journal.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-book mr-2 text-primary"></i>Jurnal ko'rish
                        </a>
                        <a href="{{ route('teacher.journal.grades') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-pen mr-2 text-success"></i>Baho qo'yish
                        </a>
                        <a href="{{ route('teacher.journal.topics') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list-alt mr-2 text-warning"></i>Mavzular kiritish
                        </a>
                        <a href="{{ route('teacher.journal.export') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-export mr-2 text-danger"></i>Eksport qilish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Davomat moduli -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>Davomat
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Davomat belgilash va hisobot</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.attendance.mark') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-check-circle mr-2 text-success"></i>Davomat belgilash
                        </a>
                        <a href="{{ route('teacher.attendance.today') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-day mr-2 text-primary"></i>Bugungi davomat
                        </a>
                        <a href="{{ route('teacher.attendance.history') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-history mr-2 text-info"></i>Davomat tarixi
                        </a>
                        <a href="{{ route('teacher.attendance.report') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar mr-2 text-warning"></i>Hisobot
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- O'quv reja -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-graduation-cap mr-2"></i>O'quv Reja
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Fan bo'yicha o'quv reja</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.curriculum.view') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-eye mr-2 text-primary"></i>Rejani ko'rish
                        </a>
                        <a href="{{ route('teacher.curriculum.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus mr-2 text-success"></i>Reja yaratish
                        </a>
                        <a href="{{ route('teacher.curriculum.edit') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit mr-2 text-warning"></i>Tahrirlash
                        </a>
                        <a href="{{ route('teacher.curriculum.materials') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-upload mr-2 text-info"></i>Materiallar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vedmost moduli -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-file-invoice mr-2"></i>Vedmost
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Vedmost to'ldirish va topshirish</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.vedomost.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus-circle mr-2 text-success"></i>Yangi vedmost
                        </a>
                        <a href="{{ route('teacher.vedomost.list') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list mr-2 text-primary"></i>Vedomostlar
                        </a>
                        <a href="{{ route('teacher.vedomost.fill') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-pen-alt mr-2 text-warning"></i>To'ldirish
                        </a>
                        <a href="{{ route('teacher.vedomost.submit') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-paper-plane mr-2 text-danger"></i>Topshirish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadvallar qatori -->
    <div class="row mt-4">
        <!-- Bugungi dars jadvali -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt mr-2"></i>Bugungi dars jadvali
                        <small class="float-right text-white-50">{{ now()->format('d.m.Y') }} - {{ ['Yakshanba', 'Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'][now()->dayOfWeek] }}</small>
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($todaySchedule) && count($todaySchedule) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vaqt</th>
                                    <th>Guruh</th>
                                    <th>Fan</th>
                                    <th>Xona</th>
                                    <th>Turi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaySchedule as $lesson)
                                <tr>
                                    <td><strong>{{ $lesson['time'] }}</strong></td>
                                    <td>{{ $lesson['group'] }}</td>
                                    <td>{{ $lesson['subject'] }}</td>
                                    <td>{{ $lesson['room'] }}</td>
                                    <td>
                                        @if(($lesson['type'] ?? 'lecture') == 'lecture')
                                            <span class="badge badge-primary">Ma'ruza</span>
                                        @elseif(($lesson['type'] ?? '') == 'practice')
                                            <span class="badge badge-success">Amaliyot</span>
                                        @elseif(($lesson['type'] ?? '') == 'lab')
                                            <span class="badge badge-info">Laboratoriya</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $lesson['type'] ?? 'Dars' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Bugun dars jadvali mavjud emas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Oxirgi topshiriqlar -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks mr-2"></i>Tekshirilishi kerak bo'lgan ishlar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Talaba</th>
                                    <th>Guruh</th>
                                    <th>Topshiriq</th>
                                    <th>Muddat</th>
                                    <th>Harakat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Aliyev A.</td>
                                    <td>TUR-201</td>
                                    <td>Referat</td>
                                    <td><span class="text-danger">Bugun</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Karimova D.</td>
                                    <td>TUR-202</td>
                                    <td>Prezentatsiya</td>
                                    <td><span class="text-warning">Ertaga</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Toshmatov B.</td>
                                    <td>TUR-301</td>
                                    <td>Kurs ishi</td>
                                    <td>3 kun</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tezkor harakatlar -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-rocket mr-2"></i>Tezkor harakatlar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#quickAttendance">
                                <i class="fas fa-clipboard-check mr-2"></i>Tez davomat
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#quickGrade">
                                <i class="fas fa-star mr-2"></i>Tez baho qo'yish
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-info btn-block" data-toggle="modal" data-target="#quickAssignment">
                                <i class="fas fa-file-alt mr-2"></i>Topshiriq berish
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-warning btn-block" data-toggle="modal" data-target="#quickMessage">
                                <i class="fas fa-envelope mr-2"></i>Xabar yuborish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Attendance Modal -->
<div class="modal fade" id="quickAttendance" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tez davomat belgilash</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>Guruhni tanlang</label>
                        <select class="form-control">
                            <option>TUR-201</option>
                            <option>TUR-202</option>
                            <option>TUR-301</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dars turi</label>
                        <select class="form-control">
                            <option>Ma'ruza</option>
                            <option>Amaliyot</option>
                            <option>Laboratoriya</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary">Davom etish</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Grade Modal -->
<div class="modal fade" id="quickGrade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tez baho qo'yish</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>Guruh</label>
                        <select class="form-control">
                            <option>TUR-201</option>
                            <option>TUR-202</option>
                            <option>TUR-301</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Talaba</label>
                        <select class="form-control">
                            <option>Tanlang...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Baho turi</label>
                        <select class="form-control">
                            <option>Joriy nazorat</option>
                            <option>Oraliq nazorat</option>
                            <option>Yakuniy nazorat</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Baho</label>
                        <input type="number" class="form-control" min="0" max="100">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-success">Saqlash</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Dashboard yangilanishi
function refreshDashboard() {
    fetch('/teacher/api/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            console.log('Dashboard updated:', data);
        });
}

// Har 60 sekundda yangilash
setInterval(refreshDashboard, 60000);
</script>
@endsection
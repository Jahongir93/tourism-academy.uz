@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-question-circle me-2"></i>
                        Yordam markazi
                    </h4>
                    <p class="mb-0 opacity-75">Tez-tez so'raladigan savollar va qo'llanmalar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- FAQ Section -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-question me-2 text-primary"></i>
                        Tez-tez so'raladigan savollar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Baholarni qanday qo'yaman?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Chap menuda <strong>"Baholar"</strong> bo'limiga kiring</li>
                                        <li>O'zingiz dars o'tadigan guruhni tanlang</li>
                                        <li><strong>"Baho qo'shish"</strong> tugmasini bosing</li>
                                        <li>Sana, mavzu va nazorat turini tanlang</li>
                                        <li>Har bir talabaga baho qo'ying (0-100 ball)</li>
                                        <li><strong>"Saqlash"</strong> tugmasini bosing</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Davomatni qanday belgilayman?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Chap menuda <strong>"Davomat"</strong> bo'limiga kiring</li>
                                        <li>Guruh va fanni tanlang</li>
                                        <li><strong>"Davomat belgilash"</strong> tugmasini bosing</li>
                                        <li>Darsga kelgan talabalarni belgilang</li>
                                        <li><strong>"Saqlash"</strong> tugmasini bosing</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Topshiriq qanday beraman?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Chap menuda <strong>"Topshiriqlar"</strong> bo'limiga kiring</li>
                                        <li><strong>"Yangi topshiriq"</strong> tugmasini bosing</li>
                                        <li>Fan va guruhni tanlang</li>
                                        <li>Topshiriq sarlavhasi va tavsifini yozing</li>
                                        <li>Muddatni belgilang</li>
                                        <li>Kerak bo'lsa fayl biriktiring</li>
                                        <li><strong>"Saqlash"</strong> tugmasini bosing</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    O'quv materiallarni qanday yuklayman?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Chap menuda <strong>"Online Ta'lim"</strong> bo'limiga kiring</li>
                                        <li><strong>"Yangi material"</strong> tugmasini bosing</li>
                                        <li>Material turini tanlang (PDF, video, prezentatsiya)</li>
                                        <li>Faylni yuklang yoki link kiriting</li>
                                        <li>Fan va guruhni tanlang</li>
                                        <li><strong>"Saqlash"</strong> tugmasini bosing</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Baholash tizimlari (ball) qanday hisoblanadi?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Ball</th>
                                                <th>Baho</th>
                                                <th>Natija</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-success">
                                                <td>86-100</td>
                                                <td>A (5)</td>
                                                <td>A'lo</td>
                                            </tr>
                                            <tr class="table-primary">
                                                <td>71-85</td>
                                                <td>B (4)</td>
                                                <td>Yaxshi</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td>56-70</td>
                                                <td>C (3)</td>
                                                <td>Qoniqarli</td>
                                            </tr>
                                            <tr class="table-danger">
                                                <td>0-55</td>
                                                <td>F (2)</td>
                                                <td>Qoniqarsiz</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & Quick Links -->
        <div class="col-lg-4 mb-4">
            <!-- Contact Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-phone-alt me-2 text-success"></i>
                        Bog'lanish
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Texnik yordam:</h6>
                        <p class="mb-0">
                            <i class="fas fa-phone me-2"></i>+998 66 233-30-30
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Email:</h6>
                        <p class="mb-0">
                            <i class="fas fa-envelope me-2"></i>support@tourism.uz
                        </p>
                    </div>
                    <div class="mb-0">
                        <h6 class="text-muted mb-1">Telegram:</h6>
                        <p class="mb-0">
                            <i class="fab fa-telegram me-2"></i>@hemis_support
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2 text-info"></i>
                        Tezkor havolalar
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.grades.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-star me-2 text-warning"></i>Baholar
                        </a>
                        <a href="{{ route('teacher.attendance.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-clipboard-list me-2 text-success"></i>Davomat
                        </a>
                        <a href="{{ route('teacher.assignments.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tasks me-2 text-primary"></i>Topshiriqlar
                        </a>
                        <a href="{{ route('teacher.profile') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2 text-secondary"></i>Profilim
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.accordion-button:not(.collapsed) {
    background-color: #f0f3ff;
    color: #5e72e4;
}
.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}
</style>
@endsection

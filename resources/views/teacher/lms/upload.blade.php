@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Material yuklash</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Yangi material qo'shish</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.lms.store-upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="title">Material nomi</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Fan</label>
                            <select class="form-control" id="subject" name="subject" required>
                                <option value="">Tanlang...</option>
                                <option value="turizm">Turizm asoslari</option>
                                <option value="geografiya">Turizm geografiyasi</option>
                                <option value="iqtisod">Turizm iqtisodiyoti</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="group">Guruh</label>
                            <select class="form-control" id="group" name="group" required>
                                <option value="">Tanlang...</option>
                                <option value="TUR-201">TUR-201</option>
                                <option value="TUR-202">TUR-202</option>
                                <option value="TUR-301">TUR-301</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type">Material turi</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Tanlang...</option>
                                <option value="lecture">Ma'ruza</option>
                                <option value="presentation">Prezentatsiya</option>
                                <option value="document">Hujjat</option>
                                <option value="video">Video</option>
                                <option value="test">Test</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Tavsif</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="file">Faylni tanlang</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
                            <small class="form-text text-muted">
                                Ruxsat berilgan formatlar: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, MP4, AVI
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload mr-2"></i>Yuklash
                        </button>
                        <a href="{{ route('teacher.lms.materials') }}" class="btn btn-secondary">
                            Bekor qilish
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">Qo'llanma</h6>
                </div>
                <div class="card-body">
                    <h6>Yuklash tartibi:</h6>
                    <ol class="small">
                        <li>Material nomini kiriting</li>
                        <li>Tegishli fanni tanlang</li>
                        <li>Guruhni tanlang</li>
                        <li>Material turini belgilang</li>
                        <li>Qisqacha tavsif yozing</li>
                        <li>Faylni tanlang va yuklang</li>
                    </ol>

                    <hr>

                    <h6>Fayl hajmi chegaralari:</h6>
                    <ul class="small">
                        <li>Hujjatlar: maksimal 10 MB</li>
                        <li>Prezentatsiyalar: maksimal 25 MB</li>
                        <li>Videolar: maksimal 100 MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
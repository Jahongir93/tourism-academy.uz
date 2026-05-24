<!-- Attendance Statistics Widget -->
<div class="card h-100" style="border-radius: 12px; border: 1px solid #e1e8ed; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-header" style="background: linear-gradient(135deg, #0d4f3c 0%, #16a085 100%); border-radius: 12px 12px 0 0; border: none;">
        <h5 class="mb-0 text-white">
            <i class="fas fa-chart-pie"></i> Bugungi davomat statistikasi
        </h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-4">
                <div class="p-2">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2"
                         style="width: 48px; height: 48px; background: #e8f5f0; border-radius: 10px;">
                        <i class="fas fa-user-check fs-5" style="color: #16a085;"></i>
                    </div>
                    <h3 class="mt-2" style="color: #2c3e50;">{{ $todayAttendance['present'] ?? 0 }}</h3>
                    <small class="text-muted">Keldi</small>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2"
                         style="width: 48px; height: 48px; background: #f0f9f6; border-radius: 10px;">
                        <i class="fas fa-user-clock fs-5" style="color: #f39c12;"></i>
                    </div>
                    <h3 class="mt-2" style="color: #2c3e50;">{{ $todayAttendance['late'] ?? 0 }}</h3>
                    <small class="text-muted">Kechikdi</small>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2"
                         style="width: 48px; height: 48px; background: #fef0f0; border-radius: 10px;">
                        <i class="fas fa-user-times fs-5" style="color: #e74c3c;"></i>
                    </div>
                    <h3 class="mt-2" style="color: #2c3e50;">{{ $todayAttendance['absent'] ?? 0 }}</h3>
                    <small class="text-muted">Kelmadi</small>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        @if(($todayAttendance['total'] ?? 0) > 0)
            <div class="mt-3">
                @php
                    $presentPercent = round(($todayAttendance['present'] / $todayAttendance['total']) * 100);
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <small style="color: #2c3e50; font-weight: 500;">Davomat foizi</small>
                    <small style="color: #16a085; font-weight: bold;">{{ $presentPercent }}%</small>
                </div>
                <div class="progress" style="height: 20px; background: #e8f5f0; border-radius: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $presentPercent }}%; background: linear-gradient(90deg, #16a085 0%, #48c9b0 100%); border-radius: 8px;">
                        {{ $presentPercent }}%
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Check-ins -->
        @if($recentCheckIns ?? false)
            <hr style="border-color: #e8f5f0;">
            <h6 style="color: #2c3e50; font-weight: 600;">Oxirgi kirishlar</h6>
            <div class="recent-checkins">
                @foreach($recentCheckIns as $checkIn)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2" style="border-color: #e8f5f0 !important;">
                        <div>
                            <strong style="color: #2c3e50;">{{ $checkIn['user_name'] }}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-clock" style="color: #16a085;"></i> {{ $checkIn['check_in_time'] }}
                            </small>
                        </div>
                        <div>
                            @if($checkIn['status'] == 'present')
                                <span class="badge" style="background: #e8f5f0; color: #16a085; border-radius: 6px; padding: 6px 12px;">O'z vaqtida</span>
                            @elseif($checkIn['status'] == 'late')
                                <span class="badge" style="background: #fff3cd; color: #f39c12; border-radius: 6px; padding: 6px 12px;">Kechikdi</span>
                            @else
                                <span class="badge" style="background: #fef0f0; color: #e74c3c; border-radius: 6px; padding: 6px 12px;">Juda kech</span>
                            @endif
                            @if($checkIn['confidence'])
                                <br>
                                <small class="text-muted">{{ round($checkIn['confidence']) }}%</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-3">
            <a href="{{ route('attendance.reports') }}" class="btn btn-sm" style="background: #0d4f3c; color: white; border-radius: 8px; padding: 8px 20px; border: none; transition: all 0.3s;"
               onmouseover="this.style.background='#16a085'" onmouseout="this.style.background='#0d4f3c'">
                <i class="fas fa-chart-line"></i> To'liq hisobot
            </a>
        </div>
    </div>
</div>
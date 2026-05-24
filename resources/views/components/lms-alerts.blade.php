<!-- Success Alert -->
@if(session('success'))
<div class="lms-alert lms-alert-success lms-fade-in-up mb-6" role="alert">
    <div class="flex-shrink-0">
        <i class="fas fa-check-circle text-2xl"></i>
    </div>
    <div class="ml-4 flex-1">
        <h4 class="font-semibold mb-1">Muvaffaqiyatli!</h4>
        <p class="text-sm">{{ session('success') }}</p>
    </div>
    <button type="button" class="ml-4 flex-shrink-0" onclick="this.parentElement.remove()">
        <i class="fas fa-times text-lg hover:text-green-800"></i>
    </button>
</div>
@endif

<!-- Error Alert -->
@if(session('error'))
<div class="lms-alert lms-alert-danger lms-fade-in-up mb-6" role="alert">
    <div class="flex-shrink-0">
        <i class="fas fa-exclamation-circle text-2xl"></i>
    </div>
    <div class="ml-4 flex-1">
        <h4 class="font-semibold mb-1">Xatolik!</h4>
        <p class="text-sm">{{ session('error') }}</p>
    </div>
    <button type="button" class="ml-4 flex-shrink-0" onclick="this.parentElement.remove()">
        <i class="fas fa-times text-lg hover:text-red-800"></i>
    </button>
</div>
@endif

<!-- Warning Alert -->
@if(session('warning'))
<div class="lms-alert lms-alert-warning lms-fade-in-up mb-6" role="alert">
    <div class="flex-shrink-0">
        <i class="fas fa-exclamation-triangle text-2xl"></i>
    </div>
    <div class="ml-4 flex-1">
        <h4 class="font-semibold mb-1">Ogohlantirish!</h4>
        <p class="text-sm">{{ session('warning') }}</p>
    </div>
    <button type="button" class="ml-4 flex-shrink-0" onclick="this.parentElement.remove()">
        <i class="fas fa-times text-lg hover:text-yellow-800"></i>
    </button>
</div>
@endif

<!-- Info Alert -->
@if(session('info'))
<div class="lms-alert lms-alert-info lms-fade-in-up mb-6" role="alert">
    <div class="flex-shrink-0">
        <i class="fas fa-info-circle text-2xl"></i>
    </div>
    <div class="ml-4 flex-1">
        <h4 class="font-semibold mb-1">Ma'lumot!</h4>
        <p class="text-sm">{{ session('info') }}</p>
    </div>
    <button type="button" class="ml-4 flex-shrink-0" onclick="this.parentElement.remove()">
        <i class="fas fa-times text-lg hover:text-cyan-800"></i>
    </button>
</div>
@endif

<!-- Validation Errors -->
@if($errors->any())
<div class="lms-alert lms-alert-danger lms-fade-in-up mb-6" role="alert">
    <div class="flex-shrink-0">
        <i class="fas fa-exclamation-circle text-2xl"></i>
    </div>
    <div class="ml-4 flex-1">
        <h4 class="font-semibold mb-2">Ma'lumotlarni tekshirishda xatoliklar!</h4>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="ml-4 flex-shrink-0" onclick="this.parentElement.remove()">
        <i class="fas fa-times text-lg hover:text-red-800"></i>
    </button>
</div>
@endif

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.lms-alert').forEach(function(alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(function() {
                alert.remove();
            }, 300);
        });
    }, 5000);
</script>

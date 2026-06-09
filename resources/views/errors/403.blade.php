<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غير مصرح - 403</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-lg w-full text-center px-6">
        @if(auth()->check() && !auth()->user()->is_active)
            <div class="mb-8">
                <div class="mx-auto h-24 w-24 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-6">
                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">حسابك قيد المراجعة</h1>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                مرحباً بك <span class="font-bold">{{ auth()->user()->username }}</span>. حسابك حالياً قيد المراجعة من قبل الإدارة. يرجى الانتظار حتى يتم تفعيله لتتمكن من الدخول إلى لوحة التحكم الخاصة بك.
            </p>
        @else
            <div class="mb-8">
                <div class="mx-auto h-24 w-24 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-6">
                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">403 - غير مصرح</h1>
            <p class="text-lg text-gray-600 mb-8">
                عذراً، ليس لديك الصلاحية للوصول إلى هذه الصفحة.
            </p>
        @endif
        
        <div class="flex flex-col gap-3 justify-center items-center">
            <a href="/" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all">
                العودة للصفحة الرئيسية
            </a>
            
            @if(auth()->check())
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-900 underline underline-offset-4 text-sm mt-4">
                        تسجيل الخروج
                    </button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>

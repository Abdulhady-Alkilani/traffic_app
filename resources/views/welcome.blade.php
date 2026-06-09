@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden min-h-[70vh] flex items-center">
    {{-- Decorative elements --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 start-10 w-72 h-72 bg-indigo-400/10 dark:bg-indigo-400/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-10 end-10 w-96 h-96 bg-purple-400/10 dark:bg-purple-400/5 rounded-full blur-3xl animate-float stagger-3"></div>
        <div class="absolute top-1/2 start-1/2 w-64 h-64 bg-cyan-400/5 rounded-full blur-3xl animate-float stagger-5"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 w-full">
        <div class="text-center max-w-4xl mx-auto">
            {{-- Official Badge --}}
            <div class="animate-fade-in-down inline-flex items-center gap-2 px-5 py-2 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200/50 dark:border-indigo-800/50 text-indigo-600 dark:text-indigo-400 text-sm font-medium mb-8">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                منصة رسمية لإدارة السلامة المرورية
            </div>

            {{-- Title --}}
            <h1 class="animate-fade-in-up text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-6">
                <span class="text-gray-900 dark:text-white">النظام الوطني</span>
                <br>
                <span class="gradient-text">للسلامة المرورية</span>
            </h1>

            {{-- Subtitle --}}
            <p class="animate-fade-in-up stagger-1 text-base md:text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                منصة إلكترونية متكاملة تهدف لتعزيز السلامة على الطرق من خلال تقديم البلاغات المرورية، متابعة المخالفات، وإدارة بيانات المركبات بشكل رقمي وآمن.
            </p>

            {{-- CTA Buttons --}}
            <div class="animate-fade-in-up stagger-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    @if(auth()->user()->isCitizen())
                        <a href="{{ route('citizen.dashboard') }}" class="group flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 hover:-translate-y-0.5 btn-shine">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            {{ __('messages.dashboard') }}
                            <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                        <a href="{{ route('citizen.reports.create') }}" class="group flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-gray-900 dark:text-white bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-700 shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                            <svg class="w-5 h-5 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            {{ __('messages.new_report') }}
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a href="/admin" class="flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 btn-shine">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ __('messages.admin_panel') }}
                        </a>
                    @endif

                    @if(auth()->user()->isPolice())
                        <a href="/police" class="flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 btn-shine">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                            {{ __('messages.police_panel') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="group flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 hover:-translate-y-0.5 btn-shine">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        {{ __('messages.login') }}
                        <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <a href="{{ route('register') }}" class="group flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-gray-900 dark:text-white bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700 shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                        <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                        </svg>
                        {{ __('messages.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- Services Section --}}
<section class="relative py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                الخدمات المقدمة
            </h2>
            <div class="w-20 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full mx-auto mb-4"></div>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">خدمات إلكترونية متكاملة لتسهيل التواصل بين المواطنين والجهات الأمنية المختصة</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            {{-- Service 1 - Report Submission --}}
            <div class="group glass-card rounded-2xl p-8 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-500 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">تقديم البلاغات المرورية</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">أبلغ عن الحوادث والمخالفات المرورية إلكترونياً مع تحديد الموقع والتوجيه التلقائي للقسم المختص بمعالجة البلاغ.</p>
            </div>

            {{-- Service 2 - Violation Tracking --}}
            <div class="group glass-card rounded-2xl p-8 hover:shadow-xl hover:shadow-purple-500/10 transition-all duration-500 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/20 mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">استعلام عن المخالفات</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">اطّلع على سجل المخالفات المرورية المسجلة على مركباتك، وتابع حالتها وقيمة الغرامات المالية المترتبة عليها.</p>
            </div>

            {{-- Service 3 - Vehicle Management --}}
            <div class="group glass-card rounded-2xl p-8 hover:shadow-xl hover:shadow-cyan-500/10 transition-all duration-500 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">إدارة المركبات</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">سجّل مركباتك إلكترونياً واحتفظ بسجل رقمي شامل يتضمن بيانات كل مركبة من نوعها ولوحتها وسنة التصنيع.</p>
            </div>
        </div>
    </div>
</section>

{{-- How it Works Section --}}
<section class="relative py-20 bg-gradient-to-b from-transparent via-indigo-50/30 dark:via-indigo-950/10 to-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                كيف يعمل النظام؟
            </h2>
            <div class="w-20 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full mx-auto mb-4"></div>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">خطوات بسيطة للاستفادة من خدمات النظام</p>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            {{-- Step 1 --}}
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 mb-5 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-bold text-xl">1</span>
                </div>
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">إنشاء حساب</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">سجّل حسابك كمواطن أو شرطي وأدخل بياناتك الشخصية</p>
            </div>

            {{-- Step 2 --}}
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/20 mb-5 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-bold text-xl">2</span>
                </div>
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">تسجيل المركبات</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">أضف بيانات مركباتك لربطها بحسابك الشخصي</p>
            </div>

            {{-- Step 3 --}}
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 mb-5 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-bold text-xl">3</span>
                </div>
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">تقديم البلاغات</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">أبلغ عن أي حادث مروري وسيتم توجيهه للقسم المعني</p>
            </div>

            {{-- Step 4 --}}
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20 mb-5 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-bold text-xl">4</span>
                </div>
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">متابعة الحالة</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">تابع حالة بلاغاتك ومخالفاتك بشكل لحظي من لوحة التحكم</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="relative py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card rounded-2xl p-10 md:p-14 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 pointer-events-none"></div>
            <div class="relative">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25 mb-6">
                    <svg class="w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">ساهم في تعزيز السلامة المرورية</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-xl mx-auto leading-relaxed">
                    سلامتك وسلامة الآخرين على الطرق مسؤولية مشتركة. سجّل الآن وكن جزءاً فعّالاً في منظومة السلامة المرورية الوطنية.
                </p>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 hover:-translate-y-0.5 btn-shine">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                        </svg>
                        أنشئ حسابك الآن
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection

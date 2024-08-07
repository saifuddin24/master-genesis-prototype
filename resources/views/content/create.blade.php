<x-app-layout>

    <x-slot name="title">content.create || Master Genesis Prototype v1</x-slot>
    <x-slot name="heading">content.create</x-slot>

    <form 
        class="w-full mx-auto max-w-4xl p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 space-y-6" 
        action="{{ route('contents.store')}}" 
        method="POST"
    >
        <h5 class="text-xl font-medium text-gray-900 dark:text-white text-center">Lecture Video/Exam assign on Session</h5>
        @method('POST')
        @csrf
        
        @include('content.form')

    </form>

    @section('scripts')
        <!-- Your script here -->
    @endsection

</x-app-layout>
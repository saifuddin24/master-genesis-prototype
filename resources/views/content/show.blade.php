<x-app-layout>

    <x-slot name="title">content.show || Master Genesis Prototype v1</x-slot>
    <x-slot name="heading">content.show</x-slot>

    <div>
        My Page content is here 
    </div>

    @section('scripts')
        <!-- Your script here -->
    @endsection

</x-app-layout>
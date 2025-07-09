@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:people.id-creator :schoolIdCard="$idCard" />
    </div>
@endsection
@push('scripts')
    <script>
        Livewire.on('id-card-saved', (e) =>
        {
            axios.post('{{ route('people.school-ids.manage.both.update', [$role, $campus]) }}', {school_id: e.idCard})
                .then(response => {
                    console.log(response);
                })
                .catch(error => {
                    alert('There was an error saving the ID card.')
                    console.log(error);
                });
        });
    </script>
@endpush

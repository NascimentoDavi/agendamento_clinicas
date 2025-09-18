@extends('layouts.app_adm')

@section('title', 'Calendário')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/menu-calendar/app.css'])
@endsection

@section('content')

    <div class="container mw-100">
        
        <div class="row">
            
            <x-page-title>
                <span id="button-novo-agendamento-header" onclick="window.location.href = '/aluno/criar-agendamento'" class="btn btn-success">
                    <span>Novo Agendamento</span>
                </span>
            </x-page-title>

            <div class="col-12 shadow-lg shadow-dark pt-3 me-3 me-sm-0 bg-body-tertiary rounded mt-3 mt-md-0">
                <!-- CALENDÁRIO -->
                <div id="calendar" class="bg-light-subtle"></div>
            </div>
        </div>
    </div>

    @include('psicologia.adm.partials.modals')
@endsection

@section('scripts')
    @vite(['resources/js/calendar.js'])
@endsection
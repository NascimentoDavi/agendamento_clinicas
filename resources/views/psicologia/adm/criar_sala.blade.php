@extends('layouts.app_adm')

@section('title', 'Criar Sala')

@section('styles')
    <!-- FULL CALENDAR  -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/criar-sala/app.css'])
@endsection

@section('content')

    @if($errors->any())
        <div class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert" style="max-width: 90%;">
            <strong>Ops!</strong> Corrija os itens abaixo:
            <ul class="mb-0 mt-1 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="container mw-100">

        <div class="row">

            <x-page-title>
            </x-page-title>
            
            <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">
                
                <form class="needs-validation" action="{{ route('criarSala-Psicologia') }}" method="POST">

                    @csrf

                    <!-- ID CLINICA -->
                    <input type="hidden" name="ID_CLINICA" value="1">
                    
                    <div class="row g-3">

                        <!-- DESCRIÇÃO DA SALA -->
                        <div class="col-md-6">
                            <label for="nome-sala" class="form-label">Descrição da Sala</label>
                            <input type="text" id="nome-sala" name="DESCRICAO" class="form-control" value="{{ old('DESCRICAO', request('DESCRICAO')) }}" required>
                        </div>

                        <!-- DISCIPLINA -->
                        <div class="col-md-6">
                            <label for="disciplina-sala" class="form-label">Disciplina</label>

                            <select name="DISCIPLINA" id="disciplina-sala" class="form-select">
                                <option value="" selected>Carregando...</option>
                            </select>

                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-2"></i>Salvar Sala</button>
                        </div>
                        
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center mb-4">
                    <h2 class="fs-4 mb-0">Consulta e Edição de Salas</h2>
                </div>

                <!-- CAMPO DE BUSCA POR SALAS CADASTRADAS -->
                <div class="mb-3">
                    <input type="search" id="search-sala" class="form-control" placeholder="Buscar sala por nome..." />
                </div>

                <div class="table-responsive border rounded" style="max-height: 50vh; overflow-y: auto;">

                    <table class="table table-hover table-bordered align-middle mb-0">

                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">

                            <tr>
                                <th>Descrição</th>
                                <th>Disciplina</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>

                        </thead>

                        <tbody id="salas-tbody">
                            <tr><td colspan="4" class="text-center">Carregando...</td></tr>
                        </tbody>

                    </table>

                </div>

            </div>
            
        </div>

    </div>
    
    @include('psicologia.adm.partials.modals_criar_sala')

@endsection

@section('scripts')
    @vite(['resources/js/criar-sala/app.js'])
@endsection
@extends('layouts.app_adm')

@section('title', 'Criar Serviço')

@section('styles')
    <!-- FULL CALENDAR  -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/criar-servico/app.css'])
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
                
                <form class="needs-validation" action="{{ route('criarServico-Psicologia') }}" method="POST" novalidate id="form-criar-servico">
                    @csrf
                    <input type="hidden" name="ID_CLINICA" value="1">
                    
                    <div class="row g-3">

                        <!-- NOME DO SERVIÇO -->
                        <div class="col-md-6">
                            <label for="nome-servico" class="form-label">Nome do Serviço</label>
                            <input type="text" id="nome-servico" name="SERVICO_CLINICA_DESC" class="form-control" value="{{ old('SERVICO_CLINICA_DESC', request('SERVICO_CLINICA_DESC')) }}" required>
                        </div>
                        
                        <!-- DISCIPLINA DO SERVIÇO -->
                        <div class="col-md-6">
                            <label for="disciplina-servico" class="form-label">Disciplina</label>
                            <select name="DISCIPLINA" id="disciplina-servico" class="form-select">
                                <option value="" selected>Carregando...</option>
                            </select>
                        </div>

                        <!-- VALOR DO SERVIÇO -->
                        <div class="col-md-3">
                            <label for="valor-servico" class="form-label">Valor do Serviço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" id="valor-servico" name="VALOR_SERVICO" class="form-control" placeholder="0,00" value="{{ old('VALOR_SERVICO') }}">
                            </div>
                        </div>

                        <!-- RECORRÊNCIA DO SERVIÇO -->
                        <div class="col-md-3">
                            <label for="tempo_recorrencia_meses" class="form-label">Recorrência (meses)</label>
                            <input type="number" min="0" step="1" name="TEMPO_RECORRENCIA_MESES" id="tempo_recorrencia_meses" class="form-control" placeholder="Ex: 6" value="{{ old('TEMPO_RECORRENCIA_MESES') }}">
                        </div>

                        <!-- OBSERVAÇÕES DO SERVIÇO -->
                        <div class="col-12">
                            <label for="observacao-servico" class="form-label">Observações</label>
                            <textarea id="observacao-servico" name="OBSERVACAO" class="form-control">{{ old('OBSERVACAO') }}</textarea>
                        </div>

                        <div class="col-12 text-end">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-2"></i>Salvar Serviço</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center mb-4">
                    <h2 class="fs-4 mb-0">Consulta e Edição de Serviços</h2>
                </div>
                <div class="mb-3">
                    <input type="search" id="search-servico" class="form-control" placeholder="Buscar serviço por nome..." />
                </div>
                <div class="table-responsive border rounded" style="max-height: 45vh; overflow-y: auto;">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Descrição</th>
                                <th>Disciplina</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="servicos-tbody">
                            <tr><td colspan="4" class="text-center">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@include('psicologia.adm.partials.modals_criar_servico')

@endsection

@section('scripts')
    @vite(['resources/js/criar-servico/app.js'])
@endsection
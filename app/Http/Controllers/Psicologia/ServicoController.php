<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaesaClinicaServico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\search;

class ServicoController extends Controller
{
    // CRIAÇÃO DE SERVIÇO
    public function criarServico(Request $request)
    {
        // Ajuste de valor do serviço
        $valorInput = $request->input('VALOR_SERVICO');
        if ($valorInput) {
            $valor = str_replace(['.', ','], ['', '.'], $valorInput);
            $request->merge(['VALOR_SERVICO' => $valor]);
        } else {
            $request->merge(['VALOR_SERVICO' => null]);
        }

        // Ajuste do código interno (null se vazio ou 0)
        if (!$request->filled('COD_INTERNO_SERVICO_CLINICA') || $request->input('COD_INTERNO_SERVICO_CLINICA') == 0) {
            $request->merge(['COD_INTERNO_SERVICO_CLINICA' => null]);
        }

        $validated = $request->validate([
            'ID_CLINICA' => 'required|integer|min:1',
            'SERVICO_CLINICA_DESC' => 'required|string|min:1|max:255',
            'DISCIPLINA' => 'nullable|string|max:50',
            'COD_INTERNO_SERVICO_CLINICA' => 'nullable|integer|min:0',
            'VALOR_SERVICO' => 'nullable|numeric',
            'OBSERVACAO' => 'nullable|string|max:500',
            'TEMPO_RECORRENCIA_MESES' => 'nullable|integer|min:0|max:6',
        ], [
            'ID_CLINICA.required' => 'O ID da clínica é obrigatório.',
            'ID_CLINICA.integer' => 'O ID da clínica deve ser um número inteiro.',
            'ID_CLINICA.min' => 'O ID da clínica deve ser maior ou igual a 1.',

            'SERVICO_CLINICA_DESC.required' => 'Informe o nome do Serviço antes de prosseguir.',
            'SERVICO_CLINICA_DESC.string' => 'O nome do Serviço deve ser um texto.',
            'SERVICO_CLINICA_DESC.min' => 'O nome do Serviço deve ter pelo menos 1 caractere.',
            'SERVICO_CLINICA_DESC.max' => 'O nome do Serviço não pode ter mais de 255 caracteres.',

            'DISCIPLINA.string' => 'A Disciplina deve ser um texto.',
            'DISCIPLINA.max' => 'A Disciplina não pode ter mais de 50 caracteres.',

            'COD_INTERNO_SERVICO_CLINICA.integer' => 'O Código Interno deve ser um número inteiro.',
            'COD_INTERNO_SERVICO_CLINICA.min' => 'O Código Interno deve ser maior ou igual a 0.',

            'VALOR_SERVICO.numeric' => 'O Valor do Serviço deve ser um número válido.',

            'OBSERVACAO.string' => 'A Observação deve ser um texto.',
            'OBSERVACAO.max' => 'A Observação não pode ter mais de 500 caracteres.',

            'TEMPO_RECORRENCIA_MESES.integer' => 'O Tempo de Recorrência deve ser um número inteiro.',
            'TEMPO_RECORRENCIA_MESES.min' => 'O Tempo de Recorrência deve ser maior ou igual a 0.',
            'TEMPO_RECORRENCIA_MESES.max' => 'O Tempo de Recorrência deve ser menor ou igual a 6 meses.',
        ]);

        // Verificação de duplicidade por nome
        $existeNome = FaesaClinicaServico::where('SERVICO_CLINICA_DESC', $validated['SERVICO_CLINICA_DESC'])
            ->where('ID_CLINICA', $validated['ID_CLINICA'])
            ->exists();

        if ($existeNome) {
            return redirect()->back()
                ->withErrors(['Já existe um serviço com este nome nesta clínica.'])
                ->withInput();
        }

        // Verificação de duplicidade por código interno se informado
        if (!is_null($validated['COD_INTERNO_SERVICO_CLINICA'])) {
            $existeCodigo = FaesaClinicaServico::where('COD_INTERNO_SERVICO_CLINICA', $validated['COD_INTERNO_SERVICO_CLINICA'])
                ->where('ID_CLINICA', $validated['ID_CLINICA'])
                ->exists();

            if ($existeCodigo) {
                return redirect()->back()
                    ->withErrors(['Já existe um serviço com este código interno nesta clínica.'])
                    ->withInput();
            }
        }

        FaesaClinicaServico::create($validated);

        return redirect()->back()->with('success', 'Serviço criado com sucesso.');
    }

    // PESQUISA OS SERVIÇOS DISPONÍVEIS
    public function getServicos(Request $request)
    {
        // TIRA OS ESPAÇOS EM BRANCO
        $search = trim($request->query('search', ''));

        $query = FaesaClinicaServico::where('ID_CLINICA', 1);

        if ($search) {
            $query->where('SERVICO_CLINICA_DESC', 'LIKE', "%{$search}%");

            if (is_numeric($search)) {
                $query->orWhere('ID_SERVICO_CLINICA', $search);
            }
        }

        $servicos = $query->orderBy('ID_SERVICO_CLINICA', 'desc')->get();

        // Substituir null ou 0 no código interno por texto customizado
        $servicos->transform(function($item) {
            if (is_null($item->COD_INTERNO_SERVICO_CLINICA) || $item->COD_INTERNO_SERVICO_CLINICA == 0) {
                $item->COD_INTERNO_SERVICO_CLINICA = '--';
            }
            return $item;
        });

        return response()->json($servicos);
    }

    public function getDisciplinaServico(Request $request)
    {
        $matriculasaluno = array_column(session('aluno')[5], 'DISCIPLINA');

        $search = trim($request->query('search', ''));

        $query = FaesaClinicaServico::where('ID_CLINICA', 1)->whereIn('DISCIPLINA', $matriculasaluno);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('DISCIPLINA', 'LIKE', "%{$search}%")
                ->orWhere('SERVICO_CLINICA_DESC', 'LIKE', "%{$search}%");
            });
        }

        $servicos = $query->orderBy('ID_SERVICO_CLINICA', 'desc')->get();

        return $servicos;
    } 

    // RETORNA SERVIÇO PELO NOME
    public function getServicoByName(string|null $request)
    {
        if ($request == null){
            return '';
        }

        $servico = FaesaClinicaServico::where('SERVICO_CLINICA_DESC', $request)->first();
        return $servico->ID_SERVICO_CLINICA;
    }

    public function getServicoById($id)
    {
        $servico = FaesaClinicaServico::find($id);

        if (!$servico) {
            return response()->json(['message' => 'Serviço não encontrado'], 404);
        }

        // Substituir null ou 0 no código interno por texto customizado
        if (is_null($servico->COD_INTERNO_SERVICO_CLINICA) || $servico->COD_INTERNO_SERVICO_CLINICA == 0) {
            $servico->COD_INTERNO_SERVICO_CLINICA = '--';
        }

        return response()->json($servico->SERVICO_CLINICA_DESC);
    }

    // ATUALIZAÇÃO DE SERVIÇO
public function atualizarServico(Request $request, $id)
{
    $input = $request->all();

    if (isset($input['COD_INTERNO_SERVICO_CLINICA'])) {
        $cod = $input['COD_INTERNO_SERVICO_CLINICA'];
        if ($cod === '--' || trim($cod) === '') {
            $input['COD_INTERNO_SERVICO_CLINICA'] = null;
        }
    }
    $request->replace($input);

    $validator = Validator::make($request->all(), [
        'SERVICO_CLINICA_DESC' => 'required|string|max:255',
        'DISCIPLINA' => 'nullable|string|max:50',
        'VALOR_SERVICO' => 'nullable',
        'OBSERVACAO' => 'nullable|string|max:500',
        'TEMPO_RECORRENCIA_MESES' => 'nullable|integer|min:0|max:6',
    ], [
        'SERVICO_CLINICA_DESC.required' => 'Informe o nome do Serviço antes de prosseguir.',
        'SERVICO_CLINICA_DESC.string' => 'O nome do Serviço deve ser um texto.',
        'SERVICO_CLINICA_DESC.max' => 'O nome do Serviço não pode ter mais de 255 caracteres.',
        'DISCIPLINA.string' => 'A Disciplina deve ser um texto.',
        'DISCIPLINA.max' => 'A Disciplina não pode ter mais de 50 caracteres.',
        'VALOR_SERVICO.numeric' => 'O Valor do Serviço deve ser um número válido.',
        'OBSERVACAO.string' => 'A Observação deve ser um texto.',
        'OBSERVACAO.max' => 'A Observação não pode ter mais de 500 caracteres.',
        'TEMPO_RECORRENCIA_MESES.integer' => 'O Tempo de Recorrência deve ser um número inteiro.',
        'TEMPO_RECORRENCIA_MESES.min' => 'O Tempo de Recorrência deve ser maior ou igual a 0.',
        'TEMPO_RECORRENCIA_MESES.max' => 'O Tempo de Recorrência deve ser menor ou igual a 6 meses.',
    ]);


    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors()->first()], 422);
    }

    $validated = $validator->validated();

    if (isset($validated['VALOR_SERVICO'])) {
        $valor = str_replace(',', '.', $validated['VALOR_SERVICO']);
        $validated['VALOR_SERVICO'] = is_numeric($valor) ? (float)$valor : null;
    }

    $servico = FaesaClinicaServico::find($id);
    if (!$servico) {
        return response()->json(['message' => 'Serviço não encontrado'], 404);
    }

    $clinicaId = $servico->ID_CLINICA;

    // Duplicidade nome
    if (FaesaClinicaServico::where('SERVICO_CLINICA_DESC', $validated['SERVICO_CLINICA_DESC'])
        ->where('ID_CLINICA', $clinicaId)
        ->where('ID_SERVICO_CLINICA', '!=', $id)
        ->exists()
    ) {
        return response()->json(['message' => 'Já existe um serviço com este nome nesta clínica.'], 422);
    }


    $servico->update($validated);

    return response()->json(['message' => 'Serviço atualizado com sucesso']);
}

    // DELETAR SERVIÇO
    public function deletarServico($id)
    {
        $servico = FaesaClinicaServico::find($id);

        if (!$servico) {
            return response()->json(['message' => 'Serviço não encontrado.'], 404);
        }

        $temAgendamentos = DB::table('FAESA_CLINICA_AGENDAMENTO')
            ->where('ID_SERVICO', $id)
            ->exists();

        if ($temAgendamentos) {
            return response()->json([
                'message' => 'Não é possível excluir este serviço porque existem agendamentos vinculados a ele. Para excluir, é necessário remover ou atualizar os agendamentos antes.'
            ], 422);
        }

        $servico->delete();

        return response()->json(['message' => 'Serviço excluído com sucesso.']);
    }
}

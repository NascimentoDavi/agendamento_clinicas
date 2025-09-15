<?php

namespace app\Http\Controllers\Psicologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FaesaClinicaAgendamento;
use App\Models\FaesaClinicaServico;
use App\Models\FaesaClinicaSala;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class SalaController extends Controller
{
    public function createSala(Request $request)
    {
        $validatedData = $request->validate([
            'DESCRICAO' => 'required|string|max:255',
            'DISCIPLINA' => 'nullable|string|max:10'
        ], [
            'DESCRICAO.required' => 'A descrição da Sala é obrigatória',
            'DESCRICAO.string' => 'A descrição da sala não pode ser numérica',
            'DESCRICAO.max' => 'A descrição da sala não pode ter mais de 255 caracteres',
            'DISCIPLINA.string' => 'A Disciplina deve conter o código da Disciplina',
        ]);

        // Verifica se já existe uma sala com o mesmo nome e status diferente de 'Excluido'
        $existeSalaAtiva = FaesaClinicaSala::where('DESCRICAO', $validatedData['DESCRICAO'])
            ->where(function ($query) {
                $query->whereNull('SIT_SALA')
                    ->orWhere('SIT_SALA', '<>', 'Excluido');
            })
            ->exists();


        if ($existeSalaAtiva) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['DESCRICAO' => 'Já existe uma sala com essa descrição.']);
        }

        // Se passou na checagem, cria a sala
        $sala = new FaesaClinicaSala();
        $sala->DESCRICAO = $validatedData['DESCRICAO'];
        $sala->DISCIPLINA = $validatedData['DISCIPLINA'] ?? null;
        $sala->SIT_SALA = 'Ativo'; // ou 'S', depende do seu padrão
        $sala->save();

        return redirect()->route('salas_psicologia')->with('success', 'Sala criada com sucesso!');
    }

    public function getSala(Request $request)
    {
        $search = trim($request->query('search', ''));
        $idServico = $request->query('servico');

        $query = FaesaClinicaSala::query()
            ->where('ATIVO', '<>', 'N')
            ->when($search, function ($q) use ($search) {
                $q->where('DESCRICAO', 'like', "%{$search}%");
            });

        if ($idServico) {
            // Recupera disciplina do serviço
            $servico = FaesaClinicaServico::find($idServico);

            if ($servico && $servico->DISCIPLINA) {
                $disciplina = $servico->DISCIPLINA;

                $query->where(function ($q) use ($disciplina) {
                    $q->whereNull('DISCIPLINA') // salas sem disciplina sempre aparecem
                    ->orWhere('DISCIPLINA', $disciplina);
                });
            }
        }

        $salas = $query->select('ID_SALA_CLINICA', 'DESCRICAO')->get();

        return response()->json($salas);
    }

    public function updateSala(Request $request, $id)
    {
        $requestData = $request->json()->all();

        try {
            $validatedData = validator($requestData, [
                'DESCRICAO' => 'required|string|max:255',
                'DISCIPLINA' => 'nullable|string|max:10',
                'ATIVO' => 'required|in:S,N',
            ], [
                'DESCRICAO.required' => 'A descrição da Sala é obrigatória',
                'DESCRICAO.string' => 'A descrição da sala não pode ser numérica',
                'DESCRICAO.max' => 'A descrição da sala não pode ter mais de 255 caracteres',
                'DISCIPLINA.string' => 'A Disciplina deve conter o código da Disciplina',
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        $sala = FaesaClinicaSala::findOrFail($id);

        // Verifica se já existe outra sala com o mesmo nome e que não está excluída
        $existeSalaAtiva = FaesaClinicaSala::where('DESCRICAO', $validatedData['DESCRICAO'])
            ->where(function ($query) {
                $query->whereNull('SIT_SALA')
                    ->orWhere('SIT_SALA', '<>', 'Excluido');
            })
            ->where('ID_SALA_CLINICA', '<>', $id) // ignora a própria sala
            ->exists();

        if ($existeSalaAtiva) {
            return response()->json([
                'success' => false,
                'errors' => ['DESCRICAO' => ['Já existe uma sala com essa descrição.']]
            ], 422);
        }

        // Atualiza a sala
        $sala->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Sala atualizada com sucesso!'
        ]);
    }


    public function deleteSala($id): JsonResponse
    {
        $sala = FaesaClinicaSala::find($id);
        if(!$sala) {
            return response()->json(['message' => 'Sala não foi encontrada'], 404);
        } else if ($sala->ATIVO === 'S') {
            return response()->json(['message' => 'Sala não pôde ser excluída pois ainda está ativa. Desative-a antes de prosseguir com exclusão.'], 422);
        }

        $agendamentos = FaesaClinicaAgendamento::where('ID_SALA', $id)->exists();

        if($agendamentos) {
            return response()->json(['message' => 'Sala possui agendamento(s) vinculados e por isso não pode ser excluída'], 422);
        } else {
            // Nao exclui diretamente, mas muda situacao para excluido, caso seja necessario reverter
            $sala->SIT_SALA = 'Excluido';
            $sala->save();
            return response()->json(['message' => 'Sala excluída com sucesso.'], 200);
        }
    }

    // LISTAGEM DE SALAS
    public function listSalas(Request $request)
    {
        $search = trim($request->input('search', ''));
        $query = FaesaClinicaSala::query();

        $query->where(function ($q) {
            $q->where('SIT_SALA', '<>', 'Excluido')
            ->orWhereNull('SIT_SALA');
        });

        $query->when($search, function ($query, $search) {
            return $query->where('DESCRICAO', 'like', '%' . $search . '%');
        });

        $salas = $query->orderBy('DESCRICAO', 'desc')->get();

        return response()->json($salas);
    }
}

<?php

namespace App\Http\Controllers\Psicologia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FaesaClinicaHorario;

class HorarioController extends Controller
{
    public function getHorario()
    {

    }

    // CRIA HORÁRIO
    public function createHorario(Request $request)
    {
        $request->validate([
            'BLOQUEADO' => 'required|string|max:1|in:S,N',
            'DATA_HORARIO_INICIAL' => 'required|date',
            'DATA_HORARIO_FINAL' => 'required|date|after_or_equal:DATA_HORARIO_INICIAL',
            'HR_HORARIO_INICIAL' => 'required|date_format:H:i',
            'HR_HORARIO_FINAL' => 'required|date_format:H:i|after:HR_HORARIO_INICIAL',
            'DESCRICAO_HORARIO' => 'required|string|max:255',
            'OBSERVACAO' => 'nullable|string|max:500',
            // 'ID_CLINICA' => 'required|integer|exists:faesa_clinica,ID_CLINICA', // se for via formulário
        ], [
            // mensagens de erro
        ]);

        $horario = new FaesaClinicaHorario();
        $horario->USUARIO = session('usuario')->ID_USUARIO_CLINICA;
        $horario->ID_CLINICA = 1;
        $horario->BLOQUEADO = $request->BLOQUEADO;
        $horario->DATA_HORARIO_INICIAL = $request->DATA_HORARIO_INICIAL;
        $horario->DATA_HORARIO_FINAL = $request->DATA_HORARIO_FINAL;
        $horario->HR_HORARIO_INICIAL = $request->HR_HORARIO_INICIAL;
        $horario->HR_HORARIO_FINAL = $request->HR_HORARIO_FINAL;
        $horario->DESCRICAO_HORARIO = $request->DESCRICAO_HORARIO;
        $horario->OBSERVACAO = $request->OBSERVACAO;

        // Verifica se o horário já existe para mesma clínica
        $existingHorario = FaesaClinicaHorario::where('ID_CLINICA', $horario->ID_CLINICA)
            ->where('BLOQUEADO', $request->BLOQUEADO)
            ->where('DATA_HORARIO_INICIAL', $request->DATA_HORARIO_INICIAL)
            ->where('DATA_HORARIO_FINAL', $request->DATA_HORARIO_FINAL)
            ->where('HR_HORARIO_INICIAL', $request->HR_HORARIO_INICIAL)
            ->where('HR_HORARIO_FINAL', $request->HR_HORARIO_FINAL)
            ->first(); 

        if ($existingHorario) {
            return response()->json(['message' => 'Horário já existe para essa clínica!'], 409);
        }

        $horario->save();

        return redirect()->route('criarHorarioView-Psicologia')->with('success', 'Horário criado com sucesso!');
    }

    public function updateHorario(Request $request, $id, FaesaClinicaHorario $horarioModel)
    {   
        $horario = $horarioModel->find($id);

        if (!$horario) {
            return response()->json(['message' => 'Horário não encontrado!'], 404);
        }

        $validatedData = $request->validate([
            'BLOQUEADO' => 'required|string|max:1|in:S,N',
            'DATA_HORARIO_INICIAL' => 'required|date',
            'DATA_HORARIO_FINAL' => 'required|date|after_or_equal:DATA_HORARIO_INICIAL',
            'HR_HORARIO_INICIAL' => 'required|date_format:H:i',
            'HR_HORARIO_FINAL' => 'required|date_format:H:i|after:HR_HORARIO_INICIAL',
            'DESCRICAO_HORARIO' => 'required|string|max:255',
            'OBSERVACAO' => 'nullable|string|max:500',
        ], [
            'BLOQUEADO.required' => 'O tipo de horário é obrigatório.',
            'DATA_HORARIO_INICIAL.required' => 'A data inicial do horário é obrigatória.',
            'DATA_HORARIO_FINAL.required' => 'A data final do horário é obrigatória.',
            'DATA_HORARIO_FINAL.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
            'HR_HORARIO_INICIAL.required' => 'A hora inicial do horário é obrigatória.',
            'HR_HORARIO_FINAL.required' => 'A hora final do horário é obrigatória.',
            'HR_HORARIO_FINAL.after' => 'A hora final deve ser posterior à hora inicial.',
            'DESCRICAO_HORARIO.required' => 'A descrição do horário é obrigatória.',
            'OBSERVACAO.max' => 'A observação não pode ter mais de 500 caracteres.',
        ]);

        // Verifica se o horário já existe com os mesmos dados (exceto o próprio)
        $existingHorario = FaesaClinicaHorario::where('BLOQUEADO', $validatedData['BLOQUEADO'])
            ->where('DATA_HORARIO_INICIAL', $validatedData['DATA_HORARIO_INICIAL'])
            ->where('DATA_HORARIO_FINAL', $validatedData['DATA_HORARIO_FINAL'])
            ->where('HR_HORARIO_INICIAL', $validatedData['HR_HORARIO_INICIAL'])
            ->where('HR_HORARIO_FINAL', $validatedData['HR_HORARIO_FINAL'])
            ->where('ID_HORARIO', '!=', $id)
            ->first();

        if ($existingHorario) {
            return response()->json(['message' => 'Horário já existe!'], 409);
        }

        // ATUALIZA HORÁRIO
        $horario->BLOQUEADO = $validatedData['BLOQUEADO'];
        $horario->DATA_HORARIO_INICIAL = $validatedData['DATA_HORARIO_INICIAL'];
        $horario->DATA_HORARIO_FINAL = $validatedData['DATA_HORARIO_FINAL'];
        $horario->HR_HORARIO_INICIAL = $validatedData['HR_HORARIO_INICIAL'];
        $horario->HR_HORARIO_FINAL = $validatedData['HR_HORARIO_FINAL'];
        $horario->DESCRICAO_HORARIO = $validatedData['DESCRICAO_HORARIO'];
        $horario->OBSERVACAO = $validatedData['OBSERVACAO'] ?? null;

        $horario->save();

        return response()->json(['success' => true, 'message' => 'Horário atualizado com sucesso!'], 200);
    }

    // DELETAR HORÁRIO
    public function deleteHorario($id, FaesaClinicaHorario $horarioModel)
    {
        $horario = $horarioModel->find($id);

        if (!$horario) {
            return response()->json(['message' => 'Horário não encontrado!'], 404);
        }

        $horario->delete();

        return response()->json(['message' => 'Horário deletado com sucesso!']);
    }

    // LISTAR HORÁRIOS
    public function listHorarios(Request $request, FaesaClinicaHorario $horarioModel)
    {
        $search = trim($request->input('search', ''));
        $query = $horarioModel->where('DESCRICAO_HORARIO', 'like', '%' . $search . '%');

        if ($search) {
            $query->where('DESCRICAO_HORARIO', 'like', '%' . $search . '%');
        }

        $horarios = $query->orderBy('CREATED_AT', 'desc')->get();

        return response()->json($horarios);
    }
}

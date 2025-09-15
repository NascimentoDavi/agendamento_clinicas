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
        ], [
            'BLOQUEADO.required' => 'O tipo do horário é obrigatório.',
            'BLOQUEADO.in' => 'O tipo do horário deve ser "S" ou "N".',
            'DATA_HORARIO_INICIAL.required' => 'A data inicial do horário é obrigatória.',
            'DATA_HORARIO_INICIAL.date' => 'A data inicial deve ser uma data válida.',
            'DATA_HORARIO_FINAL.required' => 'A data final do horário é obrigatória.',
            'DATA_HORARIO_FINAL.date' => 'A data final deve ser uma data válida.',
            'DATA_HORARIO_FINAL.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
            'HR_HORARIO_INICIAL.required' => 'A hora inicial do horário é obrigatória.',
            'HR_HORARIO_INICIAL.date_format' => 'A hora inicial deve estar no formato HH:MM.',
            'HR_HORARIO_FINAL.required' => 'A hora final do horário é obrigatória.',
            'HR_HORARIO_FINAL.date_format' => 'A hora final deve estar no formato HH:MM.',
            'HR_HORARIO_FINAL.after' => 'A hora final deve ser posterior à hora inicial.',
            'DESCRICAO_HORARIO.required' => 'A descrição do horário é obrigatória.',
            'DESCRICAO_HORARIO.max' => 'A descrição do horário não pode ter mais de 255 caracteres.',
            'OBSERVACAO.max' => 'A observação não pode ter mais de 500 caracteres.',
        ]);

        // Verifica se já existe horário com mesma descrição
        $existingDescricao = FaesaClinicaHorario::where('ID_CLINICA', 1)
            ->where('DESCRICAO_HORARIO', $request->DESCRICAO_HORARIO)
            ->first();

        if ($existingDescricao) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['DESCRICAO_HORARIO' => 'Já existe um horário com essa descrição!']);
        }

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

        $horario->save();

        return redirect()->route('criarHorarioView-Psicologia')->with('success', 'Horário criado com sucesso!');
    }

    public function updateHorario(Request $request, $id, FaesaClinicaHorario $horarioModel)
    {   
        $horario = $horarioModel->find($id);

        if (!$horario) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Horário não encontrado!'], 404);
            }
            return redirect()->back()->withErrors(['DESCRICAO_HORARIO' => 'Horário não encontrado!']);
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
            'BLOQUEADO.required' => 'O tipo do horário é obrigatório.',
            'BLOQUEADO.string' => 'O tipo do horário deve ser uma string.',
            'BLOQUEADO.max' => 'O tipo do horário deve ter no máximo 1 caractere.',
            'BLOQUEADO.in' => 'O tipo do horário deve ser "S" ou "N".',

            'DATA_HORARIO_INICIAL.required' => 'A data inicial do horário é obrigatória.',
            'DATA_HORARIO_INICIAL.date' => 'A data inicial deve ser uma data válida.',

            'DATA_HORARIO_FINAL.required' => 'A data final do horário é obrigatória.',
            'DATA_HORARIO_FINAL.date' => 'A data final deve ser uma data válida.',
            'DATA_HORARIO_FINAL.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',

            'HR_HORARIO_INICIAL.required' => 'A hora inicial do horário é obrigatória.',
            'HR_HORARIO_INICIAL.date_format' => 'A hora inicial deve estar no formato HH:MM.',

            'HR_HORARIO_FINAL.required' => 'A hora final do horário é obrigatória.',
            'HR_HORARIO_FINAL.date_format' => 'A hora final deve estar no formato HH:MM.',
            'HR_HORARIO_FINAL.after' => 'A hora final deve ser posterior à hora inicial.',

            'DESCRICAO_HORARIO.required' => 'A descrição do horário é obrigatória.',
            'DESCRICAO_HORARIO.string' => 'A descrição do horário deve ser uma string.',
            'DESCRICAO_HORARIO.max' => 'A descrição do horário não pode ter mais de 255 caracteres.',

            'OBSERVACAO.string' => 'A observação deve ser uma string.',
            'OBSERVACAO.max' => 'A observação não pode ter mais de 500 caracteres.',
        ]);

        // Verifica se já existe outro horário com a mesma descrição
        $existingDescricao = FaesaClinicaHorario::where('DESCRICAO_HORARIO', $validatedData['DESCRICAO_HORARIO'])
            ->where('ID_HORARIO', '!=', $id)
            ->first();

        if ($existingDescricao) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Já existe outro horário com essa descrição!'], 422);
            }
            return redirect()->back()->withInput()->withErrors(['DESCRICAO_HORARIO' => 'Já existe outro horário com essa descrição!']);
        }

        // Atualiza horário
        $horario->update($validatedData);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Horário atualizado com sucesso!']);
        }

        return redirect()->back()->with('success', 'Horário atualizado com sucesso!');
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

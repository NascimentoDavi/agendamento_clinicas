<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Psicologia\PacienteService;
use App\Models\FaesaClinicaPaciente;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PacienteController extends Controller
{
    private PacienteService $pacienteService;

    public function __construct(PacienteService $pacienteService)
    {
        $this->pacienteService = $pacienteService;
    }

    // CRIA PACIENTE
    public function createPaciente(Request $request)
    {
        $validatedData = $request->validate([
            'NOME_COMPL_PACIENTE' => 'required|string|max:255',
            'DT_NASC_PACIENTE' => 'nullable|date',
            'CPF_PACIENTE' => 'required|string|max:14|regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/',
            'SEXO_PACIENTE' => 'required|string|in:M,F,O',
            'CEP' => 'required|string|max:20',
            'ENDERECO' => 'required|string|max:255',
            'END_NUM' => 'required|string',
            'COMPLEMENTO' => 'nullable|string|max:255',
            'BAIRRO' => 'required|string',
            'MUNICIPIO' => 'required|string|max:255',
            'UF' => 'required|string|max:2',
            'E_MAIL_PACIENTE' => 'nullable|email|max:255',
            'FONE_PACIENTE' => 'required|string|max:20|regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/',
            'OBSERVACAO' => 'nullable|string',
            'STATUS' => 'required|string|max:50|in:Em espera',
            'NOME_RESPONSAVEL' => 'nullable|string|max:255',
            'CPF_RESPONSAVEL' => 'nullable|string|max:14|regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/',
            'COD_SUS' => 'nullable|string',
        ], [
            'NOME_COMPL_PACIENTE.required' => 'O nome completo do paciente é obrigatório.',
            'NOME_COMPL_PACIENTE.max' => 'O nome completo não pode passar de 255 caracteres.',

            'DT_NASC_PACIENTE.date' => 'A data de nascimento deve ser uma data válida.',
            'DT_NASC_PACIENTE.required' => 'A data de nascimento deve ser preenchida',

            'CPF_PACIENTE.required' => 'O CPF do paciente é obrigatório.',
            'CPF_PACIENTE.regex' => 'O CPF informado não está em um formato válido.',

            'SEXO_PACIENTE.required' => 'O sexo do paciente é obrigatório.',
            'SEXO_PACIENTE.in' => 'O sexo informado é inválido. Use M, F ou O.',

            'CEP.required' => 'O CEP é obrigatório.',
            'CEP.max' => 'O CEP não pode ter mais que 20 caracteres.',

            'ENDERECO.required' => 'O endereço é obrigatório.',
            'ENDERECO.max' => 'O endereço não pode ter mais que 255 caracteres.',

            'END_NUM.required' => 'O número do endereço é obrigatório.',

            'COMPLEMENTO.max' => 'O complemento não pode ter mais que 255 caracteres.',

            'BAIRRO.required' => 'O bairro é obrigatório.',

            'MUNICIPIO.required' => 'O município é obrigatório.',
            'MUNICIPIO.max' => 'O município não pode ter mais que 255 caracteres.',

            'UF.required' => 'O estado (UF) é obrigatório.',
            'UF.max' => 'A UF deve conter 2 caracteres.',

            'E_MAIL_PACIENTE.email' => 'O e-mail informado não é válido.',
            'E_MAIL_PACIENTE.max' => 'O e-mail não pode ter mais que 255 caracteres.',

            'FONE_PACIENTE.required' => 'O telefone do paciente é obrigatório.',
            'FONE_PACIENTE.regex' => 'O telefone deve estar no formato (99) 99999-9999.',

            'OBSERVACAO.string' => 'A observação deve ser um texto.',

            'STATUS.required' => 'O status é obrigatório.',
            'STATUS.in' => 'O status deve ser "Em espera".',

            'NOME_RESPONSAVEL.max' => 'O nome do responsável não pode ter mais que 255 caracteres.',

            'CPF_RESPONSAVEL.regex' => 'O CPF do responsável não está em um formato válido.',
        ]);

        // FORMATA CPF PARA ENVIAR APENAS NÚMEROS PARA SEREM SALVOS NO BANCO
        $validatedData['CPF_PACIENTE'] = str_replace(['-', '.'], '', $validatedData['CPF_PACIENTE']);
        $validatedData['FONE_PACIENTE'] = str_replace(['(', ')', '-', ' '], '', $validatedData['FONE_PACIENTE']);

        $created = $this->pacienteService->createPaciente($validatedData);

        if(!$created) {
            return redirect('/psicologia/criar-paciente')->with('success', 'Paciente criado com sucesso!');
        } else {
            $paciente = $this->pacienteService->getByCPF($validatedData['CPF_PACIENTE']);
            if ($paciente->STATUS == "Inativo") {
                return redirect('/psicologia/criar-paciente')
                ->with('error', 'Paciente com CPF já cadastrado e inativo, por favor reative o paciente')
                ->withInput();
            }
            return redirect('/psicologia/criar-paciente')
            ->with('error', 'Paciente com CPF já cadastrado, por favor verifique os dados.')
            ->withInput();
        }
    }

    // BUSCA PACIENTE
    public function getPaciente(Request $request)
    {
        $filtros = $request->only([
            'search', 'DT_NASC_PACIENTE', 'STATUS', 'SEXO_PACIENTE', 'FONE_PACIENTE'
        ]);

        $pacientes = $this->pacienteService->filtrarPacientes($filtros);

        return response()->json($pacientes);
    }

    public function getPacienteByNameCpf(Request $request)
    {
        $pacientes = $this->pacienteService->filtrarPacientesByNameOrCpf($request->only('search'));
        return response()->json($pacientes);
    }

    public function getPacienteByNameCPFaluno(Request $request)
    {        
        $pacientes = $this->pacienteService->filtrarPacientesByNameOrCpfaluno($request);

        return response()->json($pacientes);
    }

    // EDITA PACIENTE
    public function editarPaciente(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nome'       => 'required|string|max:255',
            'cpf'        => ['required', 'string', 'max:14', Rule::unique('FAESA_CLINICA_PACIENTE', 'CPF_PACIENTE')->ignore($id, 'ID_PACIENTE')],
            'dt_nasc'    => ['required','date','date_format:Y-m-d'],
            'sexo'       => 'required|string|in:M,F,O',
            'endereco'   => 'required|string|max:255',
            'num'        => 'required|string',
            'complemento'=> 'nullable|string|max:255',
            'bairro'     => 'nullable|string|max:255',
            'uf'         => 'required|string|max:2',
            'cep'        => 'required|string|max:10',
            'celular'    => 'required|string|max:20',
            'email'      => 'nullable|email|max:255',
            'municipio'  => 'required|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'cpf_responsavel' => 'nullable|string|max:14',
        ], [
            'nome.required' => 'O nome do Paciente deve ser fornecido',
            'dt_nasc.required' => 'A data de nascimento deve ser fornecida',
            'cpf.required' => 'O CPF do paciente deve ser fornecido',
            'cpf.unique' => 'O CPF do paciente deve ser único, não pode ser igual ao de outro paciente',
            'cep.required' => 'O CEP de paciente deve ser fornecido',
            'celular.required' => 'O número de celular para contato com o paciente deve ser fornecido',
            'num.required' => 'O número do endereço deve ser fornecido',
            'endereco.required' => 'O logradouro do endereço deve ser fornecido',
            'municipio.required' => 'O município do endereço deve ser fornecido',
            'nome_responsavel.max' => 'O nome do responsávle deve ter no máximo 255 caractéres',
            'cpf_responsavel.max' => 'O CPF do responsável deve ter no máximo 14 digitos',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        try {
            $paciente = FaesaClinicaPaciente::findOrFail($id);
            $paciente->NOME_COMPL_PACIENTE = $validatedData['nome'];
            $paciente->CPF_PACIENTE = str_replace(['-', '.'], '', $validatedData['cpf']);
            $paciente->DT_NASC_PACIENTE = $validatedData['dt_nasc'] ?? $paciente->DT_NASC_PACIENTE;
            $paciente->SEXO_PACIENTE = $validatedData['sexo'] ?? $paciente->SEXO_PACIENTE;
            $paciente->ENDERECO = $validatedData['endereco'] ?? $paciente->ENDERECO;
            $paciente->END_NUM = $validatedData['num'] ?? $paciente->END_NUM;
            $paciente->COMPLEMENTO = $validatedData['complemento'] ?? $paciente->COMPLEMENTO;
            $paciente->BAIRRO = $validatedData['bairro'] ?? $paciente->BAIRRO;
            $paciente->UF = $validatedData['uf'] ?? $paciente->UF;
            $paciente->CEP = $validatedData['cep'] ?? $paciente->CEP;
            $paciente->FONE_PACIENTE = $validatedData['celular'] ?? $paciente->FONE_PACIENTE;
            $paciente->E_MAIL_PACIENTE = $validatedData['email'] ?? $paciente->E_MAIL_PACIENTE;
            $paciente->MUNICIPIO = $validatedData['municipio'] ?? $paciente->MUNICIPIO;
            $paciente->NOME_RESPONSAVEL = $validatedData['nome_responsavel'] ?? $paciente->NOME_RESPONSAVEL;
            $paciente->CPF_RESPONSAVEL = $validatedData['cpf_responsavel'] ?? $paciente->CPF_RESPONSAVEL;

            $paciente->save();

            return response()->json([
                'message' => 'Paciente atualizado com sucesso.',
                'paciente' => $paciente
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Paciente não encontrado'], 404);
        }
    }

    // DELETA PACIENTE
    public function deletePaciente($id)
    {
        $statusDelete = $this->pacienteService->deletarPaciente($id);
        if($statusDelete) {
            return response()->json(['message' => 'Registro de paciente excluído com sucesso.'], 200);
        } else {
            return response()->json(['message' => 'Erro ao excluir paciente'], 400);    
        }
    }

    // SETA STATUS "EM ATENDIMENTO"
    public function setEmAtendimento($id)
    {
        try {
            $paciente = $this->pacienteService->setEmAtendimento($id);
            return response()->json(['message' => 'Paciente em atendimento.', 'paciente' => $paciente]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Paciente não encontrado.'], 404);
        }
    }

    public function setAtivo($id)
    {
        try {
            $paciente = $this->pacienteService->setAtivo($id);
            return response()->json(['message' => 'Paciente reativado com sucesso.', 'paciente' => $paciente]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Paciente não encontrado.'], 404);
        }
    }
}

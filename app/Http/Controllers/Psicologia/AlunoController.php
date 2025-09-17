<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AlunoController extends Controller
{
    public function listAlunos(Request $request) {

        $search = $request->query('search');
        $disciplina = $request->query('disciplina');

        $query = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_MATRICULA as m')
            ->join('LYCEUM_BKP_PRODUCAO.dbo.LY_ALUNO as a', 'a.ALUNO', 'm.ALUNO')
            ->join('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p', 'a.NOME_COMPL', 'p.NOME_COMPL')
            ->where('a.CURSO', '2010')
            ->where('m.ANO', '2025')
            ->where('m.SEMESTRE', '2')
            ->where('m.SIT_MATRICULA', 'Matriculado');

        // Filtra por disciplina se houver
        if ($disciplina) {
            $query->where('m.DISCIPLINA', $disciplina);
        }

        // Filtro de busca
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('a.NOME_COMPL', 'like', "%{$search}%")
                ->orWhere('a.ALUNO', 'like', "%{$search}%");
                
            });
        }

        $resultado = $query->distinct()
            ->select('m.ALUNO as ID_ALUNO', 'a.NOME_COMPL', 'p.DT_NASC', 'p.CPF', 'p.SEXO')
            ->get();

        return response()->json($resultado);
    }
}

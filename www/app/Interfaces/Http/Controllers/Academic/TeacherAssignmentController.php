<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\HR\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        // Dados para o Kanban
        $shifts = \App\Domains\Academic\Models\Shift::where('unit_id', $unitId)->get();
        $classes = SchoolClass::with('shift', 'grade')->where('unit_id', $unitId)->get();
        $teachers = Teacher::with(['employee', 'assignments.subject', 'assignments.schoolClass'])
            ->whereHas('employee', function($q) use ($unitId) {
                $q->where('unit_id', $unitId)->where('is_active', true);
            })->get();

        $subjects = Subject::where('unit_id', $unitId)->where('is_active', true)->get();

        $aiService = new \App\Services\Academic\ScheduleAIGenerator($unitId);
        $suggestions = $aiService->getSuggestions();

        // Verificar se existem drafts
        $hasDrafts = \App\Domains\Academic\Models\Schedule::where('unit_id', $unitId)->where('status', 'draft')->exists() || 
                     TeacherAssignment::whereHas('schoolClass', function($q) use ($unitId) { $q->where('unit_id', $unitId); })->where('status', 'draft')->exists();

        return view('academic.assignments.index', compact('classes', 'teachers', 'shifts', 'subjects', 'suggestions', 'hasDrafts'));
    }

    public function generate(Request $request)
    {
        $unitId = session('active_unit_id');
        $aiService = new \App\Services\Academic\ScheduleAIGenerator($unitId);
        
        $teacherId = $request->input('teacher_id'); // Opcional, para alocar só um professor
        
        // Se for geral, limpa draft existente
        if (!$teacherId) {
            $aiService->clearDraft();
        }

        $result = $aiService->generateSchedule($teacherId);

        if (count($result['warnings']) > 0) {
            return back()->with('warning', 'Alocação gerada com ressalvas: ' . implode(" ", $result['warnings']));
        }

        return back()->with('success', 'Rascunho de alocação gerado com sucesso via IA! Revise antes de publicar.');
    }

    public function publish(Request $request)
    {
        $unitId = session('active_unit_id');
        $aiService = new \App\Services\Academic\ScheduleAIGenerator($unitId);
        $aiService->publishSchedule();

        return back()->with('success', 'Grade publicada com sucesso!');
    }

    public function clear(Request $request)
    {
        $unitId = session('active_unit_id');
        $aiService = new \App\Services\Academic\ScheduleAIGenerator($unitId);
        $aiService->clearDraft();

        return back()->with('success', 'Rascunho limpo.');
    }

    public function move(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Drag and Drop salva direto como draft
        TeacherAssignment::create([
            'teacher_id' => $request->teacher_id,
            'school_class_id' => $request->school_class_id,
            'subject_id' => $request->subject_id,
            'assigned_workload' => Subject::find($request->subject_id)->workload ?? 40,
            'status' => 'draft',
        ]);

        return response()->json(['success' => true]);
    }

    public function create()
    {
        $unitId = session('active_unit_id');
        
        $classes = SchoolClass::with(['grade', 'shift'])->where('unit_id', $unitId)->get();
        $subjects = Subject::where('unit_id', $unitId)->where('is_active', true)->get();
        $teachers = Teacher::with('employee')->whereHas('employee', function($q) use ($unitId) {
            $q->where('unit_id', $unitId)->where('is_active', true);
        })->get();

        return view('academic.assignments.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'assigned_workload' => 'required|integer|min:1|max:160',
        ]);

        // Verifica a restrição "unique_assignment" (mesmo professor, mesma turma, mesma matéria)
        $exists = TeacherAssignment::where('teacher_id', $validated['teacher_id'])
            ->where('school_class_id', $validated['school_class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este professor já está alocado para esta disciplina nesta mesma turma.');
        }

        TeacherAssignment::create($validated);

        return redirect()->route('academic.assignments.index')->with('success', 'Professor alocado com sucesso!');
    }

    public function destroy(TeacherAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('academic.assignments.index')->with('success', 'Alocação cancelada com sucesso!');
    }
}
